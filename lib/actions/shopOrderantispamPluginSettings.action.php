<?php

class shopOrderantispamPluginSettingsAction extends waViewAction {

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        $settings = $app_settings_model->get(shopOrderantispamPlugin::$plugin_id);
        $workflow = new shopWorkflow();
        $actions = $workflow->getAvailableActions();
        $this->view->assign('actions', $actions);
        $this->view->assign('settings', $settings);
    }

}
