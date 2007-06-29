Ext.BLANK_IMAGE_URL = '/assets/ext/resources/images/default/s.gif';

Ext.namespace(
'Vps', 'Vpc',
'Vps.Component',
'Vps.Admin.Pages', 'Vps.Admin.Page',
'Vps.Login',
'Vps.Menu',
'Vps.Form'
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
