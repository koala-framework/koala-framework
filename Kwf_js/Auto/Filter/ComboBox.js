Kwf.Auto.Filter.ComboBox = function(config)
{
    Kwf.Auto.Filter.ComboBox.superclass.constructor.call(this, config);

    if (config.field) {
        this.combo = Ext.ComponentMgr.create(config.field);
    } else {
        var record = Ext.data.Record.create(['id', 'name', 'displayname']);
        var filterData = config.data;
        for (var i in filterData) {
            filterData[i][2] = filterData[i][1];
            if (filterData[i][1]) {
                var a = [];
                while (filterData[i][1].substr(a.length, 1) == ' ') {
                    a.push('&nbsp;');
                }
                filterData[i][1] = a.join('') + filterData[i][1].substr(a.length);
            }
        }
        var filterStore = new Ext.data.Store({
            reader: new Ext.data.ArrayReader({}, record),
            data: filterData
        });
        if (!config['default'] && filterStore.find('id', 0) == -1) {
            filterStore.insert(0, [new record({id: 0, name: config['defaultText'] ? config['defaultText'] : trlKwf('all')})]);
            config['default'] = 0;
        }
        this.combo = new Ext.form.ComboBox({
            store: filterStore,
            displayField: 'displayname',
            valueField: 'id',
            mode: 'local',
            triggerAction: 'all',
            editable: config.editable || false,
            forceSelection: true,
            width: config.width || 200,
            listWidth: config.listWidth,
            tpl: new Ext.XTemplate(
                '<tpl for=".">',
                    '<div class="x-combo-list-item">{name}</div>',
                '</tpl>'
            )
        });
    }
    this.combo.setValue(config['default']);
    this.combo.on('select', function() {
        this.fireEvent('filter', this, this.getParams(config.paramName));
    }, this);
    this.toolbarItems.add(this.combo);
};

Ext.extend(Kwf.Auto.Filter.ComboBox, Kwf.Auto.Filter.Abstract, {
    reset: function() {
        this.combo.setValue(0);
    },
    getParams: function(paramName) {
        var params = {};
        params[paramName] = this.combo.getValue();
        return params;
    }
});

