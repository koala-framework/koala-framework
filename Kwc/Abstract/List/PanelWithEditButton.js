Ext2.namespace('Kwc.Abstract.List');
Kwc.Abstract.List.PanelWithEditButton = Ext2.extend(Kwf.Auto.FormPanel, {
    initComponent: function() {
        this.addEvents('editcomponent', 'gotComponentConfigs');
        this.fireEvent('gotComponentConfigs', this.componentConfigs);

        Kwc.Abstract.List.PanelWithEditButton.superclass.initComponent.call(this);
    },

    onSubmitSuccess: function(response, options, result) {
        Kwc.Abstract.List.PanelWithEditButton.superclass.onSubmitSuccess.apply(this, arguments);
        this.reload();
    }
});

Ext2.reg('kwc.listwitheditbutton', Kwc.Abstract.List.PanelWithEditButton);
