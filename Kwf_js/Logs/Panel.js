Ext2.namespace("Kwf.Logs");
Kwf.Logs.Panel = Ext2.extend(Ext2.Panel, {
    initComponent: function() {
        var form = new Kwf.Auto.FormPanel({
            controllerUrl: this.formControllerUrl,
            region: 'east',
            split: true,
            width: 550
        });
        var grid = new Kwf.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            region: 'center',
            bindings: [ form ]
        });
        grid.actions.deleteAll = new Ext2.Action({
            text: trlKwf('Delete All'),
            icon: '/assets/silkicons/bin_empty.png',
            cls: 'x-btn-text-icon',
            handler: function(){
                Ext2.Msg.confirm(
                    trlKwf('Are you sure?'),
                    trlKwf('Do you really want to delete all logs?'),
                    function(result) {
                        if (result == 'yes') {
                            Ext2.Ajax.request({
                                url : this.controllerUrl + '/json-delete-all',
                                params: grid.getBaseParams(),
                                success: function(response, options, r) {
                                    Ext2.MessageBox.alert(trlKwf('Status'), r.message);
                                    grid.reload();
                                },
                                scope: this
                            });
                        }
                    },
                    this
                );
            },
            scope: this
        });
        grid.actions.parse = new Ext2.Action({
            text: trlKwf('Parse files'),
            icon: '/assets/silkicons/magnifier.png',
            cls: 'x-btn-text-icon',
            handler: function() {
                Ext2.Ajax.request({
                    url : this.controllerUrl + '/json-parse-files',
                    params: grid.getBaseParams(),
                    success: function(response, options, r) {
                        Ext2.MessageBox.alert(trlKwf('Status'), r.message);
                        grid.reload();
                    },
                    scope: this
                });
            },
            scope: this
        });
        grid.actions.deleteFiles = new Ext2.Action({
            text: trlKwf('Delete Files'),
            icon: '/assets/silkicons/script_delete.png',
            cls: 'x-btn-text-icon',
            handler: function(){
                Ext2.Msg.confirm(
                    trlKwf('Are you sure?'),
                    trlKwf('Do you really want to delete all parsed Files?'),
                    function(result) {
                        if (result == 'yes') {
                            Ext2.Ajax.request({
                                url : this.controllerUrl + '/json-delete-files',
                                params: grid.getBaseParams(),
                                success: function(response, options, r) {
                                    Ext2.MessageBox.alert(trlKwf('Status'), r.message);
                                },
                                scope: this
                            });
                        }
                    },
                    this
                );
            },
            scope: this
        });


        this.layout = 'border';
        this.items = [ grid, form ];

        Kwf.Logs.Panel.superclass.initComponent.call(this);
    }
});

Ext2.reg('kwf.logs.panel', Kwf.Logs.Panel);
