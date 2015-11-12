<?php

$plugin_id = array('shop', 'orderantispam');
$app_settings_model = new waAppSettingsModel();
$app_settings_model->set($plugin_id, 'status', '1');

$model = new waModel();
try {
    $sql = 'SELECT `is_spamer` FROM `shop_customer` WHERE 0';
    $model->query($sql);
} catch (waDbException $ex) {
    $sql = "ALTER TABLE `shop_customer` ADD `is_spamer` TINYINT( 1 ) NOT NULL ";
    $model->query($sql);
}

try {
    $sql = 'SELECT `uuid` FROM `shop_customer` WHERE 0';
    $model->query($sql);
} catch (waDbException $ex) {
    $sql = "ALTER TABLE `shop_customer` ADD `uuid` VARCHAR( 36 ) NOT NULL , ADD INDEX ( `uuid` )";
    $model->query($sql);
}

