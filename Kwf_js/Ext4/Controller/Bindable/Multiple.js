Ext4.define('Kwf.Ext4.Controller.Bindable.Multiple', {
    extend: 'Kwf.Ext4.Controller.Bindable.Abstract',

    items: null,
    panel: null, //optional

    init: function()
    {
        if (this.panel && !(this.panel instanceof Ext4.panel.Panel)) Ext4.Error.raise('panel config needs to be a Ext.panel.Panel');
        if (!this.items) Ext4.Error.raise('items config is required');
        if (!(this.items instanceof Array)) Ext4.Error.raise('items config needs to be an array');
        if (this.items.length < 1) Ext4.Error.raise('items config length needs to be >0');
    },

    load: function(row)
    {
        Ext4.each(this.items, function(i) {
            i.load(row);
        }, this);
    },

    reset: function()
    {
        Ext4.each(this.items, function(i) {
            i.reset();
        }, this);
    },

    isDirty: function()
    {
        var ret = false;
        Ext4.each(this.items, function(i) {
            if (i.isDirty()) {
                ret = true;
                return false;
            }
        }, this);
        return ret;
    },

    isValid: function()
    {
        var ret = true;
        Ext4.each(this.items, function(i) {
            if (!i.isValid()) {
                ret = false;
                return false;
            }
        }, this);
        return ret;
    },

    save: function(syncQueue)
    {
        Ext4.each(this.items, function(i) {
            i.save(syncQueue);
        }, this);
    },

    getLoadedRecord: function()
    {
        return this.items[0].getLoadedRecord();
    },

    enable: function()
    {
        if (this.panel) this.panel.enable();
        Ext4.each(this.items, function(i) {
            i.enable();
        }, this);
    },
    disable: function()
    {
        if (this.panel) this.panel.disable();
        Ext4.each(this.items, function(i) {
            i.disable();
        }, this);
    },
    getPanel: function()
    {
        return this.panel;
    },
    onAdd: function()
    {
        var ret = false;
        Ext4.each(this.items, function(i) {
            if (i.onAdd()) {
                ret = true;
                return false;
            }
        }, this);
        return ret;
    }
});
