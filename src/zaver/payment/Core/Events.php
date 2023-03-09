<?php

namespace Zaver\Payment\Core;

use Zaver\Payment\Core\Setup;

/**
 * zaver event class fired onActivate module.
 *
 */
class Events
{
  /**
   * Execute zaver Module setup
   *
   * @return null
   */
  public static function onActivate() {
    try {
      $zaver_Setup = oxNew(Setup::class);
      $zaver_Setup->zaver__install();


    }
    catch (oxException $e) {
      // @codeCoverageIgnoreStart
      if (!defined('OXID_PHP_UNIT')) {
        die($e->getMessage());
      }
      // @codeCoverageIgnoreEnd
    }
  }

  public static function onDeactivate() {
    try {
      $zaver_Setup = oxNew(Setup::class);
      $zaver_Setup->zaver__install();
    }
    catch (oxException $e) {
      // @codeCoverageIgnoreStart
      if (!defined('OXID_PHP_UNIT')) {
        die($e->getMessage());
      }
      // @codeCoverageIgnoreEnd
    }
  }
}