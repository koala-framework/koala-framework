Vps.Auto.GridFilter.DateRange = function(confi

    Vps.Auto.GridFilter.DateRange.superclass.constructor.call(this, config
    this.fieldFrom = new Vps.Form.DateField
        width: 8
        value: config.fr
    }
    this.toolbarItems.add(this.fieldFrom
    this.toolbarItems.add(' - '
    this.fieldTo = new Vps.Form.DateField
        width: 8
        value: config.
    }
    this.toolbarItems.add(this.fieldTo
    this.toolbarItems.add(new Ext.Button
        text: '»
        handler: function()
            this.fireEvent('filter', this, this.getParams()
        
        scope: th
    })


Ext.extend(Vps.Auto.GridFilter.DateRange, Vps.Auto.GridFilter.Abstract,
    reset: function()
        this.textField.reset(
    
    getParams: function()
        var params = {
        params[this.id+'_from'] = this.fieldFrom.getValue().format('Y-m-d'
        params[this.id+'_to'] = this.fieldTo.getValue().format('Y-m-d'
        return param
   
}
