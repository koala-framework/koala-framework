Vps.Auto.GridFilter.ComboBox = function(confi

    Vps.Auto.GridFilter.ComboBox.superclass.constructor.call(this, config

    var data = config.dat
    data.unshift([0, 'all']
    var filterStore = new Ext.data.SimpleStore
        id: 
        fields: ['id', 'name'
        data: da
    }
    this.combo = new Ext.form.ComboBox
            store: filterStor
            displayField: 'name
            valueField: 'id
            mode: 'local
            triggerAction: 'all
            editable: fals
            width: config.width || 2
        }
    this.combo.setValue(0
    this.combo.on('select', function(combo, record, index)
        this.fireEvent('filter', this, this.getParams()
    }, this
    this.toolbarItems.add(this.combo


Ext.extend(Vps.Auto.GridFilter.ComboBox, Vps.Auto.GridFilter.Abstract,
    reset: function()
        this.combo.setValue(0
    
    getParams: function()
        var params = {
        params['query_'+this.id] = this.combo.getValue(
        return param
   
}

