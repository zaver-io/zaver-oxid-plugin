<?php
use Zaver\SDK\Checkout;
use Zaver\SDK\Object\MerchantUrls;
use Zaver\SDK\Object\PaymentCreationRequest;
use Zaver\SDK\Object\LineItem;
use Zaver\SDK\Config\ItemType;
use Zaver\SDK\Object\PayerData;
use Zaver\SDK\Config\PaymentStatus;
use Zaver\SDK\Object\Address;

/**
 * Class zaver_order
 */
class zaver_order extends zaver_order_parent
{
  /**
   * @param int $iSuccess
   *
   * @return string
   */
  protected function _getNextStep($iSuccess) {
    //Get current language
    $sActualPayment = $this->getPayment()->oxpayments__oxid->value;
    $oOrder = oxNew('oxorder');
    $sess_challenge = oxRegistry::getSession()->getVariable("sess_challenge");
    error_log("PAYMENT: $sActualPayment, iSuccess:$iSuccess");

    if ($oOrder->load($sess_challenge)) {
      if (is_numeric($iSuccess) && $iSuccess >= 1) {
        if ($iSuccess === oxOrder::ORDER_STATE_ORDEREXISTS && !$oOrder->zaver__isPaymentDone()) {
          list($oOrder, $iSuccess) = $this->zaver__recreateOrder($oOrder);
        }
        $oBasket = $this->getBasket();
        //Amount
        $dAmount = $oBasket->getPrice()->getBruttoPrice();
        //Get currency
        $oCur = $oBasket->getBasketCurrency();
        $sCur = $oCur->name;

        $iOrderId = $oOrder->oxorder__oxid->value; //orderid (char 32)

        $lang = oxRegistry::getLang()->getLanguageAbbr(); //lang

        //check supported languages.
        $supportedLangs = array(
          "de",
          "en",
          "es",
          "fr",
          "it",
          "ja",
          "pt",
          "nl",
          "cs",
          "sv",
          "da",
          "pl",
          "spde",
          "spen",
          "de_DE_stadtn"
        );

        if (!in_array($lang, $supportedLangs)) {
          $lang = "en";
        }

        // generate transaction id to identify the transaction on notify and redirect
        $transactionId = oxUtilsObject::getInstance()->generateUID();

        $sStoken = $this->getSession()->getSessionChallengeToken();
        $sRtoken = $this->getSession()->getRemoteAccessToken(true);
        $urlRedirect = $this->getConfig()->getSslShopUrl() . 'index.php?cl=order&fnc=processZaverRedirect&pm='
          . $this->getPayment()->getId() . '&sess_challenge=' . oxRegistry::getSession()->getVariable('sess_challenge')
          . '&' . $this->getSession()->getName() . '=' . $this->getSession()->getId() . '&stoken=' . $sStoken
          . '&rtoken=' . $sRtoken;
        $urlNotify = $this->getConfig()->getSslShopUrl() . 'index.php?cl=order&fnc=processZaverNotify&pm='
          . $this->getPayment()->getId() . '&sess_challenge=' . oxRegistry::getSession()->getVariable('sess_challenge')
          . '&' . $this->getSession()->getName() . '=' . $this->getSession()->getId() . '&stoken=' . $sStoken
          . '&rtoken=' . $sRtoken;


        $oUser = $this->getUser();
        $iUserId = $oUser->oxuser__oxid->value; //customer id (char 32)
        $iUserNr = $oUser->oxuser__oxcustnr->value;

        // Check if is a zaver payment
        if (substr($this->getPayment()->getId(), 0, 3) === ZaverConfig::PLUGIN_PREFIX) {
          // change order status
          $oOrder->oxorder__oxtransstatus = new oxField(ZaverConfig::ORDER_IN_PAYMENT);
          $oOrder->oxorder__zaver__transaction_id = new oxField($transactionId);
          $oOrder->setPayment($oBasket->getPaymentId());
          $oOrder->save();

          try {
            //Sends request to Zaver.
            error_log("Sends request to Zaver...");
            $api = new Checkout(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());

            $aBasketContents = $this->getBasket()->getContents();
            $aItems = array();

            if (!empty($aBasketContents)) {
              foreach ($aBasketContents as $oBasketItem) {
                $sEAN = "";
                $oArticle = $oBasketItem->getArticle();
                $sEAN = $oArticle->oxarticles__oxean->value;
                $sName = strlen($oBasketItem->getTitle()) > 100 ? substr($oBasketItem->getTitle(), 0, 90) . '...' : $oBasketItem->getTitle();
                $iPrice = $oBasketItem->getUnitPrice()->getBruttoPrice();
                $iQty = $oBasketItem->getAmount();

                $iPriceNetto = $oBasketItem->getUnitPrice()->getNettoPrice();
                $iVatRate = $oBasketItem->getUnitPrice()->getVat();

                if ($oArticle->oxarticles__oxisdownloadable->value == 1) {
                  $itemType = ItemType::DIGITAL;
                }
                else {
                  $itemType = ItemType::PHYSICAL;
                }

                $iPriceTax = $iPrice - $iPriceNetto;
                error_log("iPrice:$iPrice, iPriceNetto:$iPriceNetto, iPriceTax:$iPriceTax, iVatRate:$iVatRate");

                $item = LineItem::create()
                  ->setName($sName)
                  ->setMerchantReference($sEAN)
                  ->setQuantity($iQty)
                  ->setUnitPrice($iPrice)
                  ->setTotalAmount($iQty * $iPrice)
                  ->setTaxRatePercent($iVatRate)
                  ->setTaxAmount($iPriceTax)
                  ->setItemType($itemType);

                $aItems[] = $item;
              }
            }

            $shippingAmount = $oOrder->getOrderDeliveryPrice()->getBruttoPrice();
            $shippingName = $oOrder->getDelSet()->oxdeliveryset__oxtitle->value;
            $shippingVatRate = $oOrder->oxorder__oxdelvat->value;
            $shippingVatAmount = $oOrder->getOrderDeliveryPrice()->getVatValue();

            $shipping = LineItem::create()
              ->setName($shippingName)
              ->setQuantity(1)
              ->setUnitPrice($shippingAmount)
              ->setTotalAmount($shippingAmount)
              ->setTaxRatePercent($shippingVatRate)
              ->setTaxAmount($shippingVatAmount)
              ->setItemType(ItemType::SHIPPING);

            $urls = MerchantUrls::create()
              ->setSuccessUrl($urlRedirect)
              ->setCancelUrl($urlRedirect)
              ->setCallbackUrl($urlNotify);

            $payer = PayerData::create()
              ->setEmail($oOrder->oxorder__oxbillemail->value);

            if (!empty($oOrder->oxorder__oxdellname->value)) {
              $shippAdrName = $oOrder->oxorder__oxdelfname->value . ' ' . $oOrder->oxorder__oxdellname->value;
              $oCountry = oxNew("oxcountry");
              $oCountry->load($oOrder->oxorder__oxdelcountryid->value);
              $shippCountryIso = $oCountry->oxcountry__oxisoalpha2->value;

              $shippAdress = Address::create()
                ->setName($shippAdrName)
                ->setPostalCode($oOrder->oxorder__oxdelzip->value)
                ->setStreetName($oOrder->oxorder__oxdelstreet->value)
                ->setHouseNumber($oOrder->oxorder__oxdelstreetnr->value)
                ->setCity($oOrder->oxorder__oxdelcity->value)
                ->setCountry($shippCountryIso);

              $payer->setShippingAddress($shippAdress);
            }

            if ($oOrder->oxorder__oxbilllname->value) {
              $billAdrName = $oOrder->oxorder__oxbillfname->value . ' ' . $oOrder->oxorder__oxbilllname->value;
              $oCountry = oxNew("oxcountry");
              $oCountry->load($oOrder->oxorder__oxbillcountryid->value);
              $billCountryIso = $oCountry->oxcountry__oxisoalpha2->value;

              $billAdress = Address::create()
                ->setName($billAdrName)
                ->setPostalCode($oOrder->oxorder__oxbillzip->value)
                ->setStreetName($oOrder->oxorder__oxbillstreet->value)
                ->setHouseNumber($oOrder->oxorder__oxbillstreetnr->value)
                ->setCity($oOrder->oxorder__oxbillcity->value)
                ->setCountry($billCountryIso);

              $payer->setBillingAddress($billAdress);
            }

            $request = PaymentCreationRequest::create()
              ->setMerchantPaymentReference($transactionId)
              ->setAmount($dAmount)
              ->setCurrency(strtoupper($sCur))
              ->setMarket(strtoupper($lang))
              ->setTitle($sActualPayment)
              ->setMerchantUrls($urls)
              ->setPayerData($payer)
              ->addLineItem($shipping);

            if (!empty($aItems)) {
              foreach ($aItems as $oItem) {
                $request->addLineItem($oItem);
              }
            }

            $payment = $api->createPayment($request);

            error_log("createPayment() - getPaymentStatus():" . $payment->getPaymentStatus());

            if ($payment->getPaymentStatus() == PaymentStatus::CREATED) {
              $strUrlRedirect = $payment->getPaymentLink();
              $paymentsData = $payment->getSpecificPaymentMethodData();
              $paymentSel = strtolower(substr($this->getPayment()->getId(), 3, strlen($this->getPayment()->getId())));
              $paymentZvId = $payment->getPaymentId();

              foreach ($paymentsData as $oPayment) {
                if ($paymentSel == strtolower($oPayment["paymentMethod"])) {
                  $strUrlRedirect = $oPayment["paymentLink"];
                  error_log("$paymentSel URL:$strUrlRedirect");
                }
              }

              // Add the zaver payment id
              $oOrder->addFieldName('zaver__payment_id');
              $oOrder->oxorder__zaver__payment_id = new oxField($paymentZvId, oxField::T_RAW);
              $oOrder->save();
              error_log("PaymentStatus::CREATED - getPaymentId():$paymentZvId");

              oxRegistry::getUtils()->redirect($strUrlRedirect, false);
            }
            else {
              // change order status
              $oOrder->oxorder__oxtransstatus = new oxField(ZaverConfig::ORDER_ERROR);
              $oOrder->save();
              oxRegistry::getSession()->setVariable('_zaver_payment_error', '');
              return parent::_getNextStep(oxOrder::ORDER_STATE_PAYMENTERROR);
            }
          }
          catch (Exception $e) {
            error_log("Exception:" . $e->getMessage());
            $oOrder->oxorder__oxtransstatus = new oxField(ZaverConfig::ORDER_ERROR);
            $oOrder->save();
            oxRegistry::getSession()->setVariable('_zaver_payment_error', $e->getMessage());
            return parent::_getNextStep(oxOrder::ORDER_STATE_PAYMENTERROR);
          }
        }
      }
    }

    return parent::_getNextStep($iSuccess);
  }

