<?php

namespace Zaver\Payment\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Base;

/**
 * Class zaver_order_number_reservation
 */
class ZaverOrderNumReservation extends Base
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
    if (Registry::getConfig()->getConfigParam('blSeparateNumbering')) {
      return $orderNr . '-' . Registry::getConfig()->getShopId();
    }

    return $orderNr;
  }
}