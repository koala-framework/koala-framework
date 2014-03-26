Ext4.define('Kwf.Ext4.Controller.Bindable.GridBinding', {
    extend: 'Kwf.Ext4.Controller.Bindable.Grid',

    bindableToGridController: null,
    panel: null,

    init: function()
    {
        if (!this.bindableToGridController) Ext4.Error.raise('bindableToGridController config is required');
        if (!this.bindableToGridController instanceof Kwf.Ext4.Controller.Binding.BindableToGrid) Ext4.Error.raise('bindableToGridController config needs to be a Kwf.Ext4.Controller.Binding.BindableToGrid');
        if (this.panel && !this.panel instanceof Ext4.panel.Panel) Ext4.Error.raise('panel config needs to be a Ext.panel.Panel');

        this.callParent(arguments);
        if (this.reloadRowOnSave) {
            this.bindableToGridController.on('savesuccess', function() {
                this._reloadLoadedRow();
            }, this);
        }
    },

    reset: function()
    {
        this.callParent(arguments);
        this.bindableToGridController.reset();
    },

    isDirty: function()
    {
        if (this.callParent(arguments)) {
            return true;
        }
        return this.bindableToGridController.isDirty();
    },

    isValid: function()
    {
        if (this.callParent(arguments)) {
            return true;
        }
        return this.bindableToGridController.isValid();
    },

    save: function(syncQueue)
    {
        this.bindableToGridController.save(syncQueue);
    },

    enable: function()
    {
        if (this.panel) this.panel.enable();
        this.bindableToGridController.gridController.grid.enable();
    },
    disable: function()
    {
        if (this.panel) this.panel.disable();
        this.bindableToGridController.gridController.grid.disable();
    },
    getPanel: function()
    {
        return this.panel;
    }
});
