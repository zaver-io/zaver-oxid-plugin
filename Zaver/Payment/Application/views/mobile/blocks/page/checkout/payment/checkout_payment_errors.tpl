[{if $oView->isZaverPaymentError() === TRUE}]
    <div class="alert alert-info">[{ $oView->getZaverPaymentError() }]</div>
[{else}]
[{$smarty.block.parent}]
[{/if}]