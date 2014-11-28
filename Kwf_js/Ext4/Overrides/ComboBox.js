Ext4.define('Kwf.Ext4.Overrides.ComboBox', {
    override: 'Ext.form.field.ComboBox',
    showNoSelection: false,

    initComponent: function()
    {
        if (this.showNoSelection && !this.emptyText) {
            this.emptyText = '('+trlKwf('no selection')+')';
        }
        this.callParent(arguments);
    },

    onBindStore: function(store, initial)
    {
        this.callParent(arguments);
        this._addNoSelection(store);
        store.on('load', this._addNoSelection, this);
    },

    onUnbindStore: function(store, initial)
    {
        this.callParent(arguments);
        store.un('load', this._addNoSelection, this);
    },

    _addNoSelection : function(store)
    {
        if (!store) store = this.getStore();
        if (this.showNoSelection && store.find(this.valueField, null) == -1) {
            var row = new store.model();
            row.set(this.displayField, this.emptyText);
            row.emptyValue = true;
            store.insert(0, row);
        }
    },

    setValue: function(value, doSelect)
    {
        value = Ext4.Array.from(value);
        for (i = 0; i < value.length; i++) {
            var record = value[i];
            if (record.isModel && record.emptyValue) {
                value.splice(i, 1);
                i--;
            }
        }
        arguments[0] = value;
        this.callParent(arguments);
    }
});