  /**
   * - order nr reservieren
   * - order löschen
   * - order objekt erstellen
   * - reservierte order nr übernehmen
   * - order finalisieren ($oOrder->finalizeOrder($this->getBasket(), $this->getUser()))
   * - onOrderExecute ($oUser->onOrderExecute($oBasket, $iSuccess))
   *
   * @param oxOrder $oOrder
   *
   * @return array
   */
  protected function zaver__recreateOrder(oxOrder $oOrder) {
    $oOrderNumber = $oOrder->oxorder__oxordernr->value;

    // create order number reservation
    /** @var zaver_order_number_reservation $oOrderNumberReservation */
    $oOrderNumberReservation = oxNew('zaver_order_number_reservation');
    $reservationKey = zaver_order_number_reservation::getReservationKey($oOrderNumber);
    if (!$oOrderNumberReservation->load($reservationKey)) {
      $oOrderNumberReservation->setId($reservationKey);
      $oOrderNumberReservation->save();
    }

    $oOrder->delete();
    /** @var oxorder $newOrder */
    $newOrder = oxNew('oxorder');
    $newOrder->oxorder__oxordernr = new oxField($oOrderNumber, oxField::T_RAW);
    $iSuccess = $newOrder->finalizeOrder($this->getBasket(), $this->getUser());
    $this->getUser()->onOrderExecute($this->getBasket(), $iSuccess);

    // delete order number reservation
    $oOrderNumberReservation->delete();

    return array($newOrder, $iSuccess);
  }

