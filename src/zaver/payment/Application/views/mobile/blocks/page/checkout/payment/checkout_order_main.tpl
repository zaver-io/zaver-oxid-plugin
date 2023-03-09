    [{capture assign=gcOrderButtonDisablingJavascript}]

    $(document).ready(function()
    {
        if ( $( "#orderConfirmAgbBottom" ).length ) {
            $("#orderConfirmAgbBottom").find("button[type=submit]").click(function(e){

                if($(this).hasClass("gcDisabled")){
                    e.preventDefault();
                }else{
                    $(this).addClass("gcDisabled");
                }
            });
        }
    });
    [{/capture}]
    [{oxscript add=$gcOrderButtonDisablingJavascript}]  

[{$smarty.block.parent}]