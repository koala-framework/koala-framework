Ext4.define('Kwf.Ext4.Controller.Bindable.GridBinding', {
    extend: 'Kwf.Ext4.Controller.Bindable.Grid',

    bindableToGridController: null,
    panel: null,

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
