Vps.SubmitAction = function(form, options){
    Vps.SubmitAction.superclass.constructor.call(this, form, options);
};

Ext.extend(Vps.SubmitAction, Ext.form.Action.Submit, {
    run : function(){
        if (!this.options.params) this.options.params = {};
        this.form.items.each(function(field) {
            if (field instanceof Ext.form.DateField) {
                this.options.params[field.getName()] = field.getValue().dateFormat("Y-m-d");
            } else if (field instanceof Ext.form.Checkbox) {
                this.options.params[field.getName()] = field.getValue();
            }
        }, this);
        Vps.SubmitAction.superclass.run.call(this);
    }
});
Ext.form.Action.ACTION_TYPES.submit = Vps.SubmitAction; //normale submit-aktion überschreiben
