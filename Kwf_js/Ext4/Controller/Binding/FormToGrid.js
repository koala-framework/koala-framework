Ext4.define('Kwf.Ext4.Controller.Binding.FormToGrid', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        var grid = this.source;
        var form = this.form;
        form.disable();

        if (!this.formSaveButton) this.formSaveButton = form.down('button#save');
        if (!this.gridAddButton) this.gridAddButton = grid.down('button#add');

        if (this.formSaveButton) this.formSaveButton.disable();

        grid.on('selectionchange', function(model, rows) {
            if (rows[0]) {
                var row = rows[0];
                form.getForm().loadRecord(row);
                form.enable();
                if (this.formSaveButton) this.formSaveButton.enable();
            } else {
                form.disable();
                if (this.formSaveButton) this.formSaveButton.disable();
            }
        }, this);
        grid.on('beforedeselect', function(sm, record) {
            if (!form.getForm().isValid()) {
                return false;
            }
        }, this);

        if (this.formSaveButton) {
            this.formSaveButton.on('click', function() {
                var row = form.getRecord();
                form.updateRecord(row);
                grid.getStore().sync();
            }, this);
        }
        if (this.gridAddButton) {
            this.gridAddButton.on('click', function() {
                if (!form.getForm().isValid()) {
                    return false;
                }
                var s = grid.getStore();
                var row = s.model.create();
                s.add(row);
                grid.getSelectionModel().select(row);

                form.down('field').focus();
            }, this);
        }
    }
});
