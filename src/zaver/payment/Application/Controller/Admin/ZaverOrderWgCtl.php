<?php

namespace Zaver\Payment\Application\Controller\Admin;

use Zaver\SDK\Object\WidgetRequest;
use Zaver\SDK\Object\WidgetResponse;
use Zaver\SDK\Manage;
use Zaver\Payment\Classes\ZaverConfig;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsServer;
use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;

/**
 * Configure the zaver widget
 */
class ZaverOrderWgCtl extends AdminDetailsController
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

    $this->_oOrder = oxNew(Order::class);
    $sOxid = Registry::getConfig()->getRequestParameter('oxid');

    if ($sOxid != "-1" && isset($sOxid)) {
      // load object
      $this->_oOrder->load($sOxid);
      $this->_aViewData["edit"] = $this->_oOrder;
    }
    $oLang = Registry::getLang();

    if (!$this->isZaverOrder()) {
      $this->_aViewData["sMessage"] = $oLang->translateString("ZAVER_ONLY_FOR_ZAVER_PAYMENT");
    }
    else {
      try {
        $this->_aViewData["Widget"] = '';
        $lang = strtoupper($oLang->getLanguageAbbr());
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
    $oUtilsServer = oxNew(UtilsServer::class);
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
