Vps.Auto.Filter.ComboBox = function(config)
{
    Vps.Auto.Filter.ComboBox.superclass.constructor.call(this, config);

    if (config.field) {
        this.combo = Ext.ComponentMgr.create(config.field);
    } else {
        var record = Ext.data.Record.create(['id', 'name']);
        var filterStore = new Ext.data.Store({
            reader: new Ext.data.ArrayReader({}, record),
            data: config.data
        });
        if (!config['default'] && filterStore.find('id', 0) == -1) {
            filterStore.insert(0, [new record({id: 0, name: config['defaultText'] ? config['defaultText'] : trlVps('all')})]);
            config['default'] = 0;
        }
        this.combo = new Ext.form.ComboBox({
                store: filterStore,
                displayField: 'name',
                valueField: 'id',
                mode: 'local',
                triggerAction: 'all',
                editable: false,
                width: config.width || 200
            });
    }
    this.combo.setValue(config['default']);
    this.combo.on('select', function() {
        this.fireEvent('filter', this, this.getParams(config.paramName));
    }, this);
    this.toolbarItems.add(this.combo);
};

Ext.extend(Vps.Auto.Filter.ComboBox, Vps.Auto.Filter.Abstract, {
    reset: function() {
        this.combo.setValue(0);
    },
    getParams: function(paramName) {
        var params = {};
        params[paramName] = this.combo.getValue();
        return params;
    }
});