  /**
   * handles Zaver notify action.
   *
   */
  public function processZaverNotify() {
    error_log("processZaverNotify() _GET:" . print_r($_GET, true));
    error_log("processZaverNotify() _SERVER:" . print_r($_SERVER, true));
    if (empty($_GET['pm'])) {
      header('HTTP/1.1 400 Bad Request');
      echo "param pm not found.";
      exit;
    }

    $pm = $_GET['pm'];
    $sess_challenge = $_GET['sess_challenge'];

    /** @var zaver__oxorder $oOrder */
    $oOrder = oxNew('oxorder');
    $oOrder->load($sess_challenge);
    $oOrder->addFieldName('zaver__payment_status');

    if (!$oOrder->isLoaded()) {
      header('HTTP/1.1 400 Bad Request');
      echo "Order not found";
      exit;
    }

    try {
      $strErrorMsg = "";
      $strOrderStatus = $oOrder->oxorder__oxtransstatus;
      $api = new Checkout(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());
      $strCallBkToken = ZaverConfig::getCallbackToken();
      error_log("processZaverNotify() strCallBkToken:$strCallBkToken");
      $payment = $api->receiveCallback($strCallBkToken);

      $strPaymentStatus = $payment->getPaymentStatus();
      error_log("processZaverNotify() payment->getPaymentStatus():$strPaymentStatus");
      error_log("processZaverNotify() oxorder__oxtransstatus:$strOrderStatus");
      $bIsOrderOk = false;

      if ($oOrder->oxorder__zaver__status != "" && $strOrderStatus != ZaverConfig::ORDER_IN_PAYMENT) {
        if ($strOrderStatus == ZaverConfig::ORDER_OK) {
          $bIsOrderOk = true;
        }
        elseif ($strOrderStatus == ZaverConfig::ORDER_ERROR) {
          $bIsOrderOk = false;
        }
        else {
          if ($strPaymentStatus == PaymentStatus::SETTLED) {
            error_log("processZaverNotify() payment status SETTLED");
            $oOrder->oxorder__oxremark = new oxField('The payment was SETTLED', oxField::T_RAW);
            $oOrder->oxorder__oxtransstatus = new oxField(ZaverConfig::ORDER_OK);
            $oOrder->oxorder__zaver__payment_status = new oxField($strPaymentStatus);
            $oOrder->oxorder__oxpaid = new oxField(oxRegistry::get("oxUtilsDate")->formatDBDate(date("Y-m-d H:i:s"), true));
            $oOrder->save();

            /*$oRemark = oxNew( "oxremark" );
            $oRemark->load( oxConfig::getParameter( "rem_oxid" ) );
            $oRemark->oxremark__oxtext     = new oxField( oxConfig::getParameter( "remarktext" ) );
            $oRemark->oxremark__oxheader   = new oxField( oxConfig::getParameter( "remarkheader" ) );
            $oRemark->oxremark__oxtype     = new oxField( "o" );
            $oRemark->oxremark__oxparentid = new oxField( $oOrder->oxorder__oxuserid->value );
            $oRemark->save();*/
          }
          elseif ($strPaymentStatus == PaymentStatus::CANCELLED) {
            error_log("processZaverNotify() payment status CANCELLED");
            $oOrder->oxorder__oxremark = new oxField('The payment was CANCELLED', oxField::T_RAW);
            $oOrder->oxorder__zaver__payment_status = new oxField($strPaymentStatus);
            $oOrder->save();
          }
          $bIsOrderOk = true;
        }
      }
      elseif ($oOrder->oxorder__oxpaymenttype->value != $pm) {
        $bIsOrderOk = false;
      }
      elseif ($strPaymentStatus != PaymentStatus::PENDING || $strOrderStatus == ZaverConfig::ORDER_ERROR) {
        // Payment failed
        $oOrder->oxorder__oxtransstatus = new oxField(ZaverConfig::ORDER_ERROR);
        $oOrder->oxorder__zaver__status = new oxField(0);
        $oOrder->oxorder__zaver__payment_status = new oxField($strPaymentStatus);
        $oOrder->save();
        $oOrder->cancelOrder();

        $bIsOrderOk = true;
      }
      else {
        // Payment success
        $oOrder->oxorder__oxtransstatus = new oxField(ZaverConfig::ORDER_IN_PROCESS);
        $oOrder->oxorder__zaver__status = new oxField(1);
        $oOrder->oxorder__zaver__payment_status = new oxField($strPaymentStatus);
        //$oOrder->oxorder__oxpaid = new oxField(oxRegistry::get("oxUtilsDate")->formatDBDate(date("Y-m-d H:i:s"), true));
        $oOrder->save();
        oxRegistry::getSession()->setVariable('zaver_disable_article_check', '1');
        $oOrder->sendZaverOrderByEmail();
        oxRegistry::getSession()->deleteVariable('zaver_disable_article_check');
        $bIsOrderOk = true;
      }
    }
    catch (Exception $e) {
      $strErrorMsg = "Exception in notify: " . $e->getMessage();
      error_log("ERROR:$strErrorMsg, bIsOrderOk:$bIsOrderOk");
    }

    header("HTTP/1.1 200 OK");
    exit;
  }

