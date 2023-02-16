<?php

/**
 * zaver event class fired onActivate module.
 *
 */
class zaver_events
{
  /**
   * Execute zaver Module setup
   *
   * @return null
   */
  public static function onActivate() {
    try {
      $zaver_Setup = oxRegistry::get('zaver_setup');
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
      $zaver_Setup = oxRegistry::get('zaver_setup');
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