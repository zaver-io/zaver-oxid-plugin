<?php
use Zaver\SDK\Object\PaymentMethodsRequest;
use Zaver\SDK\Checkout;

/**
 * This class is used to install the zaver module
 *
 */
class zaver_setup extends oxAdminView
{

  private $oxPayment;

  private $methodsList = array();

  public function __construct() {
    $this->oxPayment = oxNew("oxpayment");

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

    $modul = oxNew('oxModule');
    $modul->load('zaver');

    $this->deleteOldConfigVariables();

    if (!$this->isTablesInstalled()) {
      oxDb::getDb()->Execute("CREATE TABLE IF NOT EXISTS `zaver_order_number_reservations` (" .
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

    $db = oxDb::getDb();
    $pluginCodeTxt = ZaverConfig::PLUGIN_CODE_TXT;

    foreach ($this->methodsList as $pm) {
      $aLocalization = $pm["localizations"];

      $name_de = $aLocalization["de-DE"]["title"];
      $name_en = $aLocalization["en-EN"]["title"];
      $name = ZaverConfig::PLUGIN_PREFIX . $pm["paymentMethod"];

      $update = "UPDATE oxpayments SET OXDESC = " . $db->quote($pluginCodeTxt . $name_de) . ", OXDESC_1 = " . $db->quote($pluginCodeTxt . $name_en) . " WHERE OXID = " . $db->quote($name);
      $db->Execute($update);
    }
  }

  protected function deleteOldConfigVariables() {

    $shopConfig = oxRegistry::getConfig();
    $sShopId = $shopConfig->getBaseShopId();

    $oDb = oxDb::getDb();

    //Delete existent settings
    $sSql = "DELETE FROM `oxconfig` WHERE `oxshopid`  = " . $oDb->quote($sShopId) . " AND `oxmodule` = " . $oDb->quote('module:zaver') . " ";
    $oDb->Execute($sSql);

    //Delete existent settings
    $sSql = "DELETE FROM `oxconfigdisplay` WHERE `oxcfgmodule` = " . $oDb->quote('module:zaver') . " ";

    $oDb->Execute($sSql);
  }

  //Checks if tables created.
  protected function isTablesInstalled() {

    $zaver_order_number_reservations = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne('CHECK TABLE `zaver_order_number_reservations`');
    return $zaver_order_number_reservations['Msg_text'] === "OK";
  }


  //Insert rows of payment methods are created in oxPayments table
  protected function installPayments($p_aPaymentNotInstalled) {
    $db = oxDb::getDb();
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
          $db->quote($name) . ", 1, " . $db->quote($pluginCodeTxt . $name_de) . ", 0, 'abs', 15, 0, 0, 1000000, '', 0, " .
          $db->quote($pluginCodeTxt . $name_en) . ", '', '', '', '', '', '$desc_de', '$desc_en', '', '', 0)";
        $db->Execute($insert);
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
    $shopId = $this->getConfig()->getShopId();
    $db = oxDb::getDb();
    $result = $db->getOne(
      "SELECT 1 FROM oxtplblocks"
      . " WHERE oxmodule = '" . ZaverConfig::PLUGIN_CODE . "'"
      . " AND oxshopid = " . $db->quote($shopId)
      . " AND oxid = " . $db->quote($oxBlockname)
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
    $db = oxDb::getDb();
    $shopId = $this->getConfig()->getShopId();
    $sql = "INSERT INTO `oxtplblocks` (
                    `OXID`, `OXACTIVE`, `OXSHOPID`, `OXTEMPLATE`,
                    `OXBLOCKNAME`, `OXPOS`, `OXFILE`, `OXMODULE`
                ) VALUES (
                    " . $db->quote($oxId) . ",
                    1,
                    " . $db->quote($shopId) . ",
                    " . $db->quote($oxTemplate) . ",
                    " . $db->quote($oxBlockname) . ",
                    '1',
                    " . $db->quote($oxFile) . ", '" . ZaverConfig::PLUGIN_CODE . "'
                )
        ";

    // @TODO add exception handling
    $db->execute($sql);
  }

  function addZaverStatusColumnInOrderTable() {

    $bColumExist = false;

    $sTable = 'oxorder';
    $sColumn = 'zaver__status';
    $oDbHandler = oxnew('oxDbMetaDataHandler');
    $bColumExist = $oDbHandler->fieldExists($sColumn, $sTable);

    if ($bColumExist == false || $bColumExist == null || $bColumExist == 0) {
      $sql = "ALTER TABLE `oxorder` ADD `zaver__status` smallint NULL";
      oxDb::getDb()->Execute($sql);

      $oDbHandler->updateViews();
    }
  }

  function addZaverTransactionIdColumnInOrderTable() {

    $bColumExist = false;

    $sTable = 'oxorder';
    $sColumn = 'zaver__transaction_id';
    $oDbHandler = oxnew('oxDbMetaDataHandler');
    $bColumExist = $oDbHandler->fieldExists($sColumn, $sTable);
    if ($bColumExist == false || $bColumExist == null || $bColumExist == 0) {
      $sql = "ALTER TABLE `oxorder` ADD `zaver__transaction_id` varchar(32) NULL";
      oxDb::getDb()->Execute($sql);

      $oDbHandler->updateViews();
    }
  }

  function addZaverPaymentIdColumnInOrderTable() {

    $bColumExist = false;

    $sTable = 'oxorder';
    $sColumn = 'zaver__payment_id';
    $oDbHandler = oxnew('oxDbMetaDataHandler');
    $bColumExist = $oDbHandler->fieldExists($sColumn, $sTable);

    if ($bColumExist == false || $bColumExist == null || $bColumExist == 0) {
      $sql = "ALTER TABLE `oxorder` ADD `zaver__payment_id` varchar(100) NULL";
      oxDb::getDb()->Execute($sql);

      $oDbHandler->updateViews();
    }
  }

  function addZaverPaymentStatusColumnInOrderTable() {

    $bColumExist = false;

    $sTable = 'oxorder';
    $sColumn = 'zaver__payment_status';
    $oDbHandler = oxnew('oxDbMetaDataHandler');
    $bColumExist = $oDbHandler->fieldExists($sColumn, $sTable);

    if ($bColumExist == false || $bColumExist == null || $bColumExist == 0) {
      $sql = "ALTER TABLE `oxorder` ADD `zaver__payment_status` varchar(50) NULL";
      oxDb::getDb()->Execute($sql);

      $oDbHandler->updateViews();
    }
  }
}