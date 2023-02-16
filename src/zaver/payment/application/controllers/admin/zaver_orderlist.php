<?php
use Zaver\SDK\Checkout;
use Zaver\SDK\Object\PaymentUpdateRequest;
use Zaver\SDK\Config\PaymentStatus;

class zaver_orderlist extends zaver_orderlist_parent
{
  protected $_oOrder;

  /**
   * Cancel the order
   */
  public function storno()
  {
    error_log("storno()");

    $result = $this->cancelZaverOrder();

    if ($result) {
      parent::storno();
    }
  }

  /**
   * Delete the order
   */
  public function deleteEntry()
  {
    error_log("deleteEntry()");
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
  protected function cancelZaverOrder()
  {
    $this->_oOrder = oxNew("oxorder");
    $oConfig = $this->getConfig();
    $sOxid = $oConfig->getRequestParameter("oxid");

    if ($sOxid != "-1" && isset($sOxid)) {
      // load object
      $this->_oOrder->load($sOxid);
    }

    if ($this->isZaverOrder()) {
      $paymentId = $this->_oOrder->oxorder__zaver__payment_id->value;
      error_log("cancelZaverOrder() paymentId:$paymentId");

      try {
        $oPaymentUpReq = PaymentUpdateRequest::create()
          ->setPaymentStatus(PaymentStatus::CANCELLED);

        $oCheckout = new Checkout(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());
        $zvStatusPmRes = $oCheckout->getPaymentStatus($paymentId);
        $zvStatusPm = $zvStatusPmRes->getPaymentStatus();
        error_log("captureZaverPayment() getPaymentStatus():$zvStatusPm");

        if ($zvStatusPm != PaymentStatus::SETTLED && $zvStatusPm != PaymentStatus::CANCELLED) {
          $oPaymentUpRes = $oCheckout->updatePayment($paymentId, $oPaymentUpReq);
          error_log("oPaymentUpRes:" . print_r($oPaymentUpRes, true));
        }
      } catch (Exception $e) {
        //oxRegistry::get('oxUtilsServer')->addErrorToDisplay($e);
        $_POST['oxid'] = -1;
        $this->resetContentCache();
        $this->init();
        error_log("ERROR:".$e->getMessage());
        return false;
      }
    }

    return true;
  }
}
