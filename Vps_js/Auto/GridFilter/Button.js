Vps.Auto.GridFilter.Button = function(confi

    Vps.Auto.GridFilter.Button.superclass.constructor.call(this, config

    this.button = new Ext.Button
        icon: config.ico
        text: config.tex
        cls: config.cl
        enableToggle: tr
    }
    this.button.on('toggle', function()
        this.fireEvent('filter', this, this.getParams()
    }, this
    this.toolbarItems.add(this.button


Ext.extend(Vps.Auto.GridFilter.Button, Vps.Auto.GridFilter.Abstract,
    reset: function()
        this.button.toggle(false
    
    getParams: function()
        var params = {
        params['query_'+this.id] = this.button.pressed ? 1 : 
        return param
   
}
