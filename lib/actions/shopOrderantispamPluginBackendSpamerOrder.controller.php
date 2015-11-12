<?php

class shopOrderantispamPluginBackendSpamerOrderController extends waJsonController {

    public function execute() {
        $order_id = waRequest::post('order_id', null, waRequest::TYPE_INT);
        if ($order_id) {
            $order_model = new shopOrderModel();
            $order = $order_model->getOrder($order_id);
            $customer_model = new shopCustomerModel();
            $customer = $customer_model->getById($order['contact_id']);

            $customer_model->updateById($order['contact_id'], array('is_spamer' => 1));

            $plugin = waSystem::getInstance()->getPlugin('orderantispam');
            
            $action_id = $plugin->getSettings('action_id');
            $workflow = new shopWorkflow();
            $action = $workflow->getActionById($action_id);
            $action->run($order_id);

            // counters

            $state_counters = $order_model->getStateCounters();
            $pending_counters = (!empty($state_counters['new']) ? $state_counters['new'] : 0) +
                    (!empty($state_counters['processing']) ? $state_counters['processing'] : 0) +
                    (!empty($state_counters['paid']) ? $state_counters['paid'] : 0);

            // update app coutner
            wa('shop')->getConfig()->setCount($state_counters['new']);

            $script = "<script>";
            $script .= "$.order_list.updateCounters(" . json_encode(array(
                        'state_counters' => $state_counters,
                        'common_counters' => array(
                            'pending_counters' => $pending_counters
                        )
                    )) . ");";
            $script .= "$.order.reload();</script>";

            $this->response['script'] = $script;
        }
    }

}
