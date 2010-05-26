Ext.namespace('Vpc.Abstract.List');
Vpc.Abstract.List.PanelWithEditButton = Ext.extend(Vps.Auto.FormPanel, {
    initComponent: function() {
        this.addEvents('editcomponent', 'gotComponentConfigs');
        this.fireEvent('gotComponentConfigs', this.componentConfigs);

        Vpc.Abstract.List.PanelWithEditButton.superclass.initComponent.call(this);
    },

    onSubmitSuccess: function(response, options, result) {
        Vpc.Abstract.List.PanelWithEditButton.superclass.onSubmitSuccess.apply(this, arguments);
        this.reload();
    }
});

Ext.reg('vpc.listwitheditbutton', Vpc.Abstract.List.PanelWithEditButton);
