Vps.Debug.Requests = Ext.extend(Ext.grid.GridPanel, {
    initComponent: function()
    {
        this.columns = [
            {header: 'Time', sortable: true, dataIndex: 'time',
            renderer: function(v) {
                //kein dateRenderer damit utils/Date nicht geladen sein muss
                return v.getHours()+':'+v.getMinutes()+':'+v.getSeconds();
            },width: 70},
            {header: 'Url', dataIndex: 'url', width: 250},
            {header: 'Queries', sortable: true, dataIndex: 'queries', width: 40},
            {header: 'Rows', sortable: true, dataIndex: 'rows', width: 40},
            {header: 'Explain', sortable: true, dataIndex: 'explainRows', width: 50},
            {header: '', width: 25,
                renderer: function(value, p, record, rowIndex, colIndex, store, column) {
                    p.css += 'vps-cell-button';
                    p.attr += 'style="background-image:url(/assets/silkicons/application_link.png);" ';
                    return '';
                }
            }
        ];
        this.on('cellclick', function(grid, rowIndex, columnIndex) {
            var record = grid.getStore().getAt(rowIndex);  // Get the Record
            if (columnIndex == 5) {
                window.open(record.get('url')+'?'+record.get('params'));
            }
        }, this);

        this.on('render', function() {
            this.getSelectionModel().selectFirstRow();
        }, this);

        this.actions = {};
        this.actions.reload = new Ext.Action({
            icon: '/assets/silkicons/arrow_rotate_clockwise.png',
            cls: 'x-btn-text-icon',
            text: 'Load Querycount',
            handler: this.loadQueryCount,
            scope: this
        });
        this.actions.clear = new Ext.Action({
            icon: '/assets/silkicons/cross.png',
            cls: 'x-btn-text-icon',
            text: 'Clear',
            handler: function() {
                this.store.removeAll();
            },
            scope: this
        });
        this.tbar = [this.actions.reload, this.actions.clear];

        Vps.Debug.Requests.superclass.initComponent.call(this);
    },
    loadQueryCount: function()
    {
        var requestNums = [];
        this.store.each(function(r) {
            requestNums.push(r.get('requestNum'));
        }, this);
        Ext.Ajax.request({
            url: '/vps/debug/sql/json-querycount',
            params: { requestNums: requestNums.join(';') },
            success: function(resonse, options, result) {
                this.store.each(function(r) {
                    result.data.forEach(function(d) {
                        if (d.requestNum == r.get('requestNum')) {
                            r.set('queries', d.queries);
                            r.set('rows', d.rows);
                            r.set('explainRows', d.explainRows);
                            return false;
                        }
                    });
                }, this);
                this.store.commitChanges();
            },
            scope: this
        });
    }
});
