<?php

class ZaverApi extends oxBase
{
  const USP = '';
  const HTTP_POST = 'POST';
  const HTTP_GET  = 'GET';
  const USER_AGENT = 'Zaver-Oxid/1.0.0';
  const HTTP_CODE_OK = 200;
  const HTTP_CODE_ERROR = 500;
  const HTTP_CODE_INVALID_DATA = 204;
  const HTTP_CODE_INVALID_MID = 404;
  const ENDPOINT_GETPAYMENTS = '/payments/checkout/paymentmethods/v1';
  const ENDPOINT_GETWIDGET = '/payments/manage/v1';
  const TOKEN = 'Bearer ';

  private $_hostUrl;
  private $_apiKey;
  private $_command;
  private $_params = array();
  private $_headers = array();
  public $_headersResponse = array();
  public $_responseArray;

  public static $lastRawResponse;
  public static $lastRawCurlOptions;

  public function __construct() {
    $this->_hostUrl = ZaverConfig::getHostUrl();
    $this->_apiKey = ZaverConfig::getApiKey();
  }

  /**
   * Return URL for the webservice
   *
   * @return string
   */
  public function getUrl()
  {
    $confURL = rtrim($this->_hostUrl, '/');
    $url = $confURL . self::USP . $this->_command;

    return $url;
  }

  /**
   * Return the token
   *
   * @return string
   */
  public function getToken()
  {
    return self::TOKEN;
  }

  /**
   * Return headers for the webservice
   *
   * @return array
   */
  protected function getHeaders()
  {
    $aCurHeaders = array();
    $aCurHeaders[] = "Authorization: ". self::TOKEN . $this->_apiKey;

    if (isset($this->_headers["contentType"])) {
      $aCurHeaders[] = "Content-type: " . $this->_headers["contentType"];
    }

    /*if (isset($this->_headers["token"])) {
      $aCurHeaders[] = "token: " . $this->_headers["token"];
    }*/

    return $aCurHeaders;
  }

  protected function handleHeaderLine($curl, $headerLine) {
    $params = explode(' ', $headerLine);
    $this->_headersResponse[$params[0]] = $params[1];
  }

  protected function handleHeaderResponse($headersResponse) {
    $this->_headersResponse = array();
    $output = rtrim($headersResponse);
    $data = explode("\n", $output);
    $this->_headersResponse['status'] = $data[0];
    array_shift($data);

    foreach ($data as $part) {

      // Some headers will contain ":" character (Location for example), and the part after ":" will be lost.
      $middle = explode(":", $part,2);

      // Remove warning message if $middle[1] does not exist
      if (!isset($middle[1])) {
        $middle[1] = null;
      }

      $this->_headersResponse[trim($middle[0])] = trim($middle[1]);
    }
  }

  /**
   * Perform HTTP request to REST endpoint
   *
   * @param string $method
   * @return array
   */
  protected function _requestApi($method)
  {
    $aResponse = array();

    $curlOpts = array(
      CURLOPT_URL => $this->getUrl(),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_USERAGENT => self::USER_AGENT,
      CURLOPT_SSL_VERIFYPEER => true,
      //CURLOPT_HEADERFUNCTION => array(self, 'handleHeaderLine'),
      CURLOPT_HEADER => true,
      CURLOPT_HTTPHEADER => $this->getHeaders()
    );

    if (self::HTTP_GET === $method) {
      if (0 !== count($this->_params)) {
        $curlOpts[CURLOPT_URL] .= false === strpos($curlOpts[CURLOPT_URL], '?') ? '?' : '&';
        $curlOpts[CURLOPT_URL] .= http_build_query($this->_params, null, '&');
      }
    } elseif (self::HTTP_PUT === $method || self::HTTP_POST === $method || self::HTTP_PATCH == $method) {
      if ($this->_headers["contentType"] == "text/csv") {
        $curlOpts[CURLOPT_POSTFIELDS] = $this->_params;
      } else {
        $curlOpts[CURLOPT_POSTFIELDS] = $this->_params;
      }
    } else {
      $curlOpts[CURLOPT_POSTFIELDS] = http_build_query($this->_params, null, '&');
    }

    $curl = curl_init();
    curl_setopt_array($curl, $curlOpts);

    $response = curl_exec($curl);

    $responseInfo = curl_getinfo($curl);

    $responseBody = substr($response, $responseInfo['header_size']);
    $responseHeader = substr($response, 0, $responseInfo['header_size']);

    self::$lastRawCurlOptions = $curlOpts;
    self::$lastRawResponse = $response;

    self::handleHeaderResponse($responseHeader);

    if ($response === false) {
      $aResponse['error'] = curl_error($curl);
    }

    curl_close($curl);

    if ((('application/json' === $responseInfo['content_type']) ||
        ('application/json;charset=UTF-8' === $responseInfo['content_type']))
      && !empty($responseBody)) {
      $responseBody = json_decode($responseBody);
    }

    $aResponse['url'] = $responseInfo['url'];
    $aResponse['headerStatus'] = $responseInfo['http_code'];
    $aResponse['body'] = $responseBody;

    return $aResponse;
  }

  /**
   * Perform API and handle
   *
   * @param string $method
   * @return mixed
   */
  public function request($method = 'POST')
  {
    try {
      $this->_responseArray = $this->_requestApi($method);

      $httpStatusCode = $this->_responseArray['headerStatus'];
      $aRequest = $this->_responseArray;

      if ($httpStatusCode != self::HTTP_CODE_OK) {
        $strErrorMessage = 'Server returned HTTP status code ' . $httpStatusCode;

        if (isset($this->_responseArray->body['error'])) {
          $strErrorMessage .= $this->_responseArray->body['error'];
        }

        $strMessage = '';
        if (isset($this->_responseArray->body['message'])) {
          $strMessage = $this->_responseArray->body['message'];
        }

        $aRequest['error'] = $strErrorMessage;
        $aRequest['message'] = $strMessage;
        $aRequest['response_code'] = $httpStatusCode;
        $aRequest['raw_request'] = print_r(self::$lastRawCurlOptions, true);
        $aRequest['raw_response'] = self::$lastRawResponse;
      }
      return $aRequest;
    } catch (Exception $e) {
      return array("error" => $e->getMessage(), "raw_response" => self::$lastRawResponse);
    }
  }

  /**
   * Return information about the current payment methods available
   *
   * @param string $market
   * @param int $amount
   * @param int $currency
   * @return mixed
   */
  public function getPayments($market = '', $amount = '', $currency = '')
  {
    $aRes = array();
    $aPayments = array();

    $this->_command = self::ENDPOINT_GETPAYMENTS;
    $this->_headers = array("contentType" => "application/json");

    if (!empty($market)) {
      $this->_params['market'] = $market;
    }

    if (!empty($amount)) {
      $this->_params['amount'] = $amount;
    }

    if (!empty($currency)) {
      $this->_params['currency'] = $currency;
    }

    $aRequest = $this->request(self::HTTP_GET);

    if ($aRequest['headerStatus'] == self::HTTP_CODE_OK) {
      $aPayments = $aRequest['body']->paymentMethods;
    }
    $aRes['headerStatus'] = $aRequest['headerStatus'];
    $aRes['aPayments'] = $aPayments;

    return $aRes;
  }
}
