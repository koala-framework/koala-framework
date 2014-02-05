Ext4.define('Kwf.Ext4.Controller.Grid', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    autoSync: true,
    autoLoad: false,
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        var grid = this.grid;
        if (!this.gridDeleteButton) this.gridDeleteButton = grid.down('button#delete');
        grid.on('selectionchange', function(model, rows) {
            if (rows[0]) {
                var row = rows[0];
                if (this.gridDeleteButton) this.gridDeleteButton.enable();
            } else {
                if (this.gridDeleteButton) this.gridDeleteButton.disable();
            }
        }, this);
        if (this.gridDeleteButton) {
            this.gridDeleteButton.disable();
            this.gridDeleteButton.on('click', function() {
                var sm = grid.getSelectionModel();
                grid.getStore().remove(sm.getSelection());
                if (this.autoSync) grid.getStore().sync();
            }, this);
        }
        grid.query('> toolbar[dock=top] field').each(function(field) {
            field.on('change', function() {
                var filterId = 'filter-'+field.getName();
                var v = field.getValue();
                var filter = this.grid.getStore().filters.get(filterId);
                if (!filter || filter.value != v) {
                    this.grid.getStore().addFilter({
                        id: filterId,
                        property: field.getName(),
                        value: v
                    });
                }
            }, this, { buffer: 300 });
        }, this);

        if (grid.getStore()) this.onBindStore();
        Ext4.Function.interceptAfter(grid, "bindStore", this.onBindStore, this);

        if (this.autoLoad) {
            this.grid.getStore().load();
        }
    },
    onBindStore: function()
    {
        var s = this.grid.getStore();
        this.grid.query('pagingtoolbar').each(function(i) {
            i.bindStore(s);
        }, this);
        this.grid.query('> toolbar[dock=top] field').each(function(field) {
            var filterId = 'filter-'+field.getName();
            var v = field.getValue();
            this.grid.getStore().addFilter({
                id: filterId,
                property: field.getName(),
                value: v
            }, false);
        }, this);
    }
});
