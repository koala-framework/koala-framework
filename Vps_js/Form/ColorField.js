Vps.Form.ColorField = Ext.extend(Ext.form.Fiel

    defaultAutoCreate : {tag: "input", type: "hidden"
  
    afterRender: function(
        Vps.Form.ColorField.superclass.afterRender.call(this
        span = Ext.DomHelper.insertAfter(this.el, '<span></span>'
        config = this.initialConfi
        config.renderTo = spa
        this.cp = new Ext.ColorPalette(config
        this.cp.on('select', function(palette, color)
            if (this.getValue() != color)
                this.setValue(color
           
        }, this
    
  
    setValue: function(value
        if (value != '')
            this.cp.select(value
       
        Vps.Form.ColorField.superclass.setValue.call(this, value
   
}
Ext.reg('colorfield', Vps.Form.ColorField
