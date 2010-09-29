Vpc.Basic.Text.StylesEditorTab = Ext.extend(Ext.Panel,
{
    layout: 'border',
    initComponent: function()
    {
        this.form = new Vps.Auto.FormPanel({
            controllerUrl: this.controllerUrl,
            region: 'center',
            autoLoad: true
        });
        this.grid = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl+'s',
            region: 'west',
            width: 250,
            split: true,
            autoLoad: false,
            bindings: [this.form]
        });
        this.items = [this.form, this.grid];
        Vpc.Basic.Text.StylesEditorTab.superclass.initComponent.call(this);
    },
    applyBaseParams: function(params) {
        this.form.applyBaseParams(params);
        this.grid.applyBaseParams(params);
    },
    load: function() {
        this.grid.load();
        this.grid.clearSelections();
        this.form.disable();
    }
});
