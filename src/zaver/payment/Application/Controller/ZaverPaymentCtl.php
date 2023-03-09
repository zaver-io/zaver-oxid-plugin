<?php

namespace Zaver\Payment\Application\Controller;

use Zaver\SDK\Object\PaymentMethodsRequest;
use Zaver\SDK\Checkout;
use Zaver\Payment\Classes\ZaverConfig;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class ZaverPaymentCtl
 */
class ZaverPaymentCtl extends ZaverPaymentCtl_parent
{

  public $_zaver_payment_error;
  public $_zaver_payments;
  public $_zaver_logo;

  /**
   * Return the error message to display.
   *
   * @return string
   *
   */
  public function getZaverPaymentError() {
    if (Registry::getSession()->hasVariable('_zaver_payment_error')) {
      $this->_zaver_payment_error = Registry::getSession()->getVariable('_zaver_payment_error');
      Registry::getSession()->deleteVariable('_zaver_payment_error');
    }

    return $this->_zaver_payment_error;
  }

  /**
   * Check if the error message is from zaver.
   *
   * @return boolean
   *
   */
  public function isZaverPaymentError() {
    $bIsError = false;

    if (!empty($this->_zaver_payment_error) || Registry::getSession()->hasVariable('_zaver_payment_error')) {
      $bIsError = true;
    }

    return $bIsError;
  }

  /**
   * Validate the payment input data from customer.
   *
   * @return mixed
   *
   */
  public function validatePayment() {
    $paymentId = Registry::getConfig()->getRequestParameter('paymentid');
    $parentResult = parent::validatePayment();
    $result['msg'] = '';

    if (substr($paymentId, 0, 3) === ZaverConfig::PLUGIN_PREFIX) {
      if (ZaverConfig::getHostUrl() == '' ||
        ZaverConfig::getApiKey() == ''
      ) {
        $result['msg'] = Registry::getLang()->translateString("ZV_PAYMENT_SETTINGS_EMPTY");
      }
    }

    if (!empty($result['msg'])) {
      $this->_sPaymentError = 'zaver';
      $this->_zaver_payment_error = $result['msg'];

      return;
    }

    $result['msg'] = '';

    return $parentResult;
  }

  /**
   * Return active currency name.
   *
   * @return string
   *
   */
  public function getActiveCurrencyName() {
    $oCur = $this->getConfig()->getActShopCurrencyObject();
    $sCur = $oCur->name;

    return $sCur;
  }

  /**
   * Check if credentials are set in config for Zaver payment methods.
   *
   * @param string $paymentId The payment id
   *
   * @return boolean
   *
   */
  public function isSettingsSet($paymentId) {
    $settingsAreSet = true;
    $bisPayment = false;

    // Get the current language
    $lang = strtoupper(Registry::getLang()->getLanguageAbbr());

    $oBasket = Registry::getSession()->getBasket();

    // Get the amount
    $dAmount = $oBasket->getPrice()->getBruttoPrice();

    // Get the currency
    $oCur = $oBasket->getBasketCurrency();
    $sCur = $oCur->name;
    $this->_zaver_logo = '';

    if (substr($paymentId, 0, 3) === ZaverConfig::PLUGIN_PREFIX) {
      if (ZaverConfig::getHostUrl() == '' ||
        ZaverConfig::getApiKey() == ''
      ) {
        $settingsAreSet = false;
      }

      try {
        $oCheckout = new Checkout(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());
        $oPaymentReq = PaymentMethodsRequest::create()
          ->setCurrency($sCur)
          ->setAmount($dAmount);
        $oPaymentRes = $oCheckout->getPaymentMethods($oPaymentReq);
        $this->_zaver_payments = $oPaymentRes["paymentMethods"];
      }
      catch (Exception $e) {
        $this->_zaver_payments = array();
      }

      if (count($this->_zaver_payments) > 0) {
        foreach ($this->_zaver_payments as $method) {
          $methodCode = ZaverConfig::PLUGIN_PREFIX . $method["paymentMethod"];

          if ($methodCode == $paymentId) {
            $bisPayment = true;
            $this->_zaver_logo = $method["iconSvgSrc"];
          }
        }
      }

      if (!$bisPayment) {
        $settingsAreSet = false;
      }
    }

    return $settingsAreSet;
  }

  /**
   * Return the logo to display for the payment method.
   *
   * @param string $paymentId The payment id
   *
   * @return string
   *
   */
  public function getPaymentLogo($paymentId) {
    return $this->_zaver_logo;
  }
}