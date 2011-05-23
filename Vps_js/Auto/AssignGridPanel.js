Vps.Auto.AssignGridPanel = Ext.extend(Vps.Binding.ProxyPanel, {

    gridAssignedControllerUrl: '',
    gridDataControllerUrl: '',
    textAssignActionUrl: null,

    gridDataHeight: 410,

    assignActionUrl: '',
    gridDataParamName: 'foreign_keys',

    initComponent: function()
    {
        this.addEvents({
            'assigned': true
        });

        this.actions.assign = new Ext.Action({
            text    : trlVps('Assign'),
            icon    : '/assets/silkicons/table_relationship.png',
            cls     : 'x-btn-text-icon',
            disabled: true,
            handler : this.onAssign,
            scope   : this
        });

        if (this.assignActionUrl == '') {
            this.assignActionUrl = this.gridAssignedControllerUrl + '/json-assign';
        }

        this.gridAssigned = new Vps.Auto.AssignedGridPanel({
            controllerUrl: this.gridAssignedControllerUrl,
            textAssignActionUrl: this.textAssignActionUrl,
            region: 'center',
            gridConfig: {
                selModel: new Ext.grid.CheckboxSelectionModel()
            }
        });
        this.proxyItem = this.gridAssigned;

        this.gridData = new Vps.Auto.GridPanel({
            region: 'south',
            split: true,
            height: this.gridDataHeight,
            controllerUrl: this.gridDataControllerUrl,
            gridConfig: {
                tbar: [ this.getAction('assign'), '-' ],
                selModel: new Ext.grid.CheckboxSelectionModel()
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

        Vps.Auto.AssignGridPanel.superclass.initComponent.call(this);
    },

    onAssign : function()
    {
        if (!this.gridData.getSelectionModel().getSelections()) return;
        var params = this.gridAssigned.getBaseParams();
        params[this.gridDataParamName] = [];

        var selections = this.gridData.getSelectionModel().getSelections();
        for (var i in selections) {
            if (selections[i].id) {
                params[this.gridDataParamName].push(selections[i].id);
            }
        }
        params[this.gridDataParamName] = Ext.encode(params[this.gridDataParamName]);

        Ext.Ajax.request({
            url: this.assignActionUrl,
            params: params,
            success: function() {
                this.gridAssigned.reload();
                this.gridData.reload();
                this.fireEvent('assigned', this);
            },
            scope: this
        });
    },

    reloadDataGrid: function() {
        return this.gridData.reload.apply(this.gridData, arguments);
    },

    setAutoLoad: function(v) {
        Vps.Auto.AssignGridPanel.superclass.setAutoLoad.apply(this, arguments);
        this.gridData.setAutoLoad(v);
    },
    load: function() {
        Vps.Auto.AssignGridPanel.superclass.load.apply(this, arguments);

        //wenn autoLoad=false
        if (!this.gridData.getStore()) {
            this.gridData.load();
        }
    },

    setBaseParams: function(bp) {
        this.gridAssigned.setBaseParams(bp);
        this.gridData.setBaseParams(bp);
        return Vps.Auto.AssignGridPanel.superclass.setBaseParams.call(this, bp);
    },

    applyBaseParams: function(bp) {
        this.gridAssigned.applyBaseParams(bp);
        this.gridData.applyBaseParams(bp);
        return Vps.Auto.AssignGridPanel.superclass.applyBaseParams.call(this, bp);
    }

});

Ext.reg('vps.assigngrid', Vps.Auto.AssignGridPanel);