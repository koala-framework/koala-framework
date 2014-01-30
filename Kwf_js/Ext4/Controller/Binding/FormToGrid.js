Ext4.define('Kwf.Ext4.Controller.Binding.FormToGrid', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    focusOnAddSelector: 'field',
    updateOnChange: false,
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        var grid = this.source;
        var form = this.form;
        form.disable();
        form.getForm().trackResetOnLoad = true;

        if (!this.formSaveButton) this.formSaveButton = form.down('button#save');
        if (!this.formDeleteButton) this.formDeleteButton = form.down('button#delete');
        if (!this.gridAddButton) this.gridAddButton = grid.down('button#add');

        grid.on('selectionchange', function(model, rows) {
            if (rows[0]) {
                var row = rows[0];
                if (form.getRecord() !== row) {
                    form.getForm().loadRecord(row);
                }
                form.enable();
            } else {
                form.disable();
            }
        }, this);
        grid.on('beforeselect', function(sm, record) {
            if (form.getRecord() !== record && form.getForm().isDirty()) {
                Ext4.Msg.show({
                    title: trlKwf('Save'),
                    msg: trlKwf('Do you want to save the changes?'),
                    buttons: Ext4.Msg.YESNOCANCEL,
                    scope: this,
                    fn: function(button) {
                        if (button == 'yes') {
                            if (this.doSave()) {
                                form.getForm().reset(true);
                                grid.getSelectionModel().select(record);
                            } else {
                                //validation failed re-select
                                grid.getSelectionModel().select(form.getRecord());
                            }
                        } else if (button == 'no') {
                            form.getForm().reset(true);
                            grid.getSelectionModel().select(record);
                        } else if (button == 'cancel') {
                            grid.getSelectionModel().select(form.getRecord());
                        }
                    }
                });
                return false;
            }
        }, this);

        if (this.updateOnChange) {
            Ext4.each(form.query('field'), function(i) {
                i.on('change', function() {
                    this.form.updateRecord();
                }, this);
            }, this);
        }

        if (this.formSaveButton) {
            this.formSaveButton.on('click', function() {
                this.doSave();
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

                form.down(this.focusOnAddSelector).focus();
                this.fireEvent('add');
            }, this);
        }
        if (this.formDeleteButton) {
            this.formDeleteButton.on('click', function() {
                Ext4.Msg.show({
                    title: trlKwf('Delete'),
                    msg: trlKwf('Do you really wish to remove this entry?'),
                    buttons: Ext4.Msg.YESNO,
                    scope: this,
                    fn: function(button) {
                        if (button == 'yes') {
                            grid.getStore().remove(form.getRecord());
                            grid.getStore().sync();
                        }
                    }
                });

            }, this);
        }
    },
    doSave: function()
    {
        if (!this.form.getForm().isValid()) {
            Ext4.Msg.alert(trlKwf('Save'),
                trlKwf("Can't save, please fill all red underlined fields correctly."));
            return false;
        }
        var row = this.form.getRecord();
        this.form.updateRecord(row);

        //trackResetOnLoad only calls resetOriginalValue on load, not on updateRecord
        Ext4.each(row.fields.items, function(field) {
            var f = this.form.getForm().findField(field.name);
            if (f) {
                f.resetOriginalValue();
            }
        }, this);

        this.source.getStore().sync();

        this.fireEvent('save');

        return true;
    }
});
