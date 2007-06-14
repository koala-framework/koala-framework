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

Ext.data.Store.prototype.originalLoad = Ext.data.Store.prototype.load;
Ext.override(Ext.data.Store, {
    load : function(options) {
        //wenn meta nicht gesetzt meta-parameter schicken
        if(!this.reader.meta) {
            this.baseParams.meta = true;
        }
        this.originalLoad(options);
        if (this.baseParams.meta) delete this.baseParams.meta;
    }
});
if(Ext.tree.TreeLoader) {
    Ext.override(Ext.tree.TreeLoader, {
        processResponse : function(response, node, callback){
            var r = Ext.decode(response.responseText);
            var o = r.nodes;
            for(var i = 0, len = o.length; i < len; i++){
                var n = this.createNode(o[i]);
                if(n){
                    node.appendChild(n); 
                }
            }
            if(typeof callback == "function"){
                callback(this, node);
            }
        }
    });
}
