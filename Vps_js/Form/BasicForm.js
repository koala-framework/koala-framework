Ext.form.BasicForm.override
    resetDirty: function()
        this.items.each(function(field)
            field.originalValue = field.getValue(
        }
    
    setDefaultValues: function()
        this.items.each(function(field)
            field.setValue(field.defaultValue || ''
            field.originalValue = field.getValue(
        }, this
    
    clearValues: function()
        this.items.each(function(field)
            field.setValue(''
            field.originalValue = field.getValue(
        }, this
   
}
