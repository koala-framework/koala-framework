Ext.ns('Kwf.Maintenance');
Kwf.Maintenance.Update = Ext.extend(Ext.Panel, {
    border: false,
    initComponent: function() {
        this.layout = 'border';
        this.grid = new Kwf.Auto.GridPanel({
            controllerUrl: '/kwf/maintenance/update',
            region: 'center',
            buttons: [{
                text: trlKwf('Update'),
                handler: function() {
                    Ext.Ajax.request({
                        url: '/kwf/maintenance/update/json-execute-updates',
                        progress: true,
                        scope: this,
                        success: function() {
                            this.grid.reload();
                        }
                    });
                },
                scope: this
            }]
        });
        this.items = [
            this.grid
        ];

        Kwf.Maintenance.Update.superclass.initComponent.call(this);
    }
});
Ext.reg('kwf.maintenance.update', Kwf.Maintenance.Update);
