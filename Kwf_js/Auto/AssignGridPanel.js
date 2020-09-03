Kwf.Auto.AssignGridPanel = Ext2.extend(Kwf.Binding.ProxyPanel, {

    gridAssignedControllerUrl: '',
    gridDataControllerUrl: '',
    textAssignActionUrl: null,
    assignAllLimit: 100,

    gridDataHeight: 410,

    assignActionUrl: '',
    gridDataParamName: 'foreign_keys',

    initComponent: function()
    {
        this.addEvents({
            'assigned': true
        });

        this.actions.assign = new Ext2.Action({
            text    : trlKwf('Assign'),
            icon    : '/assets/silkicons/table_relationship.png',
            cls     : 'x2-btn-text-icon',
            disabled: true,
            handler : this.onAssign,
            scope   : this
        });
        this.actions.assignAll = new Ext2.Action({
            text: trlKwf('Assign all'),
            icon: '/assets/silkicons/table_go.png',
            cls     : 'x2-btn-text-icon',
            handler : this.onAssignAll,
            scope   : this
        });

        if (this.assignActionUrl == '') {
            this.assignActionUrl = this.gridAssignedControllerUrl + '/json-assign';
        }

        this.gridAssigned = new Kwf.Auto.AssignedGridPanel({
            controllerUrl: this.gridAssignedControllerUrl,
            textAssignActionUrl: this.textAssignActionUrl,
            region: 'center',
            gridConfig: {
                selModel: new Ext2.grid.CheckboxSelectionModel()
            }
        });
        this.proxyItem = this.gridAssigned;

        this.gridData = new Kwf.Auto.GridPanel({
            region: 'south',
            split: true,
            height: this.gridDataHeight,
            controllerUrl: this.gridDataControllerUrl,
            gridConfig: {
                tbar: [ this.getAction('assign'), this.getAction('assignAll'), '-' ],
                selModel: new Ext2.grid.CheckboxSelectionModel()
            },
            autoLoad: this.autoLoad
        });

        this.gridAssigned.on('datachange', function() {
            this.gridData.reload();
        }, this);

        this.relayEvents(this.gridAssigned, ['datachange']);


        this.gridData.on('selectionchange', function() {
            if (this.gridData.getSelectionModel().getSelections()[0]) {
                this.getAction('assign').enable();
            } else {
                this.getAction('assign').disable();
            }
        }, this);

        this.gridData.on('deleterow', function() {
            this.gridAssigned.reload();
        }, this);

        this.gridData.on('datachange', function() {
            this.gridAssigned.reload();
        }, this);

        this.layout = 'border';

        this.items = [this.gridAssigned, this.gridData];

        Kwf.Auto.AssignGridPanel.superclass.initComponent.call(this);
    },

    onAssign : function()
    {
        if (!this.gridData.getSelectionModel().getSelections()) return;

        var selections = this.gridData.getSelectionModel().getSelections();
        var ids = [];
        for (var i in selections) {
            if (selections[i].id) {
                ids.push(selections[i].id);
            }
        }
        this._assignIds(ids);
    },

    _assignIds: function(ids) {
        var params = this.gridAssigned.getBaseParams();
        params[this.gridDataParamName] = [];
        for (var i = 0; i < ids.length; i++) {
            params[this.gridDataParamName].push(ids[i]);
        }
        params[this.gridDataParamName] = Ext2.encode(params[this.gridDataParamName]);
        Ext2.Ajax.request({
            mask: true,
            url: this.assignActionUrl,
            params: params,
            success: function() {
                this.gridAssigned.reload();
                this.gridData.reload();
                this.fireEvent('assigned', this);
                this.gridData.getSelectionModel().clearSelections();
            },
            scope: this
        });
    },

    onAssignAll: function() {
        var ids = [];
        var collectIds = function(store, start, previousIds) {
            store.load({params: {start: start, limit: this.assignAllLimit}, callback: function(records, options, success) {
                for (var i = 0; i < records.length; i++) {
                    previousIds.push(records[i].id);
                }
                if (records.length < this.assignAllLimit) {
                    this._assignIds(previousIds);
                } else {
                    collectIds(store, start+this.assignAllLimit, previousIds);
                }
            }, scope: this});
        }.bind(this);
        collectIds(this.gridData.getStore(), 0, []);
    },

    reloadDataGrid: function() {
        return this.gridData.reload.apply(this.gridData, arguments);
    },

    setAutoLoad: function(v) {
        Kwf.Auto.AssignGridPanel.superclass.setAutoLoad.apply(this, arguments);
        this.gridData.setAutoLoad(v);
    },
    load: function() {
        Kwf.Auto.AssignGridPanel.superclass.load.apply(this, arguments);

        //wenn autoLoad=false
        if (!this.gridData.getStore()) {
            this.gridData.load();
        }
    },

    setBaseParams: function(bp) {
        this.gridAssigned.setBaseParams(bp);
        this.gridData.setBaseParams(bp);
        return Kwf.Auto.AssignGridPanel.superclass.setBaseParams.call(this, bp);
    },

    applyBaseParams: function(bp) {
        this.gridAssigned.applyBaseParams(bp);
        this.gridData.applyBaseParams(bp);
        return Kwf.Auto.AssignGridPanel.superclass.applyBaseParams.call(this, bp);
    }

});

Ext2.reg('kwf.assigngrid', Kwf.Auto.AssignGridPanel);
