
Ext.namespace(
'Vps', 'Vpc',
'Vps.Admin', 'Vps.Admin.Pages', 'Vps.Admin.Page',
'Vps.Login',
'Vps.Menu'
);

Ext.applyIf(Array.prototype, {
    each : function(fn, scope){
        Ext.each(this, fn, scope);
    }
});

Ext.onReady(function()
{
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

    Ext.UpdateManager.defaults.loadScripts = true;
    Ext.form.Field.prototype.msgTarget = 'side';// turn on validation errors beside the field globally

    Ext.QuickTips.init();
});

function formatBoolean(value){
    return value ? 'Ja' : 'Nein';
}
function formatPassword(value){
    return value||true ? '******' : '';
}
function formatMoney(v)
{
    if (v == 0) return "";
    v = v.toString().replace(",", ".");
    v = (Math.round((v-0)*100))/100;
    v = (v == Math.floor(v)) ? v + ".00" : ((v*10 == Math.floor(v*10)) ? v + "0" : v);
    v = v.toString().replace(".", ",");
    return v + " €";
}
