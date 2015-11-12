<?php

return array(
    'name' => 'Анти-спам заказов',
    'description' => 'Перемещает заказы от спамящих клиентов в отдельную группу',
    'vendor' => 985310,
    'version' => '1.0.0',
    'img' => 'img/orderantispam.png',
    'shop_settings' => true,
    'frontend' => true,
    'handlers' => array(
        'backend_order' => 'backendOrder',
        'frontend_head' => 'frontendHead',
        'order_action.create' => 'orderActionCreate',
    ),
);
//EOF
