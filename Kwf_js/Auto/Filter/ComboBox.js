Kwf.Auto.Filter.ComboBox = function(config)
{
    Kwf.Auto.Filter.ComboBox.superclass.constructor.call(this, config);

    if (config.field) {
        this.combo = Ext2.ComponentMgr.create(config.field);
    } else {
        var record = Ext2.data.Record.create(['id', 'name', 'displayname']);
        var filterData = config.data;
        Ext2.each(filterData, function(item, i){
            filterData[i][2] = filterData[i][1];
            filterData[i][1] = filterData[i][1].toString();
            var a = [];
            while (filterData[i][1].substr(a.length, 1) == ' ') {
                a.push('&nbsp;');
            }
            filterData[i][1] = a.join('') + filterData[i][1].substr(a.length);
        });
        var filterStore = new Ext2.data.Store({
            reader: new Ext2.data.ArrayReader({}, record),
            data: filterData
        });
        if (!config['default'] && filterStore.find('id', 0) == -1) {
            var name = config['defaultText'] ? config['defaultText'] : trlKwf('all');
            filterStore.insert(0, [new record({
                id: 0,
                name: name,
                displayname: name
            })]);
            config['default'] = 0;
        }
        this.combo = new Ext2.form.ComboBox({
            store: filterStore,
            displayField: 'displayname',
            valueField: 'id',
            mode: 'local',
            triggerAction: 'all',
            editable: config.editable || false,
            forceSelection: true,
            width: config.width || 200,
            listWidth: config.listWidth,
            tpl: new Ext2.XTemplate(
                '<tpl for=".">',
                    '<div class="x2-combo-list-item">{name:htmlEncode}</div>',
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

Ext2.extend(Kwf.Auto.Filter.ComboBox, Kwf.Auto.Filter.Abstract, {
    reset: function() {
        this.combo.setValue(0);
    },
    getParams: function(paramName) {
        var params = {};
        params[paramName] = this.combo.getValue();
        return params;
    }
});

