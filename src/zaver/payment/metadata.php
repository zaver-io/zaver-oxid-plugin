<?php
/**
 * Copyright © Zaver B2B. All rights reserved.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
$aModule = array(
  'id' => 'zaver',
  'title' => 'Zaver payments',
  'description' => [
    'de' => 'Module to integrate all payment methods from Zaver payments.<br>
             Please continue with the configuration of the payment methods for Zaver in the special “Zaver” menu item.”',
    'en' => 'Module to integrate all payment methods from Zaver payments.<br>
             Please continue with the configuration of the payment methods for Zaver in the special “Zaver” menu item.”'
  ],
  'version' => '1.0.1',
  'thumbnail' => 'out/img/Logo.png',
  'author' => 'Zaver payments',
  'email' => 'integration@zaver.se',
  'url' => 'https://zaver.se',
  'extend' => array(
    'payment' => 'zaver/payment/application/controllers/zaver_payment',
    'order' => 'zaver/payment/application/controllers/zaver_order',
    'oxorder' => 'zaver/payment/application/models/zaver_oxorder',
    'oxbasket' => 'zaver/payment/application/models/zaver_oxbasket',
    'order_list' => 'zaver/payment/application/controllers/admin/zaver_orderlist',
    'order_overview' => 'zaver/payment/application/controllers/admin/zaver_orderoverview',
  ),
  'files' => array(
    'zaver_setup' => 'zaver/payment/core/zaver_setup.php',
    'zaver_events' => 'zaver/payment/core/zaver_events.php',
    'zaver_config' => 'zaver/payment/application/controllers/admin/zaver_config.php',
    'zaver_order_number_reservation' => 'zaver/payment/application/models/zaver_order_number_reservation.php',
    'ZaverConfig' => 'zaver/payment/classes/ZaverConfig.php',
    'ZaverLogger' => 'zaver/payment/classes/ZaverLogger.php',
    'ZaverApi' => 'zaver/payment/classes/ZaverApi.php',
    'zaver_orderwg' => 'zaver/payment/application/controllers/admin/zaver_orderwg.php',
    'zaver_admindetails' => 'zaver/payment/application/controllers/admin/zaver_admindetails.php',
  ),
  'blocks' => array(
    array('template' => 'page/checkout/payment.tpl',
      'block' => 'select_payment',
      'file' => 'application/views/azure/blocks/page/checkout/payment/payment.tpl'
    ),
    array('template' => 'page/checkout/payment.tpl',
      'block' => 'checkout_payment_errors',
      'file' => 'application/views/azure/blocks/page/checkout/payment/checkout_payment_errors.tpl'
    ),
    array('template' => 'page/checkout/order.tpl',
      'block' => 'checkout_order_main',
      'file' => 'application/views/azure/blocks/page/checkout/payment/checkout_order_main.tpl'
    ),
    array('template' => 'page/checkout/payment.tpl',
      'block' => 'mb_select_payment',
      'file' => 'application/views/mobile/blocks/page/checkout/payment/payment.tpl'
    ),
    array('template' => 'page/checkout/payment.tpl',
      'block' => 'mb_select_payment_dropdown',
      'file' => 'application/views/mobile/blocks/page/checkout/payment/mb_select_payment_dropdown.tpl'
    ),
    array('template' => 'page/checkout/order.tpl',
      'block' => 'checkout_order_main',
      'file' => 'application/views/mobile/blocks/page/checkout/payment/checkout_order_main.tpl'
    ),
    array('template' => 'order_overview.tpl',
      'block' => 'admin_order_overview_checkout',
      'file' => 'application/views/admin/blocks/zaver_orderoverview.tpl'
    ),
  ),
  'templates' => array(
    'zaver_payment.tpl' => 'zaver/payment/application/views/azure/tpl/page/checkout/inc/zaver_payment.tpl',
    'mb_zaver_payment.tpl' => 'zaver/payment/application/views/mobile/tpl/page/checkout/inc/zaver_payment.tpl',
    'zaver_config.tpl' => 'zaver/payment/application/views/admin/tpl/zaver_config.tpl',
    'zaver_orderwg.tpl' => 'zaver/payment/application/views/admin/tpl/zaver_orderwg.tpl',
  ),
  'settings' => [
    [
      'name' => 'Zaver Oxid',
      'type' => 'str',
      'value' => 'Please configure via the separate Zaver menu item'
    ]
  ],
  'events' => array(
    'onActivate' => 'zaver_events::onActivate',
    'onDeactivate' => 'zaver_events::onDeactivate'
  ),
);
