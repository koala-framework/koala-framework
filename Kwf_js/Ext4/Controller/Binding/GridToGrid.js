Ext4.define('Kwf.Ext4.Controller.Binding.GridToGrid', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        this.grid.disable();
        this.source.on('selectionchange', function(model, rows) {
            if (rows[0]) {
                var row = rows[0];
                var store = row[this.relation]();
                if (!store.loaded) {
                    store.loaded = true;
                    if (!row.phantom) {
                        store.load();
                    }
                }
                this.grid.bindStore(store);
                this.grid.enable();
            } else {
                this.grid.disable();
            }
        }, this);
        this.source.on('beforedeselect', function(sm, record) {
            //TODO if dirty return false
        }, this);
    }
});
