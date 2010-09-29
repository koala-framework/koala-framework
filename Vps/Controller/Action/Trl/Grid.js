Ext.namespace('Vps.Trl');

Vps.Trl.Grid = Ext.extend(Vps.Auto.GridPanel, {
    initComponent : function()
    {
        Vps.Trl.Grid.superclass.initComponent.call(this);
        this.on('loaded', function() {
            this.getEditDialog().getAutoForm().on('beforeloadform', function(form, data) {
                form.items.items.each(function(value, index) {
                    if (!data[value.name] && !data[this.language+'_plural'] && value.name.match(/.*_plural/)) {
                        value.hide();

                    }
                }, this);
            }, this);
        }, this);
    }
});