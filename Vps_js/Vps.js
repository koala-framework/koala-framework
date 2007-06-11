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
