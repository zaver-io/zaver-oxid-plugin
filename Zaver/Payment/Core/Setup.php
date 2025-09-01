<?php
namespace Zaver\Payment\Core;

use Zaver\SDK\Object\PaymentMethodsRequest;
use Zaver\SDK\Checkout;
use Zaver\Payment\Classes\ZaverConfig;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\DbMetaDataHandler;

/**
 * This class is used to install the zaver module
 *
 */
class Setup
{

  private $oxPayment;

  private $methodsList = array();

  public function __construct() {
    $this->oxPayment = oxNew(Payment::class);

    try {
      if (!empty(ZaverConfig::getApiKey())) {
        $oCheckout = new Checkout(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());
        $oPaymentReq = PaymentMethodsRequest::create();
        $oPaymentRes = $oCheckout->getPaymentMethods($oPaymentReq);
        $this->methodsList = $oPaymentRes["paymentMethods"];
      }
    }
    catch (Exception $e) {
      $this->methodsList = array();
    }
  }

  /**
   * Install tables if not exists
   */
  public function zaver__install() {

    $modul = oxNew(Module::class);
    $modul->load('zaver');

    $this->deleteOldConfigVariables();
    $oDb = DatabaseProvider::getDb();

    if (!$this->isTablesInstalled()) {
      $oDb->execute("CREATE TABLE IF NOT EXISTS `zaver_order_number_reservations` (" .
        "`OXID` CHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ," .
        " UNIQUE (`OXID`)" .
        " ) ENGINE = MYISAM ;");
    }

    $aPaymentNotInstalled = $this->isPaymentInstalled();
    if (!empty($aPaymentNotInstalled)) {
      $this->installPayments($aPaymentNotInstalled);
    }

    $this->updateZaverPaymentNames();

    // Insert the zaver status, transaction id, payment status and payment id column
    $this->addzaverStatusColumnInOrderTable();
    $this->addzaverTransactionIdColumnInOrderTable();
    $this->addZaverPaymentIdColumnInOrderTable();
    $this->addZaverPaymentStatusColumnInOrderTable();
  }

  protected function updateZaverPaymentNames() {

    $oDb = DatabaseProvider::getDb();
    $pluginCodeTxt = ZaverConfig::PLUGIN_CODE_TXT;

    foreach ($this->methodsList as $pm) {
      $aLocalization = $pm["localizations"];

      $name_de = $aLocalization["de-DE"]["title"];
      $name_en = $aLocalization["en-EN"]["title"];
      $name = ZaverConfig::PLUGIN_PREFIX . $pm["paymentMethod"];

      $update = "UPDATE oxpayments SET OXDESC = " . $oDb->quote($pluginCodeTxt . $name_de) . ", OXDESC_1 = " . $oDb->quote($pluginCodeTxt . $name_en) . " WHERE OXID = " . $oDb->quote($name);
      $oDb->execute($update);
    }
  }

  protected function deleteOldConfigVariables() {

    $shopConfig = Registry::getConfig();
    $sShopId = $shopConfig->getBaseShopId();

    $oDb = DatabaseProvider::getDb();

    //Delete existent settings
    $sSql = "DELETE FROM `oxconfig` WHERE `oxshopid`  = " . $oDb->quote($sShopId) . " AND `oxmodule` = " . $oDb->quote('module:zaver') . " ";
    $oDb->execute($sSql);

    //Delete existent settings
    $sSql = "DELETE FROM `oxconfigdisplay` WHERE `oxcfgmodule` = " . $oDb->quote('module:zaver') . " ";

    $oDb->execute($sSql);
  }

  //Checks if tables created.
  protected function isTablesInstalled() {

    $zaver_order_number_reservations = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne('CHECK TABLE `zaver_order_number_reservations`');
    return $zaver_order_number_reservations['Msg_text'] === "OK";
  }


