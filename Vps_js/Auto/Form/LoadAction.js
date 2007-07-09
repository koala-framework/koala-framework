Ext.namespace('Vps.Auto.Form');

Vps.Auto.Form.LoadAction = function(form, options){
    Vps.Auto.Form.LoadAction.superclass.constructor.call(this, form, options);
};

Ext.extend(Vps.Auto.Form.LoadAction, Ext.form.Action.Load,
{
    run : function()
    {
        if (!this.options.params) this.options.params = {};
        this.options.params.meta = true;
        this.options.method = 'POST';
        return Vps.Auto.Form.LoadAction.superclass.run.call(this);
    },
    
    handleResponse : function(response)
    {
        var ret = Vps.Auto.Form.LoadAction.superclass.handleResponse.call(this, response);
        if (ret && ret.success && ret.meta && this.options.meta) {
            Ext.callback(this.options.meta, this.options.scope, [ret.meta]);
        }
        return ret;
    }
});

Ext.form.Action.ACTION_TYPES.loadAutoForm = Vps.Auto.Form.LoadAction;
