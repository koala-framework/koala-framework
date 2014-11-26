Kwf.Auto.Filter.TextColumn = function(config)
{
    Kwf.Auto.Filter.TextColumn.superclass.constructor.call(this, config);

    var record = Ext2.data.Record.create(['id', 'name']);
    var filterStore = new Ext2.data.Store({
        reader: new Ext2.data.ArrayReader({}, record),
        data: config.data
    });
    if (!config['default'] && filterStore.find('id', 0) == -1) {
        filterStore.insert(0, [new record({id: 0, name: config['defaultText'] ? config['defaultText'] : trlKwf('all')})]);
        config['default'] = 0;
    }
    this.combo = new Ext2.form.ComboBox({
        store: filterStore,
        displayField: 'name',
        valueField: 'id',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        width: config.width || 200
    });
    this.combo.setValue(config['default']);
    this.combo.on('select', function() {
        this.fireEvent('filter', this, this.getParams(config.paramName));
    }, this);
    this.toolbarItems.add((config.columnsLabel || trlKwf('Column')+':'));
    this.toolbarItems.add(this.combo);
};

Ext2.extend(Kwf.Auto.Filter.TextColumn, Kwf.Auto.Filter.Text, {
    reset: function() {
        this.textField.reset();
        this.combo.setValue(0);
    },
    getParams: function() {
        var params = {};
        params[this.paramName+'_column'] = this.combo.getValue();
        params[this.paramName+'_text'] = this.textField.getValue();
        return params;
    }
});
