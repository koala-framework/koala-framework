Vps.Debug.SqlQueries = Ext.extend(Ext.grid.GridPanel, {
    initComponent: function()
    {
        this.store = new Ext.data.SimpleStore({
            fields: [
                {name: 'num', type: 'int'},
                {name: 'query'},
                {name: 'time', type: 'float'},
                {name: 'type'},
                {name: 'params'},
                {name: 'resultCount', type: 'int'},
                {name: 'explainRows', type: 'int'},
                {name: 'backtrace'}
            ]
        });
        this.columns = [
            {header: '#', sortable: true, dataIndex: 'num', width: 30},
            {header: 'Query', dataIndex: 'query', width: 500,
                renderer: function(v) {
                    return '<pre>'+v+'</pre>';
                }
            },
            {header: 'Time', sortable: true,  dataIndex: 'time', width: 70,
                renderer: function(v, p, record) {
                    if (v > 0.1) p.attr += 'style="color:red"';
                    return v;
                }
            },
            {header: 'rows', sortable: true, dataIndex: 'resultCount', width: 30},
            {header: 'explain', sortable: true, dataIndex: 'explainRows', width: 40,
                renderer: function(v) {
                    if (!v) return '';
                    return v;
                }
            },
            {header: '', width: 25,
                renderer: function(value, p, record, rowIndex, colIndex, store, column) {
                    p.css += 'vps-cell-button';
                    p.attr += 'style="background-image:url(/assets/silkicons/information.png);" ';
                    return '';
                }
            },
            {header: '', width: 25,
                renderer: function(value, p, record, rowIndex, colIndex, store, column) {
                    p.css += 'vps-cell-button';
                    p.attr += 'style="background-image:url(/assets/silkicons/script.png);" ';
                    return '';
                }
            },
            {header: '', width: 25,
                renderer: function(value, p, record, rowIndex, colIndex, store, column) {
                    p.css += 'vps-cell-button';
                    p.attr += 'style="background-image:url(/assets/silkicons/database_gear.png);" ';
                    return '';
                }
            }
        ];
        this.on('cellclick', function(grid, rowIndex, columnIndex) {
            var record = grid.getStore().getAt(rowIndex);  // Get the Record
            if (columnIndex == 5) {
                var msg = String.format('<pre>{0}</pre>', record.get('query'));
                if (record.get('params') != '') {
                    msg += String.format('<h3>Params:</h3><pre>{0}</pre>',
                            record.get('params'));
                }
                Ext.Msg.show({
                    title : 'SQL',
                    msg : msg,
                    buttons: Ext.Msg.OK,
                    width: 800,
                    resizable: true
                });
            } else if (columnIndex == 6) {
                Ext.Msg.show({
                    title : 'Backtrace',
                    msg : '<pre>'+record.get('backtrace')+'</pre>',
                    buttons: Ext.Msg.OK,
                    width: 800,
                    resizable: true
                });
            } else if (columnIndex == 7) {
                var win = new Ext.Window({
                    title: 'SQL Explain',
                    width: 650,
                    height: 300,
                    layout: 'fit',
                    items: new Vps.Debug.SqlExplain({
                        query: record.get('query')
                    }),
                    modal: true
                });
                win.show();
            }
        }, this);
        Vps.Debug.SqlQueries.superclass.initComponent.call(this);
    },
    load: function(requestNum) {
        Ext.Ajax.request({
            url: '/vps/debug/sql/json-data',
            params: { requestNum: requestNum },
            success: function(response, options, result) {
                this.store.loadData(result.data);
            },
            scope: this
        });
    }
});
