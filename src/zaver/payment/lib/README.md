# Zaver PHP SDK
This is the officially supported PHP SDK for [Zaver Checkout](https://www.zaver.io/), developed as a Composer package by [The Web Mafia](https://www.webbmaffian.se/).

## Requirements
- PHP 7.4+
- Zaver API key (either test or prod)
- Zaver callback token (optional but recommended)

## Installation
```
composer require zaver/sdk
```

## Usage
All classes and methods are properly type-hinted with complementary PHPDoc - please see [the examples](#examples) on this page, and read [the API documentation](https://api-docs.zaver.se/v-1-2-0/checkout.html) for further details.

## Examples
- [Initialize payment](#initialize-payment)
- [Receive payment callback](#receive-payment-callback)
- [Do a refund](#do-a-refund)
- [Receive refund callback](#receive-refund-callback-eg-after-refund-approval)

### Initialize payment
```php
// URL: https://example.com/checkout

use Zaver\SDK\Checkout;
use Zaver\SDK\Object\MerchantUrls;
use Zaver\SDK\Object\PaymentCreationRequest;
use Zaver\SDK\Object\LineItem;
use Zaver\SDK\Config\ItemType;

const API_KEY = '<your API key>';
const CALLBACK_TOKEN = '<your callback token>';
const IS_TEST_ENVIRONMENT = true;

$api = new Checkout(API_KEY, IS_TEST_ENVIRONMENT);

$item = LineItem::create()
    ->setName('Fancy pants')
    ->setMerchantReference('FANCY-123')
    ->setQuantity(2)
    ->setUnitPrice(1000)
    ->setTotalAmount(2000)
    ->setTaxRatePercent(25)
    ->setTaxAmount(400)
    ->setItemType(ItemType::PHYSICAL);

$shipping = LineItem::create()
    ->setName('DHL')
    ->setQuantity(1)
    ->setUnitPrice(100)
    ->setTotalAmount(100)
    ->setTaxRatePercent(25)
    ->setTaxAmount(20)
    ->setItemType(ItemType::SHIPPING);

$urls = MerchantUrls::create()
    ->setSuccessUrl('https://example.com/thank-you')
    ->setCancelUrl('https://example.com/canceled')
    ->setCallbackUrl('https://example.com/api/payment-callback');

$request = PaymentCreationRequest::create()
    ->setMerchantPaymentReference('123456')
    ->setAmount(2100)
    ->setCurrency('SEK')
    ->setMarket('SE')
    ->setTitle('My fancy payment')
    ->setMerchantUrls($urls)
    ->addLineItem($item)
    ->addLineItem($shipping);

$payment = $api->createPayment($request);

echo $payment->getPaymentStatus();
// Output: CREATED

echo $api->getHtmlSnippet($payment);
// Outputs iframe with Zaver Checkout
```

### Receive payment callback
```php
// URL: https://example.com/api/payment-callback

use Zaver\SDK\Checkout;

const API_KEY = '<your API key>';
const CALLBACK_TOKEN = '<your callback token>';
const IS_TEST_ENVIRONMENT = true;

$api = new Checkout(API_KEY, IS_TEST_ENVIRONMENT);
$payment = $api->receiveCallback(CALLBACK_TOKEN);

echo $payment->getPaymentStatus();
// Output: SETTLED
```

### Do a refund
```php
use Zaver\SDK\Refund;
use Zaver\SDK\Object\RefundCreationRequest;

const API_KEY = '<your API key>';
const CALLBACK_TOKEN = '<your callback token>';
const IS_TEST_ENVIRONMENT = true;

$api = new Refund(API_KEY, IS_TEST_ENVIRONMENT);

// It does most likely not make any sense to fetch the payment first - this is just for
// showing the relation between a payment and a refund.
$payment = $api->getPaymentStatus('463d6d10-4c0f-424b-b804-8e95114864dd');

$urls = MerchantUrls::create()
    ->setCallbackUrl('https://example.com/api/refund-callback');

$request = RefundCreationRequest::create()
    ->setPaymentId($payment->getPaymentId())
    ->setRefundAmount($payment->getAmount())
    ->setDescription('Mr Fancy Pants changed his mind')
    ->setMerchantUrls($urls);

foreach($payment->getLineItems() as $paymentItem) {
    $refundItem = RefundLineItem::create()
        ->setLineItemId($paymentItem->getId())
        ->setRefundTotalAmount($paymentItem->getTotalAmount())
        ->setRefundTaxAmount($paymentItem->getTaxAmount())
        ->setRefundTaxRatePercent($paymentItem->getTaxRatePercent())
        ->setRefundQuantity($paymentItem->getQuantity())
        ->setRefundUnitPrice($paymentItem->getUnitPrice());
    
    $request->addLineItem($refundItem);
}

$refund = $api->createRefund($request);

echo $refund->getStatus();
// Outputs: PENDING_MERCHANT_APPROVAL
    
```

### Receive refund callback (e.g. after refund approval)
```php
// URL: https://example.com/api/refund-callback

use Zaver\SDK\Refund;

const API_KEY = '<your API key>';
const CALLBACK_TOKEN = '<your callback token>';
const IS_TEST_ENVIRONMENT = true;

$api = new Refund(API_KEY, IS_TEST_ENVIRONMENT);
$refund = $api->receiveCallback(CALLBACK_TOKEN);

echo $refund->getStatus();
// Output: PENDING_EXECUTION
```
