<?php

namespace Zaver\Payment\Application\Controller\Admin;

use Zaver\SDK\Checkout;
use Zaver\SDK\Object\PaymentUpdateRequest;
use Zaver\SDK\Config\PaymentStatus;
use Zaver\Payment\Classes\ZaverConfig;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;

class ZaverOrderList extends ZaverOrderList_parent
{
  protected $_oOrder;

  /**
   * Cancel the order
   */
  public function storno() {
    $result = $this->cancelZaverOrder();

    if ($result) {
      parent::storno();
      $this->_oOrder->oxorder__oxtransstatus = new Field(ZaverConfig::ORDER_CANCELED);
      $this->_oOrder->save();
    }
  }

  /**
   * Delete the order
   */
  public function deleteEntry() {
    $result = $this->cancelZaverOrder();

    if ($result) {
      parent::deleteEntry();
    }
  }

  /**
   * Checks is order was made with Zaver module
   *
   * @return bool
   */
  protected function isZaverOrder() {
    $blActive = false;
    $oOrder = $this->_oOrder;

    if (stripos($oOrder->oxorder__oxpaymenttype->value, ZaverConfig::PLUGIN_PREFIX) !== false) {
      $blActive = true;
    }

    return $blActive;
  }

  /**
   * Send status from cancel order
   *
   * @return bool
   */
  protected function cancelZaverOrder() {
    $this->_oOrder = oxNew(Order::class);
    $sOxid = Registry::getConfig()->getRequestParameter('oxid');

    if ($sOxid != "-1" && isset($sOxid)) {
      // load object
      $this->_oOrder->load($sOxid);
    }

    if ($this->isZaverOrder()) {
      $paymentId = $this->_oOrder->oxorder__zaver__payment_id->value;

      try {
        $oPaymentUpReq = PaymentUpdateRequest::create()
          ->setPaymentStatus(PaymentStatus::CANCELLED);

        $oCheckout = new Checkout(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());
        $zvStatusPmRes = $oCheckout->getPaymentStatus($paymentId);
        $zvStatusPm = $zvStatusPmRes->getPaymentStatus();

        if ($zvStatusPm != PaymentStatus::SETTLED && $zvStatusPm != PaymentStatus::CANCELLED) {
          $oPaymentUpRes = $oCheckout->updatePayment($paymentId, $oPaymentUpReq);
        }
      }
      catch (Exception $e) {
        //Registry::get(UtilsView::class)->addErrorToDisplay($e);
        $_POST['oxid'] = -1;
        $this->resetContentCache();
        $this->init();
        return false;
      }
    }

    return true;
  }
}
