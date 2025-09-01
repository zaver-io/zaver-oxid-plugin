<style>
    .zv-image {
        max-width: 100%;
        height: auto;
        float: right;
    }

    .zv-clearfix::after {
        content: "";
        clear: both;
        display: table;
    }
</style>
[{if $oView->getActiveCurrencyName() == "EUR"}]
    [{if $oView->isSettingsSet($sPaymentID) == true}]
    <dl>
        <dt>
        <div class="zv-clearfix">
            <img src="[{$oView->getPaymentLogo($sPaymentID)}]" border="0" class="zv-image">
            <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]"
                   [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]/>
            <label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}]</b></label>
        </div>
        </dt>
        <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
            [{block name="checkout_payment_longdesc"}]
            <div class="desc">
                [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                [{ $paymentmethod->oxpayments__oxlongdesc->value nofilter}]
                [{/if}]
            </div>
            [{/block}]
        </dd>
    </dl>
    [{/if}]
    [{/if}]