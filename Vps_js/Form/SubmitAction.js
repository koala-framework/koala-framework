Vps.SubmitAction = function(form, options
    Vps.SubmitAction.superclass.constructor.call(this, form, options


Ext.extend(Vps.SubmitAction, Ext.form.Action.Submit,
    run : function(
        if (!this.options.params) this.options.params = {

        //manually add date-value and checkbox-state and clear name to submit it only on
        this.form.items.each(function(field)
            if (Ext.form.DateField && field instanceof Ext.form.DateField && field.getValue() instanceof Date)
                this.options.params[field.getName()] = field.getValue().dateFormat("Y-m-d"
            } else if (Ext.form.Checkbox && field instanceof Ext.form.Checkbox)
                if (field.getValue())
                    this.options.params[field.getName()] = 
                } else
                    this.options.params[field.getName()] = 
               
            } else if (field.rendered && field.el.dom.type == 'file')
                //überspringen, file-upload muss direkt submitted werd
                retur
            } else
                var v = field.getValue(
                if (typeof v == 'object')
                    v = Ext.encode(v
               
                this.options.params[field.getName()] = 
           
            if (field.rendered)
                field.realName = field.getName(
                field.el.dom.name = '
           
        }, this

        Vps.SubmitAction.superclass.run.call(this

        //restore nam
        this.form.items.each(function(field)
            if (field.realName)
                field.el.dom.name = field.realNam
                delete field.realNam
           
        }, this
   
}
Ext.form.Action.ACTION_TYPES.submit = Vps.SubmitAction; //normale submit-aktion überschreib
