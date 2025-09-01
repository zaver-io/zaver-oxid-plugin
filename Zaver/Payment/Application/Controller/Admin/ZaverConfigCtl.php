<?php

namespace Zaver\Payment\Application\Controller\Admin;

use Zaver\SDK\Object\PaymentMethodsRequest;
use Zaver\SDK\Checkout;
use Zaver\Payment\Classes\ZaverConfig;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration;
use OxidEsales\Eshop\Application\Model\Payment;

/**
 * Configure the Zaver interface
 */
class ZaverConfigCtl extends ShopConfiguration
{
  /** @var string|null */
  private $errorMessage = null;

  /**
   * class template.
   * @var string
   */
  protected $_sThisTemplate = 'zaver_config.tpl';

  protected $_parameters = [];

  /**
   * Passes shop configuration parameters
   * @extend render
   * @return string
   */
  public function render() {
    $this->_aViewData['zaver_config'] = ZaverConfig::get(ZaverConfig::VAR_CONFIG);
    $this->_aViewData['zaver_error'] = $this->getMerchantConfigErrorId();
    $this->_aViewData['zaver_error_message'] = $this->errorMessage;

    if (file_exists(ZaverConfig::getLogFilename())) {
      $this->_aViewData['log_filename'] = substr(ZaverConfig::getLogFilename(), strlen($_SERVER['DOCUMENT_ROOT']));
    }

    return $this->_sThisTemplate;
  }

  /**
   * Saves shop configuration parameters.
   *
   * @return void
   */
  public function save() {
    $oxConfig = Registry::getConfig();

    if (empty($this->_parameters)) {
      $this->_parameters = $oxConfig->getRequestParameter(ZaverConfig::VAR_CONFIG);
    }

    if (!empty($this->_parameters[ZaverConfig::KEY_CALLBACK_TOKEN]) &&
      !empty($this->_parameters[ZaverConfig::KEY_API_KEY]) &&
      !empty($this->_parameters[ZaverConfig::KEY_HOST_URL])
    ) {
      $oxConfig->saveShopConfVar('arr', ZaverConfig::VAR_CONFIG, $this->_parameters);
    }
  }

  /**
   * @throws oxConnectionException
   *
   * @return void
   */
  public function synchronize() {
    try {
      $oCheckout = new Checkout(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());
      $oPaymentReq = PaymentMethodsRequest::create();
      $oPaymentRes = $oCheckout->getPaymentMethods($oPaymentReq);
      $methods = $oPaymentRes["paymentMethods"];

      if (count($methods) > 0) {
        $prefix = ZaverConfig::PLUGIN_PREFIX;
        DatabaseProvider::getDb()->execute(sprintf("DELETE FROM `oxobject2payment` where `oxpaymentid` LIKE '%%%s%%'", $prefix));
        DatabaseProvider::getDb()->execute(sprintf("DELETE FROM `oxpayments` where `oxid` LIKE '%%%s%%'", $prefix));

        $locales = $this->getLangList();
        $pluginCodeTxt = ZaverConfig::PLUGIN_CODE_TXT;
        $oPayment = oxNew(Payment::class);

        foreach ($methods as $method) {
          $methodCode = $prefix . $method["paymentMethod"];
          $oPayment->load($methodCode);
          $oPayment->setEnableMultilang(false);
          $oPayment->setId($methodCode);
          $oPayment->oxpayments__oxid = new Field($methodCode, Field::T_RAW);

          $aLocalization = $method["localizations"];

          foreach ($locales as $locale => $lang) {
            $key = $locale . "-" . strtoupper($locale);
            $oPayment->{'oxpayments__oxdesc' . $lang} = new Field($pluginCodeTxt . $aLocalization[$key]["title"], Field::T_RAW);
            $oPayment->{'oxpayments__oxlongdesc' . $lang} = new Field(strip_tags($aLocalization[$key]["description"]), Field::T_RAW);
          }

          $oPayment->oxpayments__oxactive = new Field(1, Field::T_RAW);
          $oPayment->oxpayments__oxaddsum = new Field(0, Field::T_RAW);
          $oPayment->oxpayments__oxaddsumtype = new Field('abs', Field::T_RAW);
          $oPayment->oxpayments__oxaddsumrules = new Field('15', Field::T_RAW);
          $oPayment->oxpayments__oxfromboni = new Field('0', Field::T_RAW);
          $oPayment->oxpayments__oxtoamount = new Field('1000000', Field::T_RAW);
          $oPayment->oxpayments__oxchecked = new Field(0, Field::T_RAW);
          $oPayment->oxpayments__oxsort = new Field('0', Field::T_RAW);
          $oPayment->oxpayments__oxtspaymentid = new Field('', Field::T_RAW);
          $oPayment->save();

          // Assign the payment to country DE
          $sOxId = $oPayment->oxpayments__oxid->value;
          $countryModel = oxNew('oxCountry');
          $countryId = $countryModel->getIdByCode('DE');

          if ($countryId) {
            $oObject2Payment = oxNew('oxbase');
            $oObject2Payment->init('oxobject2payment');
            $oObject2Payment->oxobject2payment__oxpaymentid = new Field($sOxId);
            $oObject2Payment->oxobject2payment__oxobjectid = new Field($countryId);
            $oObject2Payment->oxobject2payment__oxtype = new Field("oxcountry");
            $oObject2Payment->save();
          }
        }
      }
      else {
        $this->errorMessage = '';
      }
    }
    catch (Exception $exception) {
      $this->errorMessage = $exception->getMessage();
      return;
    }
  }

  /**
   * @return array
   */
  private function getLangList() {
    $result = [];
    $obLang = Registry::getLang();
    $aLang = $obLang->getLanguageArray();

    foreach ($aLang as $oLang) {
      $result[$oLang->abbr] = $oLang->id ? '_' . $oLang->id : '';
    }

    return $result;
  }

  /**
   * Display Error
   *
   * @see ./../../views/admin/tpl/zaver_config.tpl
   *
   * @param null
   * @return boolean
   */
  public function getMerchantConfigErrorId() {
    $request = $_POST;
    if ($request['fnc'] == 'synchronize') {
      if ($this->errorMessage) {
        return 4;
      }
      return 3;
    }
    elseif ($request['fnc'] == 'save') {
      if (empty($this->_parameters[ZaverConfig::KEY_CALLBACK_TOKEN]) ||
        empty($this->_parameters[ZaverConfig::KEY_API_KEY]) ||
        empty($this->_parameters[ZaverConfig::KEY_HOST_URL])
      ) {
        return 5;
      }
      return 2;
    }

    return 1;
  }
}
