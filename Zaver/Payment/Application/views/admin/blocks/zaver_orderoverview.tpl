[{$smarty.block.parent}]
[{if isset($isZaverOrder) && $isZaverOrder}]
<tr>
    <td class="edittext">[{oxmultilang ident="ZAVER_PAYMENTID_TXT"}]:</td>
    <td class="edittext"><b>[{$zaverPaymentId}]</b></td>
</tr>
<tr>  
    <td class="edittext">[{oxmultilang ident="ZAVER_PAYMENT_STATUS_TXT"}]:</td>
    <td class="edittext"><b>[{$zaverStatus}]</b></td>
</tr>
    [{/if}]