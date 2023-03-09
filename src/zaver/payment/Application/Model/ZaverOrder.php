<?php

namespace Zaver\Payment\Application\Model;

use Zaver\Payment\Application\Model\ZaverOrderNumReservation;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class ZaverOrder
 */
class ZaverOrder extends ZaverOrder_parent
{

  protected $_forceOrderStatusOk = false;

  /**
   * if zaver transaction
   * don't send order email under normal workflow
   *
   * @param null $oUser
   * @param null $oBasket
   * @param null $oPayment
   *
   * @return bool|int
   */
  protected function _sendOrderByEmail($oUser = null, $oBasket = null, $oPayment = null) {
    if ($this->zaver__isZaverOrder() && !$this->zaver__isPaymentDone()) {

      return self::ORDER_STATE_OK;
    }
    return parent::_sendOrderByEmail($oUser, $oBasket, $oPayment);
  }

  protected function _setOrderStatus($sStatus) {
    if ($sStatus != 'OK' || !$this->zaver__isZaverOrder() || $this->_forceOrderStatusOk) {
      $this->_forceOrderStatusOk = false;
      parent::_setOrderStatus($sStatus);
    }
  }


  /**
   * separate method to send zaver order email
   * once order status = OK
   *
   */
  public function sendZaverOrderByEmail() {

    $this->_forceOrderStatusOk = true;
    //$this->_setOrderStatus('OK');

    $this->_oBasket = $this->_getRecalculatedBasket();
    $this->_oUser = $this->_getUserFromOrder();

    $this->_oPayment = $this->getPaymentType();
    $this->_sendOrderByEmail($this->_oUser, $this->_oBasket, $this->_oPayment);
  }

  /**
   * @return bool
   */
  public function zaver__isZaverOrder() {
    return stristr($this->oxorder__oxpaymenttype->value, 'zv_') !== false;
  }

  /**
   * @return bool
   */
  public function zaver__isPaymentDone() {
    // if status from zaver is available, payment is done
    if (empty($this->oxorder__zaver__status->value)) {
      return false;
    }

    return true;
  }

  /**
   * @return oxBasket
   */
  protected function _getRecalculatedBasket() {
    $oBasket = Registry::getSession()->getBasket();
    $oBasketArticles = $oBasket->getBasketArticles();

    if (count($oBasketArticles) > 0) {
      return $oBasket;
    }

    $oBasket = $this->_getOrderBasket();

    // add this order articles to virtual basket and recalculates basket
    #$this->_addOrderArticlesToBasket($oBasket, $this->getOrderArticles(true));
    $this->_addArticlesToBasket($oBasket, $this->getOrderArticles(true));

    // recalculating basket
    $oBasket->calculateBasket(true);
    return $oBasket;
  }

  /**
   * @return null|oxUser
   */
  protected function _getUserFromOrder() {
    $oUser = NULL;
    $oUser = Registry::getSession()->getUser();
    if ($oUser != NULL) {
      if ($oUser->isLoaded() == true) {
        return $oUser;
      }
    }
    $oUser = $this->_oBasket->getBasketUser();
    return $oUser;
  }

  /**
   * @param      $sMaxField
   * @param null $aWhere
   * @param int $iMaxTryCnt
   */
  protected function _setRecordNumber_($sMaxField, $aWhere = null, $iMaxTryCnt = 5) {
    /** @var zaver_order_number_reservation $orderNumberReservation */
    $orderNumberReservation = oxNew(ZaverOrderNumReservation::class);
    do {
      // as long as a reservation exists for the current order number
      // create a new order number
      parent::_setRecordNumber($sMaxField, $aWhere, $iMaxTryCnt);

      $reservationExists = $orderNumberReservation->load(
        zaver_order_number_reservation::getReservationKey($this->oxorder__oxordernr->value)
      );
    } while ($reservationExists);
  }

  /**
   * Creates and returns user payment.
   *
   * @param string $sPaymentid used payment id
   *
   * @return oxUserPayment
   */
  function setPayment($paymentId) {

    return $this->_setPayment($paymentId);
  }
}