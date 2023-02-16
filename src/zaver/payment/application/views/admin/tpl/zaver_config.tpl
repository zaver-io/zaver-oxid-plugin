[{include file="headitem.tpl" title="Zaver Configuration"}]
[{assign var="sZaverCssPath" value=$oViewConf->getModuleUrl('zaver', 'out/admin/src/css/zaver_admin.css')}]
<link rel="stylesheet" href="[{$sZaverCssPath}]" type="text/css"/>
[{if $zaver_error_message}]
    <p style="color:red;">[{$zaver_error_message}]</p>
    [{/if}]
[{if $zaver_error eq 4}]
    <p style="color:red;">[{ oxmultilang ident="ZAVER_ADMIN_ERROR_SYNC" }]</p>
    [{elseif $zaver_error eq 2}]
    <p style="color:green;">[{ oxmultilang ident="ZAVER_ADMIN_SUCCESS" }]</p>
    [{elseif $zaver_error eq 3}]
    <p style="color:green;">[{ oxmultilang ident="ZAVER_ADMIN_SUCCESS_SYNC" }]</p>
    [{/if}]
<hr/>
<div id="zaver_admin" style="display:none">
    <label class="pe_map_header">[{ oxmultilang ident="ZAVER_ADMIN" }]</label>
    <div id="zaver_admin_iframe"></div>
</div>
<div style="padding:20px;" id="zaver_config">
    <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
        <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
        [{$oViewConf->getHiddenSid()}]

        <div class="zaverCont">
            <div class="cntRow">
                <div class="pe_payment_title">
                    <div class="cntRgt" id="config_0">
                        <div class="zaver-config-section">
                            <div class="zaver-config-section-title">[{ oxmultilang ident="ZAVER_API_CONFIGURATION" }]
                            </div>
                            <dl>
                                <dd>
                                    <label>[{ oxmultilang ident="ZAVER_HOSTURL" }]</label>
                                </dd>
                                <dt>
                                    <input type="text" class="editinput" name="zaver_config[hosturl]"
                                           value="[{$zaver_config.hosturl}]"/>
                                <dd><span>[{ oxmultilang ident="ZAVER_HOSTURL_DESCRIPTION" }]</span></dd>
                                </dt>
                                <div class="spacer"></div>
                            </dl>
                            <dl>
                                <dd>[{ oxmultilang ident="ZAVER_APIKEY" }]</dd>
                                <dt>
                                    <input type="text" class="editinput" name="zaver_config[apikey]"
                                           value="[{$zaver_config.apikey}]"/>
                                <dd><span>[{ oxmultilang ident="ZAVER_APIKEY_DESCRIPTION" }]</span></dd>
                                </dt>
                                <div class="spacer"></div>
                            </dl>
                            <dl>
                                <dd>[{ oxmultilang ident="ZAVER_CALLBACK_TOKEN" }]</dd>
                                <dt>
                                    <input type="text" class="editinput" name="zaver_config[callbacktoken]"
                                           value="[{$zaver_config.callbacktoken}]"/>
                                <dd><span>[{ oxmultilang ident="ZAVER_CALLBACK_TOKEN_DESCRIPTION" }]</span></dd>
                                </dt>
                                <div class="spacer"></div>
                            </dl>
                            <dl>
                                <dd class="cntExLft"></dd>
                                <dt>
                                    <input type="hidden" name="zaver_config[autocapture]" value="0" />
                                    <input type="checkbox" class="editinput" name="zaver_config[autocapture]" value="1"
                                           [{if $zaver_config.autocapture}]checked="checked"[{/if}] />&nbsp;
                                    [{ oxmultilang ident="ZAVER_AUTOMATIC_CAPTURE" }]<br />
                                </dt>
                                <div class="spacer"></div>
                            </dl>
                            <div class="cntExLft">
                                <input type="submit" class="zaver-config-btn" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onclick="document.myedit.fnc.value='save'; document.myedit.submit();" style="margin:1em 3em 0;"/>
                            </div>
                        </div>
                        <div class="zaver-config-section">
                            <div class="zaver-config-section-title">[{ oxmultilang ident="ZAVER_BASIC_CONFIGURATION" }]
                            </div>
                            <div class="cntExLft">
                                <input type="submit" class="zaver-config-btn" name="synchronize" value="[{ oxmultilang ident="ZAVER_SYNCHRONIZE"
                                }]" onclick="document.myedit.fnc.value='synchronize'; synchronize();" style="margin:1em 3em 0;"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="fnc" value="synchronize">
    </form>
</div>
<script type="text/javascript">
    function synchronize() {
        if (confirm([{oxmultilang ident = "ZAVER_SYNCHRONIZE_CONFIRM"}])) {
            document.myedit.submit();
        }
    }
</script>
[{include file="bottomitem.tpl"}]
