Vps.Debug.SqlExplain = Ext.extend(Ext.grid.GridPanel, {
    initComponent: function()
    {
        Ext.Ajax.request({
            url: '/vps/debug/sql/json-explain',
            params: { query: this.query },
            success: function(response, options, result) {
                this.store.loadData(result.data);
            },
            scope: this
        });
        

        this.store = new Ext.data.SimpleStore({
            fields: [
                {name: 'id', type: 'int'},
                {name: 'select_type'},
                {name: 'table'},
                {name: 'type'},
                {name: 'possible_keys'},
                {name: 'key'},
                {name: 'key_len', type: 'int'},
                {name: 'ref'},
                {name: 'rows', type: 'int'},
                {name: 'Extra'}
            ]
        });
        this.columns = [
            {header: '#', sortable: true, dataIndex: 'id', width: 20},
            {header: 'Select Type', dataIndex: 'select_type', width: 50},
            {header: 'Table', dataIndex: 'table', width: 100},
            {header: 'Join Type', dataIndex: 'type', width: 50},
            {header: 'Possible Keys', dataIndex: 'possible_keys', width: 80},
            {header: 'Key', dataIndex: 'key', width: 80},
            {header: 'Key Len', sortable: true, dataIndex: 'key_len', width: 50},
            {header: 'ref', dataIndex: 'ref', width: 40},
            {header: 'Rows', sortable: true, dataIndex: 'rows', width: 50},
            {header: 'Other Info', dataIndex: 'Extra', width: 100}
        ];

        Vps.Debug.SqlExplain.superclass.initComponent.call(this);
    }
});
