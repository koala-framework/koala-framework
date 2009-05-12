Ext.namespace('Vpc.Forum');
Vpc.Forum.Panel = Ext.extend(Vps.Auto.TreePanel,
{
    initComponent: function() {
        this.modAssignGrid = new Vps.Auto.AssignGridPanel({
            gridAssignedControllerUrl : this.controllerUrl + '!ModeratorsToGroup',
            gridDataControllerUrl     : this.controllerUrl + '!Moderators',
            gridDataHeight            : 300
        });

        this.bindings = [
            { item: this.modAssignGrid, queryParam: 'group_id' }
        ];

        this.modDialog = new Ext.Window({
            layout: 'fit',
            title: trlVps('Moderators of selected group'),
            closeAction: 'hide',
            width: 600,
            height: 480,
            items: [ this.modAssignGrid ]
        });

        this.actions.moderators = new Ext.Action({
            text    : 'Moderators',
            handler : this.onModerators,
            cls     : 'x-btn-text-icon',
            disabled: true,
            scope   : this
        });

        this.on('selectionchange', function(node) {
            if (node && node.id != 0) {
                this.getAction('moderators').enable();
            } else {
                this.getAction('moderators').disable();
            }
        });

        Vpc.Forum.Panel.superclass.initComponent.call(this);
    },

    onModerators: function()
    {
        this.modDialog.show()
    },

    setBaseParams: function(bp) {
        this.modAssignGrid.setBaseParams(bp);
        return Vpc.Forum.Panel.superclass.setBaseParams.call(this, bp);
    },

    applyBaseParams: function(bp) {
        this.modAssignGrid.applyBaseParams(bp);
        return Vpc.Forum.Panel.superclass.applyBaseParams.call(this, bp);
    }
});

Ext.reg('vpc.forum', Vpc.Forum.Panel);
