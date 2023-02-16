<?php

class ZaverConfig extends oxSuperCfg
{
  const LOG_FILENAME = 'zaver.log';

  const PLUGIN_CODE = 'zaver';

  const PLUGIN_CODE_TXT = 'Zaver ';

  const PLUGIN_PREFIX = 'zv_';

  const VAR_CONFIG = 'zaver_config';

  const KEY_LOG_LEVEL = 'logLevel';

  const KEY_IS_TEST_ENVIROMENT = true;

  const KEY_HOST_URL = 'hosturl';
  const KEY_API_KEY = 'apikey';
  const KEY_CALLBACK_TOKEN = 'callbacktoken';
  const KEY_AUTOMATIC_CAPTURE = 'autocapture';

  const ORDER_OK = 'OK';
  const ORDER_ERROR = 'ERROR';
  const ORDER_NOT_FINISHED = 'NOT_FINISHED';
  const ORDER_IN_PAYMENT = 'IN_PAYMENT';
  const ORDER_IN_PROCESS = 'IN_PROCESS';


  /** @var oxConfig */
  private static $config;

  /** @var ZaverLogger */
  private static $logger;

  /**
   *
   * @var array
   */
  private static $methods = [
    ["paymentMethod" => self::PLUGIN_PREFIX."PAY_LATER",
      "title" => "Rechnung",
      "description" => "Später bezahlen",
      "iconSvgSrc" => "https://cdn.zaver.com/DE/paymentmethod/icon-pay-later.svg",
      "localizations" => array(
        "de-DE" => array(
          "title" => "Rechnung",
          "description" => "Später bezahlen",
          "iconSvgSrc" => "https://cdn.zaver.com/DE/paymentmethod/icon-pay-later.svg",
        ))],

    ["paymentMethod" => self::PLUGIN_PREFIX."INSTALLMENTS",
      "title" => "Teilzahlung",
      "description" => "",
      "iconSvgSrc" => "https://cdn.zaver.com/DE/paymentmethod/icon-installments.svg",
      "localizations" => array(
        "de-DE" => array(
          "title" => "Teilzahlung",
          "description" => "",
          "iconSvgSrc" => "https://cdn.zaver.com/DE/paymentmethod/icon-installments.svg",
        ))]
  ];

  private function __construct() {
    // only static context allowed
  }

  /**
   *
   * @return array
   */
  public static function getMethodsList() {
      return static::$methods;
  }

  private static function loadConfig()
  {
    if (!static::$config) {
      static::$config = oxRegistry::getConfig();
    }
  }

  /**
   * @param string $varName
   * @param string|null $keyName
   *
   * @return mixed|null
   */
  public static function get($varName, $keyName = null)
  {
    static::loadConfig();

    $data = static::$config->getShopConfVar($varName);

    return $keyName ? (isset($data[$keyName]) ? $data[$keyName] : null) : $data;
  }

  public static function getHostUrl() {
    return static::get(static::VAR_CONFIG, static::KEY_HOST_URL);
  }

  public static function getApiKey() {
    return static::get(static::VAR_CONFIG, static::KEY_API_KEY);
  }

  public static function getCallbackToken() {
    return static::get(static::VAR_CONFIG, static::KEY_CALLBACK_TOKEN);
  }

  public static function getAutomaticCapture() {
    return static::get(static::VAR_CONFIG, static::KEY_AUTOMATIC_CAPTURE);
  }

  public static function getIsTestEnviroment() {
    return static::KEY_IS_TEST_ENVIROMENT;
  }

  public static function getLogFilename() {
    static::loadConfig();

    return static::$config->getLogsDir() . static::LOG_FILENAME;
  }

  /**
   * @return ZaverLogger
   */
  public static function getLogger() {
    if (!static::$logger) {
      static::$logger = new ZaverLogger(static::getLogFilename(), '');
    }

    return static::$logger;
  }
}
