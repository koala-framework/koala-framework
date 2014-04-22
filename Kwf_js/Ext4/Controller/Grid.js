Ext4.define('Kwf.Ext4.Controller.Grid', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    uses: [ 'Ext.window.MessageBox' ],
    autoSync: true,
    autoLoad: false,

    grid: null,

    _store: null,
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        if (!this.grid) Ext4.Error.raise('grid config is required');
        if (!this.grid instanceof Ext4.grid.Panel) Ext4.Error.raise('grid config needs to be a Ext.grid.Panel');
        var grid = this.grid;

        if (typeof this.deleteButton == 'undefined') this.deleteButton = grid.down('button#delete');
        if (this.deleteButton && !this.deleteButton instanceof Ext4.button.Button) Ext4.Error.raise('deleteButton config needs to be a Ext.button.Button');
        if (this.deleteButton) {
            this.deleteButton.disable();
            this.deleteButton.on('click', this.onDeleteClick, this);
        }

        if (typeof this.exportCsvButton == 'undefined') this.exportCsvButton = grid.down('button#exportCsv');
        if (this.exportCsvButton && !this.exportCsvButton instanceof Ext4.button.Button) Ext4.Error.raise('exportCsvButton config needs to be a Ext.button.Button');
        if (this.exportCsvButton) {
            this.exportCsvButton.on('click', this.csvExport, this);
        }

        grid.on('selectionchange', function(model, rows) {
            if (rows[0]) {
                var row = rows[0];
                if (this.deleteButton) this.deleteButton.enable();
            } else {
                if (this.deleteButton) this.deleteButton.disable();
            }
        }, this);
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

    onDeleteClick: function(options)
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
                        if (options.callback) options.callback.call(options.scope || this);
                    }
                }
            });
        } else {
            this.deleteSelected();
            if (options.callback) options.callback.call(options.scope || this);
        }
    },

    deleteSelected: function()
    {
        this.grid.getStore().remove(this.grid.getSelectionModel().getSelection());
        if (this.autoSync) {
            this.grid.getStore().sync({
                success: function() {
                    this.fireEvent('savesuccess');
                },
                scope: this
            });
            this.fireEvent('save');
        }
    },

    onBindStore: function()
    {
        var s = this.grid.getStore();
        this._store = s;
        Ext4.each(this.grid.query('pagingtoolbar'), function(i) {
            i.bindStore(s);
        }, this);
        Ext4.each(this.grid.query('> toolbar[dock=top] field'), function(field) {
            var filterId = 'filter-'+field.getName();
            var v = field.getValue();
            if (typeof v == 'undefined') v = null;
            this.grid.getStore().addFilter({
                id: filterId,
                property: field.getName(),
                value: v
            }, false);
        }, this);

        this.fireEvent('bindstore', s);
    },

    csvExport: function()
    {
        var csv = '';

        //header
        var sep = '';
        Ext4.each(this.grid.columns, function(col) {
            if (!col.dataIndex) return;
            csv += sep+col.text;
            sep = ';';
        }, this);
        csv += '\n';


        var pageSize = 25;
        var totalCount = this._store.getTotalCount();
        var pageCount = Math.ceil(totalCount / pageSize);
        var page = 1;

        //create own store, so grid doesn't display loaded rows
        var store = this._store.self.create({
            filters: this._store.filters.items,
            sorters: this._store.sorters.items,
            pageSize: pageSize
        });

        Ext4.Msg.show({
            title: trlKwf('Export'),
            msg: trlKwf('Exporting rows...'),
            progress: true,
            buttons: Ext4.Msg.CANCEL
        });

        loadPage.call(this);

        function loadPage()
        {
            if (!Ext4.Msg.isVisible()) return; //export cancelled
            Ext4.Msg.updateProgress((page-1)/pageCount);
            store.loadPage(page, {
                callback: function() {
                    exportRows.call(this);
                    if (page < pageCount) {
                        page++;
                        loadPage.call(this);
                    } else {
                        Ext4.Msg.updateProgress(1);
                        createDownload.call(this);
                    }
                },
                scope: this
            });
        }

        function exportRows()
        {
            store.each(function(row) {
                var sep = '';
                Ext4.each(this.grid.columns, function(col) {
                    if (!col.dataIndex) return;
                    var val = row.get(col.dataIndex);
                    if (col.renderer) {
                        val = Ext.util.Format.stripTags(col.renderer(val, col, row));
                    }
                    if (!val) val = '';
                    csv += sep;
                    csv += String(val).replace('\\', '\\\\').replace(';', '\;').replace('\n', '\\n');
                    sep = ';';
                }, this);
                csv += '\n';
            }, this);
        }

        function createDownload()
        {
            //TODO IE8 compatibility
            var URL = window.URL || window.webkiURL;
            var blob = new Blob([csv]);
            var blobURL = URL.createObjectURL(blob);
            var a = this.grid.el.createChild({
                tag: 'a',
                href: blobURL,
                style: 'display:none;',
                download: 'export.csv'
            });
            a.dom.click();
            a.remove();
            Ext4.Msg.hide();
        }
    }
});
