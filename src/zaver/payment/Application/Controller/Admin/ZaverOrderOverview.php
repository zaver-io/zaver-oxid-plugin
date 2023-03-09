<?php

namespace Zaver\Payment\Application\Controller\Admin;

use Zaver\SDK\Checkout;
use Zaver\SDK\Object\PaymentCaptureRequest;
use Zaver\SDK\Object\PaymentCaptureResponse;
use Zaver\SDK\Config\PaymentStatus;
use Zaver\Payment\Classes\ZaverConfig;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;

class ZaverOrderOverview extends ZaverOrderOverview_parent
{
  protected $_oOrder;

  /**
   * Sends order.
   */
  public function sendorder() {
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
    $this->_oOrder = oxNew(Order::class);
    $sOxid = Registry::getConfig()->getRequestParameter('oxid');

    if ($sOxid != "-1" && isset($sOxid)) {
      // load object
      $this->_oOrder->load($sOxid);
    }

    if ($this->isZaverOrder()) {
      try {
        if (ZaverConfig::getAutomaticCapture()) {
          $paymentId = $this->_oOrder->oxorder__zaver__payment_id->value;
          $dAmount = number_format($this->_oOrder->oxorder__oxtotalordersum->value, 2, '.', '');
          $oPaymentCapReq = PaymentCaptureRequest::create()
            ->setCurrency($this->_oOrder->oxorder__oxcurrency->value)
            ->setAmount($dAmount);
          $oCheckout = new Checkout(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());
          $zvStatusPmRes = $oCheckout->getPaymentStatus($paymentId);
          $zvStatusPm = $zvStatusPmRes->getPaymentStatus();

          if ($zvStatusPm != PaymentStatus::SETTLED && $zvStatusPm != PaymentStatus::CANCELLED) {
            $oCheckout->capturePayment($paymentId, $oPaymentCapReq);
          }
        }
      }
      catch (Exception $e) {
        //Registry::get('oxUtilsServer')->addErrorToDisplay($e);
        $_POST['oxid'] = -1;
        $this->resetContentCache();
        $this->init();
        error_log("ERROR:" . $e->getMessage());
        return false;
      }
    }

    return true;
  }

  /**
   * Returns name of template to render
   *
   * @return string
   */
  public function render() {
    $sTemplate = parent::render();
    $this->_aViewData['isZaverOrder'] = false;

    $this->_oOrder = oxNew(Order::class);
    $sOxid = Registry::getConfig()->getRequestParameter('oxid');

    if ($sOxid != "-1" && isset($sOxid)) {
      // load object
      $this->_oOrder->load($sOxid);
    }

    if ($this->isZaverOrder()) {
      $this->_aViewData['isZaverOrder'] = true;
      $this->_aViewData['zaverPaymentId'] = $this->_oOrder->oxorder__zaver__payment_id->value;
    }

    return $sTemplate;
  }

}
