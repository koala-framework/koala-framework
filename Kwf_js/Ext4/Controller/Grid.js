Ext4.define('Kwf.Ext4.Controller.Grid', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    autoSync: true,
    autoLoad: false,

    _store: null,
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        var grid = this.grid;
        if (!this.deleteButton) this.deleteButton = grid.down('button#delete');
        if (this.deleteButton) this.deleteButton.disable();
        grid.on('selectionchange', function(model, rows) {
            if (rows[0]) {
                var row = rows[0];
                if (this.deleteButton) this.deleteButton.enable();
            } else {
                if (this.deleteButton) this.deleteButton.disable();
            }
        }, this);
        if (this.deleteButton) {
            this.deleteButton.disable();
            this.deleteButton.on('click', this.onDeleteClick, this);
        }
        Ext4.each(grid.query('> toolbar[dock=top] field'), function(field) {
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

    onDeleteClick: function()
    {
        if (this.autoSync) {
            Ext4.Msg.show({
                title: trlKwf('Delete'),
                msg: trlKwf('Do you really wish to remove this entry?'),
                buttons: Ext4.Msg.YESNO,
                scope: this,
                fn: function(button) {
                    if (button == 'yes') {
                        this.deleteSelected();
                    }
                }
            });
        } else {
            this.deleteSelected();
        }
    },

    deleteSelected: function(options)
    {
        this.grid.getStore().remove(this.grid.getSelectionModel().getSelection());
        if (this.autoSync) {
            var syncOptions = {};
            if (options && options.syncOptions) syncOptions = options.syncOptions;
            this.grid.getStore().sync(syncOptions);
        }
    },

    onBindStore: function()
    {
        if (this._store) {
            this._store.un('write', this.onStoreWrite, this);
        }

        var s = this.grid.getStore();
        this._store = s;
        Ext4.each(this.grid.query('pagingtoolbar'), function(i) {
            i.bindStore(s);
        }, this);
        Ext4.each(this.grid.query('> toolbar[dock=top] field'), function(field) {
            var filterId = 'filter-'+field.getName();
            var v = field.getValue();
            this.grid.getStore().addFilter({
                id: filterId,
                property: field.getName(),
                value: v
            }, false);
        }, this);

        this.fireEvent('bindstore', s);

        s.on('write', this.onStoreWrite, this);
    },


    onStoreWrite: function(store, operation)
    {
        this.fireEvent('write', store, operation);
    }
});
