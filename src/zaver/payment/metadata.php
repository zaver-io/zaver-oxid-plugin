<?php
/*/**
 * Copyright © Zaver B2B. All rights reserved.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

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
  'version' => '1.0.2',
  'thumbnail' => 'out/img/Logo.png',
  'author' => 'Zaver payments',
  'email' => 'integration@zaver.se',
  'url' => 'https://zaver.se',
  'extend' => array(
    \OxidEsales\Eshop\Application\Controller\OrderController::class => Zaver\Payment\Application\Controller\ZaverOrderCtrl::class,
    \OxidEsales\Eshop\Application\Controller\PaymentController::class => Zaver\Payment\Application\Controller\ZaverPaymentCtl::class,
    \OxidEsales\Eshop\Application\Controller\Admin\OrderOverview::class => Zaver\Payment\Application\Controller\Admin\ZaverOrderOverview::class,
    \OxidEsales\Eshop\Application\Controller\Admin\OrderList::class => Zaver\Payment\Application\Controller\Admin\ZaverOrderList::class,
    \OxidEsales\Eshop\Application\Model\Order::class => Zaver\Payment\Application\Model\ZaverOrder::class,
    \OxidEsales\Eshop\Application\Model\Basket::class => Zaver\Payment\Application\Model\ZaverBasket::class,
  ),
  'controllers' => array(
    'zaverconfigcontroller' => Zaver\Payment\Application\Controller\Admin\ZaverConfigCtl::class,
    'zaverorderwgcontroller' => Zaver\Payment\Application\Controller\Admin\ZaverOrderWgCtl::class,
  ),
  'blocks' => array(
    array('template' => 'page/checkout/payment.tpl',
      'block' => 'select_payment',
      'file' => 'Application/views/azure/blocks/page/checkout/payment/payment.tpl'
    ),
    array('template' => 'page/checkout/payment.tpl',
      'block' => 'checkout_payment_errors',
      'file' => 'Application/views/azure/blocks/page/checkout/payment/checkout_payment_errors.tpl'
    ),
    array('template' => 'page/checkout/order.tpl',
      'block' => 'checkout_order_main',
      'file' => 'Application/views/azure/blocks/page/checkout/payment/checkout_order_main.tpl'
    ),
    array('template' => 'page/checkout/payment.tpl',
      'block' => 'mb_select_payment',
      'file' => 'Application/views/mobile/blocks/page/checkout/payment/payment.tpl'
    ),
    array('template' => 'page/checkout/payment.tpl',
      'block' => 'mb_select_payment_dropdown',
      'file' => 'Application/views/mobile/blocks/page/checkout/payment/mb_select_payment_dropdown.tpl'
    ),
    array('template' => 'page/checkout/order.tpl',
      'block' => 'checkout_order_main',
      'file' => 'Application/views/mobile/blocks/page/checkout/payment/checkout_order_main.tpl'
    ),
    array('template' => 'order_overview.tpl',
      'block' => 'admin_order_overview_checkout',
      'file' => 'Application/views/admin/blocks/zaver_orderoverview.tpl'
    ),
  ),
  'templates' => array(
    'zaver_payment.tpl' => 'zaver/payment/Application/views/azure/tpl/page/checkout/inc/zaver_payment.tpl',
    'mb_zaver_payment.tpl' => 'zaver/payment/Application/views/mobile/tpl/page/checkout/inc/zaver_payment.tpl',
    'zaver_config.tpl' => 'zaver/payment/Application/views/admin/tpl/zaver_config.tpl',
    'zaver_orderwg.tpl' => 'zaver/payment/Application/views/admin/tpl/zaver_orderwg.tpl',
  ),
  'settings' => [
    [
      'group' => 'ZAVER_GENERAL',
      'name' => 'ZaverOxid',
      'type' => 'str',
      'value' => ''
    ]
  ],
  'events' => array(
    'onActivate' => 'Zaver\Payment\Core\Events::onActivate',
    'onDeactivate' => 'Zaver\Payment\Core\Events::onDeactivate'
  )
);
