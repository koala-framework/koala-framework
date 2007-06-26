Vps.SubmitAction = function(form, options){
    Vps.SubmitAction.superclass.constructor.call(this, form, options);
};

Ext.extend(Vps.SubmitAction, Ext.form.Action.Submit, {
    run : function(){
        if (!this.options.params) this.options.params = {};

        //manually add date-value and checkbox-state and clear name to submit it only once
        this.form.items.each(function(field) {
            if (field instanceof Ext.form.DateField && field.getValue() instanceof Date) {
                this.options.params[field.getName()] = field.getValue().dateFormat("Y-m-d");
            } else if (field instanceof Ext.form.Checkbox) {
                if (field.getValue()) {
                    this.options.params[field.getName()] = 1;
                } else {
                    this.options.params[field.getName()] = 0;
                }
            }
            if(this.options.params[field.getName()]) {
                field.realName = field.getName();
                field.el.dom.name = '';
            }
        }, this);

        Vps.SubmitAction.superclass.run.call(this);

        //restore names
        this.form.items.each(function(field) {
            if(field.realName) {
                field.el.dom.name = field.realName;
                delete field.realName;
            }
        }, this);
    }
});
Ext.form.Action.ACTION_TYPES.submit = Vps.SubmitAction; //normale submit-aktion überschreiben