  //Insert rows of payment methods are created in oxPayments table
  protected function installPayments($p_aPaymentNotInstalled) {
    $oDb = DatabaseProvider::getDb();
    $pluginCodeTxt = ZaverConfig::PLUGIN_CODE_TXT;

    foreach ($this->methodsList as $pm) {
      $paymentName = ZaverConfig::PLUGIN_PREFIX . $pm["paymentMethod"];
      $aLocalization = $pm["localizations"];

      if (!empty($p_aPaymentNotInstalled) && in_array($paymentName, $p_aPaymentNotInstalled)) {

        $name_de = $aLocalization["de-DE"]["title"];
        $name_en = $aLocalization["en-EN"]["title"];
        $name = $paymentName;
        $desc_de = $aLocalization["de-DE"]["description"];
        $desc_en = $aLocalization["en-EN"]["description"];

        $insert = "INSERT INTO oxpayments (OXID, OXACTIVE, OXDESC, OXADDSUM, OXADDSUMTYPE, OXADDSUMRULES, OXFROMBONI, OXFROMAMOUNT, " .
          "OXTOAMOUNT, OXVALDESC, OXCHECKED, OXDESC_1, OXVALDESC_1, OXDESC_2, OXVALDESC_2, OXDESC_3, OXVALDESC_3, OXLONGDESC, " .
          "OXLONGDESC_1, OXLONGDESC_2, OXLONGDESC_3, OXSORT) VALUES(" .
          $oDb->quote($name) . ", 1, " . $oDb->quote($pluginCodeTxt . $name_de) . ", 0, 'abs', 15, 0, 0, 1000000, '', 0, " .
          $oDb->quote($pluginCodeTxt . $name_en) . ", '', '', '', '', '', '$desc_de', '$desc_en', '', '', 0)";
        $oDb->execute($insert);
      }
    }
  }

  //Check if rows of payment methods are created in oxPayments table
  protected function isPaymentInstalled() {
    $aPaymentNotInstalled = array();
    foreach ($this->methodsList as $pm) {
      $paymentName = ZaverConfig::PLUGIN_PREFIX . $pm["paymentMethod"];
      $this->oxPayment->oxpayments__oxid->value = null;
      $this->oxPayment->load($paymentName);

      $isPMNotInstalled = is_null($this->oxPayment->oxpayments__oxid->value);
      if ($isPMNotInstalled == true) {
        $aPaymentNotInstalled[] = $paymentName;
      }
    }
    return $aPaymentNotInstalled;
  }

  /**
   * Are blocks set in oxtplblocks table
   * @return bool
   */
  protected function areBlocksSet() {
    $zaver__select_payment = $this->queryForBlock('zaver__select_payment');
    $zaver__payment_errors = $this->queryForBlock('zaver__payment_errors');
    return $zaver__select_payment && $zaver__payment_errors;
  }

  /**
   * Db query for oxBlockname specified, returns true if block in table
   * oxtplblocks present
   * @param  string $oxBlockname
   * @return bool
   */
  protected function queryForBlock($oxBlockname) {
    $config = Registry::getConfig();
    $shopId = $config->getShopId();
    $oDb = DatabaseProvider::getDb();
    $result = $oDb->getOne(
      "SELECT 1 FROM oxtplblocks"
      . " WHERE oxmodule = '" . ZaverConfig::PLUGIN_CODE . "'"
      . " AND oxshopid = " . $oDb->quote($shopId)
      . " AND oxid = " . $oDb->quote($oxBlockname)
      . " LIMIT 1"
    );

    return (bool)$result;
  }

  /**
   * Update new zaver blocks if missing
   */
  protected function updateBlocks() {
    if (!$this->queryForBlock('zaver__select_payment')) {
      $this->insertBlock(
        'zaver__select_payment', 'select_payment', 'page/checkout/payment.tpl', 'out/blocks/page/checkout/payment/select_payment.tpl'
      );
    }

    if (!$this->queryForBlock('zaver__payment_errors')) {
      $this->insertBlock(
        'zaver__payment_errors', 'checkout_payment_errors', 'page/checkout/payment.tpl', 'out/blocks/page/checkout/payment/checkout_payment_errors.tpl'
      );
    }
  }