  /**
   * handles Zaver redirect action.
   *
   */
  public function processZaverRedirect() {
    error_log("processZaverRedirect() _GET:" . print_r($_GET, true));

    $pm = $_GET['pm'];

    if (empty($_GET['pm'])) {
      exit;
    }

    $sess_challenge = $_GET['sess_challenge'];

    /** @var zaver__oxorder $oOrder */
    $oOrder = oxNew('oxorder');
    $oOrder->addFieldName('zaver__payment_id');
    $oOrder->addFieldName('zaver__payment_status');
    $oOrder->load($sess_challenge);
    $sErrorMsg = "";

    try {
      $api = new Checkout(ZaverConfig::getApiKey(), ZaverConfig::getIsTestEnviroment());
      $zvPaymentId = $oOrder->oxorder__zaver__payment_id->value;
      error_log("processZaverRedirect() oxorder__zaver__status:" . $oOrder->oxorder__zaver__status->value);
      error_log("processZaverRedirect() oxorder__oxtransstatus:" . $oOrder->oxorder__oxtransstatus->value);
      error_log("processZaverRedirect() oxorder__zaver__payment_status:" . $oOrder->oxorder__zaver__payment_status->value);
      error_log("processZaverRedirect() oxorder__zaver__payment_id:$zvPaymentId");

      $zvStatusPmRes = $api->getPaymentStatus($zvPaymentId);
      $zvStatusPm = $zvStatusPmRes->getPaymentStatus();

      error_log("processZaverRedirect() getPaymentStatus():$zvStatusPm");

      if ($oOrder->oxorder__zaver__status != "" &&
        $oOrder->oxorder__oxtransstatus != ZaverConfig::ORDER_IN_PAYMENT
      ) {
        if ($oOrder->oxorder__oxtransstatus == ZaverConfig::ORDER_OK) {
          $strResult = "OK";
          $sErrorMsg = "";
        }
        elseif ($oOrder->oxorder__oxtransstatus == ZaverConfig::ORDER_ERROR) {
          $strResult = "ERROR";
          $sErrorMsg = '';

          if ($zvStatusPm == PaymentStatus::CREATED) {
            $sErrorMsg = oxRegistry::getLang()->translateString("ZV_PAYMENT_CREATED_TXT");
          }
          elseif ($zvStatusPm == PaymentStatus::ERROR) {
            $sErrorMsg = oxRegistry::getLang()->translateString("ZV_PAYMENT_ERROR_TXT");
          }
          elseif ($zvStatusPm == PaymentStatus::CANCELLED) {
            $sErrorMsg = oxRegistry::getLang()->translateString("ZV_PAYMENT_CANCEL_TXT");
          }
        }
        elseif ($oOrder->oxorder__oxtransstatus == ZaverConfig::ORDER_IN_PROCESS) {
          $strResult = "OK";
          $sErrorMsg = "";
        }
        else {
          $strResult = "ERROR";
          $sErrorMsg = '';
        }
      }
      elseif ($oOrder->oxorder__oxpaymenttype->value != $pm) {
        $strResult = "ERROR";
        $sErrorMsg = oxRegistry::getLang()->translateString("ZV_PAYMENT_NOTVALID_TXT");
      }
      elseif ($zvStatusPm != PaymentStatus::PENDING) {
        //TRANSACTION FAILED

        // Is order set to delete on failure?
        if ($oOrder->isLoaded()) {
          $oOrder->oxorder__oxtransstatus = new oxField(ZaverConfig::ORDER_ERROR);
          $oOrder->oxorder__zaver__status = new oxField(0);
          $oOrder->save();
          $oOrder->cancelOrder();
        }

        $sErrorMsg = oxRegistry::getLang()->translateString("ZV_PAYMENT_CANCEL_TXT");

        if ($zvStatusPm == PaymentStatus::CREATED) {
          $sErrorMsg = oxRegistry::getLang()->translateString("ZV_PAYMENT_CREATED_TXT");
        }
        elseif ($zvStatusPm == PaymentStatus::ERROR) {
          $sErrorMsg = oxRegistry::getLang()->translateString("ZV_PAYMENT_ERROR_TXT");
        }

        $strResult = "ERROR";
      }
      else {
        //TRANSACTION SUCCESS
        $strResult = "OK";
        $sErrorMsg = "";
      }
      error_log("processZaverRedirect() strResult:$strResult, sErrorMsg:$sErrorMsg");
      switch ($strResult) {
        case "ERROR":
          oxRegistry::getSession()->setVariable(
            'sess_challenge', oxUtilsObject::getInstance()->generateUID()
          ); // <-- forces new order creation
          oxRegistry::getSession()->setVariable('_zaver_payment_error', $sErrorMsg);
          oxRegistry::getUtils()->redirect($this->getConfig()->getSslShopUrl() . 'index.php?cl=payment');
          break;

        default:
          oxRegistry::getUtils()->redirect($this->getConfig()->getSslShopUrl() . 'index.php?cl=thankyou');
          break;
      }
    }
    catch (Exception $e) {
      error_log("ERROR:" . $e->getMessage());

      if ($oOrder->oxorder__oxtransstatus == ZaverConfig::ORDER_IN_PROCESS) {
        oxRegistry::getUtils()->redirect($this->getConfig()->getSslShopUrl() . 'index.php?cl=thankyou');
      }
      else {
        oxRegistry::getSession()->setVariable(
          'sess_challenge', oxUtilsObject::getInstance()->generateUID()
        ); // <-- forces new order creation

        if ($oOrder->oxorder__zaver__payment_status == PaymentStatus::CANCELLED) {
          $sErrorMsg = oxRegistry::getLang()->translateString("ZV_PAYMENT_CANCEL_TXT");
        }
        else {
          $sErrorMsg = $e->getMessage();
        }

        oxRegistry::getSession()->setVariable('_zaver_payment_error', $sErrorMsg);
        oxRegistry::getUtils()->redirect($this->getConfig()->getSslShopUrl() . 'index.php?cl=payment');
      }
    }
  }
}