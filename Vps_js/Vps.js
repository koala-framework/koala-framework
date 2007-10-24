Ext.BLANK_IMAGE_URL = '/assets/ext/resources/images/default/s.gif';

Ext.namespace(
'Vps', 'Vpc',
'Vps.Component',
'Vps.User.Login',
'Vps.Auto',
'Vps.Form'
);

Ext.applyIf(Array.prototype, {

    //to use array.each directly
    each : function(fn, scope){
        Ext.each(this, fn, scope);
    },

    //add is alias for push
    add : function() {
        this.push.apply(this, arguments);
    }
});

Ext.onReady(function()
{
//     Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

//     Ext.form.Field.prototype.msgTarget = 'side';// turn on validation errors beside the field globally

    if (Ext.QuickTips) {
        //init quicktips when loaded
        Ext.QuickTips.init();
    }
});

Vps.handleError = function(e)
{
    if (e.toString) e = e.toString();
    if (e.message) e = e.message;
    if(Ext.get('loading')) {
        Ext.get('loading').fadeOut({remove: true});
    }
    if (Vps.debug) {
        throw e; //re-throw
    } else {
        Ext.Msg.alert('Error', "Ein Fehler ist aufgetreten.");
        Ext.Ajax.request({
            url: '/error/jsonMail',
            params: {msg: e}
        });
    }
};
