<?php
use Zaver\SDK\Object\WidgetRequest;
use Zaver\SDK\Object\WidgetResponse;
use Zaver\SDK\Manage;

/**
 * Configure the zaver widget
 */
class zaver_orderwg extends zaver_admindetails
{
  /** @var string|null */
  private $errorMessage = null;

  /**
   * class template.
   * @var string
   */
  protected $_sThisTemplate = 'zaver_orderwg.tpl';

  protected $_oOrder;

  /**
   * Get the widget to show in zaver tab
   * @extend render
   * @return string
   */
  public function render() {
    parent::render();

    $this->_oOrder = oxNew('oxorder');

    $oConfig = $this->getConfig();

    $sOxid = $oConfig->getRequestParameter("oxid");

    if ($sOxid != "-1" && isset($sOxid)) {
      // load object
      $this->_oOrder->load($sOxid);
      $this->_aViewData["edit"] = $this->_oOrder;
    }

    if (!$this->isZaverOrder()) {
      $this->_aViewData["sMessage"] = oxRegistry::getLang()->translateString("ZAVER_ONLY_FOR_ZAVER_PAYMENT");
    }
    else {
      try {
        $this->_aViewData["Widget"] = '';
        $lang = strtoupper(oxRegistry::getLang()->getLanguageAbbr());
        $oOrder = $this->_oOrder;

        $oWidget = WidgetRequest::create()
          ->setClientIp($this->getRemoteAddress())
          ->setLanguage($lang)
          ->setPaymentId($oOrder->oxorder__zaver__payment_id->value);

        $oManage = new Manage(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());
        $oWidgetRes = $oManage->getWidget($oWidget);
        $this->_aViewData["Widget"] = $oWidgetRes->getWidgetUrl();
      }
      catch (Exception $e) {
        error_log("ERROR:" . $e->getMessage());
      }
    }

    return $this->_sThisTemplate;
  }

  /**
   * Get the client IP Address
   *
   * @return string The IP Address
   */
  protected function getRemoteAddress() {
    $oUtilsServer = oxRegistry::get('oxUtilsServer');
    $sIpAddress = $oUtilsServer->getRemoteAddress();

    return $sIpAddress;
  }

  /**
   * Checks is order was made with Zaver module
   *
   * @return bool
   */
  public function isZaverOrder() {
    $blActive = false;
    $oOrder = $this->_oOrder;

    if (stripos($oOrder->oxorder__oxpaymenttype->value, ZaverConfig::PLUGIN_PREFIX) !== false) {
      $blActive = true;
    }

    return $blActive;
  }
}
