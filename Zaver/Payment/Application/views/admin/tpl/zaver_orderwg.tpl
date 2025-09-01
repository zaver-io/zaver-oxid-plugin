[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    <!--

    //-->
</script>
<style>
    .refundTable TD {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    TD.borderTop {
        border-top: 1px solid black!important;
    }
    FIELDSET {
        border-radius: 15px;
        margin-bottom: 20px;
        padding: 10px;
    }
    FIELDSET.fullRefund SPAN{
        margin-left: 2px;
    }
    FIELDSET .refundSubmit {
        margin-top: 15px;
    }
    .typeSelect {
        margin-bottom: 10px;
    }
    FIELDSET.refundError {
        background-color: #FF8282;
        color: black;
        border: 3px solid #F00000;
    }
    FIELDSET.refundNotice {
        background-color: #ffeeb5;
        border: 3px solid #FFE385;
    }
    FIELDSET.refundSuccess {
        background-color: #7aff9e;
        border: 3px solid #00b02f;
    }
    FIELDSET.message STRONG {
        display: block;
        margin-bottom: 10px;
    }
</style>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
    [{else}]
    [{assign var="readonly" value=""}]
    [{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="zaver_orderwg">
</form>
[{if $sMessage}]
    <div class="messagebox">[{$sMessage}]</div>
[{else}]
    <iframe src="[{$Widget}]" title="Zaver" width="100%" height="100%" frameborder="0"></iframe>
[{/if}]

[{include file="bottomitem.tpl"}]