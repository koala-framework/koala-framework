
Ext.namespace("Vps.User.Grid");

Vps.User.Grid.Index = Ext.extend(Ext.Panel, {

    initComponent: function() {
        var bindings = [];
        var southPanelItems = [ ];

        if (this.commentsControllerUrl) {
            var commentsGrid = new Vps.Auto.GridPanel({
                controllerUrl: this.commentsControllerUrl,
                region: 'center',
                title: trlVps('Comments')
            });
            bindings.push({ item: commentsGrid, queryParam: 'user_id' });
            southPanelItems.push(commentsGrid);
        }

        if (this.logControllerUrl) {
            logGridConfig = {
                controllerUrl: this.logControllerUrl ,
                region: 'center',
                split: true,
                title: trlVps('Log')
            };
            if (this.commentsControllerUrl) {
                logGridConfig.width = 550;
                logGridConfig.region = 'east';
            }
            var logGrid = new Vps.Auto.GridPanel(logGridConfig);
            bindings.push({ item: logGrid, queryParam: 'user_id' });
            southPanelItems.push(logGrid);
        }

        if (typeof commentsGrid != 'undefined' || typeof logGrid != 'undefined') {
            var southPanel = new Ext.Panel({
                layout: 'border',
                items: southPanelItems,
                region: 'south',
                split: true,
                height: 240
            });
        }

        var userGrid = new Vps.User.Grid.Grid({
            controllerUrl: this.controllerUrl,
            region: 'center',
            bindings: bindings
        });

        this.layout = 'border';
        this.items = [ userGrid ];

        if (typeof southPanel != 'undefined') {
            this.items.push(southPanel);
        }

        Vps.User.Grid.Index.superclass.initComponent.call(this);
    }
});
