Ext.namespace('Vpc.Columns');
Vpc.Columns.Panel = Ext.extend(Vps.Auto.FormPanel, {
    initComponent: function() {
        this.addEvents('editcomponent', 'gotComponentConfigs');
        this.fireEvent('gotComponentConfigs', this.componentConfigs);

        Vpc.Columns.Panel.superclass.initComponent.call(this);
    },
    onSubmitSuccess: function(response, options, result) {
        Vpc.Columns.Panel.superclass.onSubmitSuccess.apply(this, arguments);
        this.reload();
    },

});

Ext.reg('vpc.columns', Vpc.Columns.Panel);
