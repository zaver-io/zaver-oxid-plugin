<?php
use Zaver\SDK\Checkout;
use Zaver\SDK\Object\PaymentCaptureRequest;
use Zaver\SDK\Object\PaymentCaptureResponse;
use Zaver\SDK\Config\PaymentStatus;

class zaver_orderoverview extends zaver_orderoverview_parent
{
  protected $_oOrder;

  /**
   * Sends order.
   */
  public function sendorder() {
    error_log("sendorder()");

    $result = $this->captureZaverPayment();

    if ($result) {
      parent::sendorder();
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

    if (stripos($oOrder->oxorder__oxpaymenttype, ZaverConfig::PLUGIN_PREFIX) !== false) {
      $blActive = true;
    }

    return $blActive;
  }

  /**
   * Capture the Zaver payment
   *
   * @return bool
   */
  protected function captureZaverPayment() {
    $this->_oOrder = oxNew("oxorder");
    $oConfig = $this->getConfig();
    $sOxid = $oConfig->getRequestParameter("oxid");

    if ($sOxid != "-1" && isset($sOxid)) {
      // load object
      $this->_oOrder->load($sOxid);
    }
    error_log("captureZaverPayment() ZaverConfig::getAutomaticCapture():".ZaverConfig::getAutomaticCapture());

    if ($this->isZaverOrder()) {
      try {
        if (ZaverConfig::getAutomaticCapture()) {
          $paymentId = $this->_oOrder->oxorder__zaver__payment_id->value;
          error_log("captureZaverPayment() paymentId:$paymentId");
          error_log("captureZaverPayment() oxorder__oxtotalordersum:".$this->_oOrder->oxorder__oxtotalordersum->value);
          error_log("captureZaverPayment() oxorder__oxcurrency:".$this->_oOrder->oxorder__oxcurrency->value);

          $dAmount = number_format($this->_oOrder->oxorder__oxtotalordersum->value, 2, '.', '');
          $oPaymentCapReq = PaymentCaptureRequest::create()
            ->setCurrency($this->_oOrder->oxorder__oxcurrency->value)
            ->setAmount($dAmount);
          $oCheckout = new Checkout(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());
          $zvStatusPmRes = $oCheckout->getPaymentStatus($paymentId);
          $zvStatusPm = $zvStatusPmRes->getPaymentStatus();
          error_log("captureZaverPayment() getPaymentStatus():$zvStatusPm");

          if ($zvStatusPm != PaymentStatus::SETTLED && $zvStatusPm != PaymentStatus::CANCELLED) {
            $oCheckout->capturePayment($paymentId, $oPaymentCapReq);
          }
        }
      }
      catch (Exception $e) {
        //oxRegistry::get('oxUtilsServer')->addErrorToDisplay($e);
        $_POST['oxid'] = -1;
        $this->resetContentCache();
        $this->init();
        error_log("ERROR:" . $e->getMessage());
        return false;
      }
    }

    return true;
  }
}
