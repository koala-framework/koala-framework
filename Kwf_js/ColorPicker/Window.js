Kwf.ColorPickerWindow = Ext2.extend(Ext2.Window,
{
    initComponent: function() {
        this.content = new Ext2.Panel({
            cls: 'kwf-overview-panel',
            border: false,
            autoScroll: true,
            bodyStyle: 'padding: 10px;',
            region: 'center'
        });

        this.picker = new Kwf.ColorPicker.Panel({
            title: "Color Picker",
            titleAsText: true,
            applyTo: "color-picker"
        });

        this.items = [this.content];
        Kwf.ColorPicker.Window.superclass.initComponent.call(this);
    }
});