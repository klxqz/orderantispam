<?php

class shopOrderantispamPlugin extends shopPlugin {

    public static $plugin_id = array('shop', 'orderantispam');

    public function backendOrder($order) {
        if ($this->getSettings('status') && $this->getSettings('action_id') && $order['state_id'] == 'new') {
            $action_id = $this->getSettings('action_id');
            $workflow = new shopWorkflow();
            $config = $workflow::getConfig();

            if (!empty($config['actions'][$action_id]['state'])) {
                $state = $config['actions'][$action_id]['state'];
                if ($order['state_id'] != $state) {
                    $customer_model = new shopCustomerModel();
                    $customer = $customer_model->getById($order['contact_id']);
                    $template_path = wa()->getAppPath('plugins/orderantispam/templates/BackendOrder.html', 'shop');
                    $view = wa()->getView();
                    $view->assign('order', $order);
                    $view->assign('customer', $customer);
                    $view->assign('action_id', $action_id);
                    $html = $view->fetch($template_path);
                    return array('action_button' => $html);
                }
            }
        }
    }

    public function frontendHead() {
        if ($this->getSettings('status') && !waRequest::cookie('uuid')) {
            wa()->getResponse()->setCookie('uuid', self::uuid(), time() + 365 * 86400, null, '', false, true);
        }
    }

    private static function uuid() {
        $uuid = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), // 16 bits for "time_mid"
                mt_rand(0, 0xffff), // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000, // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000, // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        return $uuid;
    }

    public function orderActionCreate($params) {
        if ($this->getSettings('status') && waRequest::cookie('uuid')) {
            $customer_model = new shopCustomerModel();

            $is_spamer = $customer_model->getByField(array('uuid' => waRequest::cookie('uuid'), 'is_spamer' => 1));
            $update = array();
            if ($is_spamer && $this->getSettings('action_id')) {
                $update['is_spamer'] = 1;
                $action_id = $this->getSettings('action_id');
                $workflow = new shopWorkflow();
                $action = $workflow->getActionById($action_id);
                $action->run($params['order_id']);
            }
            $customer = $customer_model->getById($params['contact_id']);

            if (!$customer['uuid']) {
                $update['uuid'] = waRequest::cookie('uuid');
                $customer_model->updateById($params['contact_id'], $update);
            }
        }
    }

}
