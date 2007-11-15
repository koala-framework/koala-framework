Vps.Auto.AssignGridPanel = Ext.extend(Vps.Auto.AbstractPanel, {

    gridAssignedControllerUrl: '',
    gridDataControllerUrl: '',
    textAssignActionUrl: null,

    gridDataHeight: 410,

    assignActionUrl: '',
    gridDataParamName: 'foreign_keys',


    initComponent: function()
    {
        this.actions = {};
        if (this.assignActionUrl == '') {
            this.assignActionUrl = this.gridAssignedControllerUrl + '/jsonAssign';
        }

        this.gridAssigned = new Vps.Auto.AssignedGridPanel({
            controllerUrl: this.gridAssignedControllerUrl,
            textAssignActionUrl: this.textAssignActionUrl,
            region: 'center'
        });

        this.gridData = new Vps.Auto.GridPanel({
            region: 'south',
            split: true,
            height: this.gridDataHeight,
            controllerUrl: this.gridDataControllerUrl,
            gridConfig: {
                tbar: [ this.getAction('assign'), '-' ],
                selModel: new Ext.grid.CheckboxSelectionModel()
            }
        });

        this.gridAssigned.on('datachange', function() {
            this.gridData.reload();
        }, this);

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

    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'assign') {
            this.actions[type] = new Ext.Action({
                text    : 'Assign',
                icon    : '/assets/silkicons/table_relationship.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                handler : this.onAssign,
                scope   : this
            });
        } else {
            throw 'unknown action-type: ' + type;
        }
        return this.actions[type];
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
            },
            scope: this
        });
    },

    reloadDataGrid: function() {
        return this.gridData.reload.apply(this.gridData, arguments);
    },


    load: function() {
        return this.gridAssigned.load.apply(this.gridAssigned, arguments);
    },
    applyBaseParams: function() {
        return this.gridAssigned.applyBaseParams.apply(this.gridAssigned, arguments);
    },
    getSelectedId: function() {
        return this.gridAssigned.getSelectedId.apply(this.gridAssigned, arguments);
    },
    selectId: function(id) {
        return this.gridAssigned.selectId.apply(this.gridAssigned, arguments);
    },
    setBaseParams : function(baseParams) {
        return this.gridAssigned.setBaseParams.apply(this.gridAssigned, arguments);
    },
    getBaseParams : function() {
        return this.gridAssigned.getBaseParams.apply(this.gridAssigned, arguments);
    }
});
