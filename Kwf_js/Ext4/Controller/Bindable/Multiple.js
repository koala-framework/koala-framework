Ext4.define('Kwf.Ext4.Controller.Bindable.Multiple', {
    extend: 'Kwf.Ext4.Controller.Bindable.Abstract',

    items: null,
    panel: null, //optional

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

    save: function()
    {
        Ext4.each(this.items, function(i) {
            i.save();
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
    }
});
