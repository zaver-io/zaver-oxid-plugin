[{$smarty.block.parent}]
[{if isset($isZaverOrder) && $isZaverOrder}]
<tr>
    <td class="edittext">[{oxmultilang ident="ZAVER_PAYMENTID_TXT"}]:</td>
    <td class="edittext"><b>[{$zaverPaymentId}]</b></td>
</tr>
    [{/if}]