  /**
   * Insert entry for zaver to oxtplblocks table
   * @param  string $oxId
   * @param  string $oxBlockname
   * @param  string $oxTemplate
   * @param  string $oxFile
   */
  protected function insertBlock($oxId, $oxBlockname, $oxTemplate, $oxFile) {
    $oDb = DatabaseProvider::getDb();
    $config = Registry::getConfig();
    $shopId = $config->getShopId();
    $sql = "INSERT INTO `oxtplblocks` (
                    `OXID`, `OXACTIVE`, `OXSHOPID`, `OXTEMPLATE`,
                    `OXBLOCKNAME`, `OXPOS`, `OXFILE`, `OXMODULE`
                ) VALUES (
                    " . $oDb->quote($oxId) . ",
                    1,
                    " . $oDb->quote($shopId) . ",
                    " . $oDb->quote($oxTemplate) . ",
                    " . $oDb->quote($oxBlockname) . ",
                    '1',
                    " . $oDb->quote($oxFile) . ", '" . ZaverConfig::PLUGIN_CODE . "'
                )
        ";

    // @TODO add exception handling
    $oDb->execute($sql);
  }

  function addZaverStatusColumnInOrderTable() {

    $bColumExist = false;

    $sTable = 'oxorder';
    $sColumn = 'zaver__status';
    $oDbHandler = oxNew(DbMetaDataHandler::class);
    $bColumExist = $oDbHandler->fieldExists($sColumn, $sTable);

    if ($bColumExist == false || $bColumExist == null || $bColumExist == 0) {
      $sql = "ALTER TABLE `oxorder` ADD `zaver__status` smallint NULL";
      DatabaseProvider::getDb()->execute($sql);

      $oDbHandler->updateViews();
    }
  }

  function addZaverTransactionIdColumnInOrderTable() {

    $bColumExist = false;

    $sTable = 'oxorder';
    $sColumn = 'zaver__transaction_id';
    $oDbHandler = oxNew(DbMetaDataHandler::class);
    $bColumExist = $oDbHandler->fieldExists($sColumn, $sTable);
    if ($bColumExist == false || $bColumExist == null || $bColumExist == 0) {
      $sql = "ALTER TABLE `oxorder` ADD `zaver__transaction_id` varchar(32) NULL";
      DatabaseProvider::getDb()->execute($sql);

      $oDbHandler->updateViews();
    }
  }

  function addZaverPaymentIdColumnInOrderTable() {

    $bColumExist = false;

    $sTable = 'oxorder';
    $sColumn = 'zaver__payment_id';
    $oDbHandler = oxNew(DbMetaDataHandler::class);
    $bColumExist = $oDbHandler->fieldExists($sColumn, $sTable);

    if ($bColumExist == false || $bColumExist == null || $bColumExist == 0) {
      $sql = "ALTER TABLE `oxorder` ADD `zaver__payment_id` varchar(100) NULL";
      DatabaseProvider::getDb()->execute($sql);

      $oDbHandler->updateViews();
    }
  }

  function addZaverPaymentStatusColumnInOrderTable() {

    $bColumExist = false;

    $sTable = 'oxorder';
    $sColumn = 'zaver__payment_status';
    $oDbHandler = oxNew(DbMetaDataHandler::class);
    $bColumExist = $oDbHandler->fieldExists($sColumn, $sTable);

    if ($bColumExist == false || $bColumExist == null || $bColumExist == 0) {
      $sql = "ALTER TABLE `oxorder` ADD `zaver__payment_status` varchar(50) NULL";
      DatabaseProvider::getDb()->execute($sql);

      $oDbHandler->updateViews();
    }
  }
}