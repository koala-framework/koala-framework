Vps.Form.ColorField = Ext.extend(Ext.form.TextField,
{
    defaultAutoCreate : {tag: "input", type: "hidden"},
    colorRendered: false,
    
    setValue: function(value){
        Vps.Form.ColorField.superclass.setValue.call(this, value);
        if (!this.colorRendered) {
            span = Ext.DomHelper.insertAfter(this.el, '<span></span>');
            config = this.initialConfig;
            config.value = value;
            config.renderTo = span;
            this.cp = new Ext.ColorPalette(config);
            this.cp.on('select', function(palette, color) {
                this.setValue(color);
            }, this)
            this.colorRendered = true;
        }
    }
});
Ext.reg('colorfield', Vps.Form.ColorField);
