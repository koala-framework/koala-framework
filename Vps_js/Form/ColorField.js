Vps.Form.ColorField = Ext.extend(Ext.form.Field,
{
    defaultAutoCreate : {tag: "input", type: "hidden"},
    
    afterRender: function(){
        Vps.Form.ColorField.superclass.afterRender.call(this);
        span = Ext.DomHelper.insertAfter(this.el, '<span></span>');
        config = this.initialConfig;
        config.renderTo = span;
        this.cp = new Ext.ColorPalette(config);
        this.cp.on('select', function(palette, color) {
            oldValue= this.getValue();
            if (oldValue != color) {
                this.setValue(color);
                this.fireEvent('change', this, color, oldValue);
            }
        }, this);
    },
    
    setValue: function(value){
        if (value) {
            this.cp.select(value);
        } else {
            //todo: deselect?!
        }
        Vps.Form.ColorField.superclass.setValue.call(this, value);
    }
});
Ext.reg('colorfield', Vps.Form.ColorField);
