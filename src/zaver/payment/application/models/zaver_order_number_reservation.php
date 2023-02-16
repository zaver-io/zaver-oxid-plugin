<?php

/**
 * Class zaver_order_number_reservation
 */
class zaver_order_number_reservation extends oxBase
{

  /**
   * Object core table name
   *
   * @var string
   */
  protected $_sCoreTable = 'zaver_order_number_reservations';

  /**
   * Current class name
   *
   * @var string
   */
  protected $_sClassName = 'zaver_order_number_reservation';

  /**
   * @param $orderNr
   *
   * @return string
   */
  public static function getReservationKey($orderNr) {
    if (oxRegistry::getConfig()->getConfigParam('blSeparateNumbering')) {
      return $orderNr . '-' . oxRegistry::getConfig()->getShopId();
    }

    return $orderNr;
  }
}