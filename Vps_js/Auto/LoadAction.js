Ext.namespace('Vps.AutoForm');

Vps.AutoForm.LoadAction = function(form, options){
    Vps.AutoForm.LoadAction.superclass.constructor.call(this, form, options);
};

Ext.extend(Vps.AutoForm.LoadAction, Ext.form.Action.Load,
{
    run : function()
    {
        if (!this.options.params) this.options.params = {};
        this.options.params.meta = true;
        this.options.method = 'POST';
        return Vps.AutoForm.LoadAction.superclass.run.call(this);
    },
    
    handleResponse : function(response)
    {
        var ret = Vps.AutoForm.LoadAction.superclass.handleResponse.call(this, response);
        if (ret && ret.success && ret.meta && this.options.meta) {
            Ext.callback(this.options.meta, this.options.scope, [ret.meta]);
        }
        return ret;
    }
});

Ext.form.Action.ACTION_TYPES.loadAutoForm = Vps.AutoForm.LoadAction;
