<?php

$model = new waModel();

try {
    $model->query("SELECT `is_spamer` FROM `shop_customer` WHERE 0");
    $model->exec("ALTER TABLE `shop_customer` DROP `is_spamer`");
} catch (waDbException $e) {
    
}

try {
    $model->query("SELECT `uuid` FROM `shop_customer` WHERE 0");
    $model->exec("ALTER TABLE `shop_customer` DROP `uuid`");
} catch (waDbException $e) {
    
}