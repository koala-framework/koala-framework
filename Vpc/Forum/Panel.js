Ext.namespace('Vpc.Forum');
Vpc.Forum.Panel = Ext.extend(Vps.Auto.TreePanel,
{
    initComponent: function() {
        var modAssignGrid = new Vps.Auto.AssignGridPanel({
            gridAssignedControllerUrl : '/admin/component/edit/Vpc_Forum_ModeratorsToGroup',
            gridDataControllerUrl     : '/admin/component/edit/Vpc_Forum_Moderators',
            gridDataHeight            : 300
        });

        this.bindings = [
            { item: modAssignGrid, queryParam: 'group_id' }
        ];

        this.modDialog = new Ext.Window({
            layout: 'fit',
            title: 'Moderators of selected group',
            closeAction: 'hide',
            width: 600,
            height: 480,
            items: [ modAssignGrid ]
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
    }
});

Ext.reg('vpc.forum', Vpc.Forum.Panel);
