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

    save: function()
    {
        this.bindableToGridController.save();
    },

    enable: function()
    {
        if (this.panel) this.panel.enable();
        this.bindableToGridController.source.enable();
    },
    disable: function()
    {
        if (this.panel) this.panel.disable();
        this.bindableToGridController.source.disable();
    },
    getPanel: function()
    {
        return this.panel;
    }
});
