
Ext2.namespace("Kwf.User.Grid");

Kwf.User.Grid.Index = Ext2.extend(Ext2.Panel, {

    initComponent: function() {
        var bindings = [];
        var southPanelItems = [ ];

        if (this.commentsControllerUrl) {
            var commentsGrid = new Kwf.Auto.GridPanel({
                controllerUrl: this.commentsControllerUrl,
                region: 'center',
                title: trlKwf('Comments')
            });
            bindings.push({ item: commentsGrid, queryParam: 'user_id' });
            southPanelItems.push(commentsGrid);
        }

        if (this.logControllerUrl) {
            logGridConfig = {
                controllerUrl: this.logControllerUrl ,
                region: 'center',
                split: true,
                title: trlKwf('Log')
            };
            if (this.commentsControllerUrl) {
                logGridConfig.width = 550;
                logGridConfig.region = 'east';
            }
            var logGrid = new Kwf.Auto.GridPanel(logGridConfig);
            bindings.push({ item: logGrid, queryParam: 'user_id' });
            southPanelItems.push(logGrid);
        }

        if (typeof commentsGrid != 'undefined' || typeof logGrid != 'undefined') {
            var southPanel = new Ext2.Panel({
                layout: 'border',
                items: southPanelItems,
                region: 'south',
                split: true,
                height: 240
            });
        }

        var userGrid = new Kwf.User.Grid.Grid({
            controllerUrl: this.controllerUrl,
            region: 'center',
            bindings: bindings
        });

        this.layout = 'border';
        this.items = [ userGrid ];

        if (typeof southPanel != 'undefined') {
            this.items.push(southPanel);
        }

        Kwf.User.Grid.Index.superclass.initComponent.call(this);
    }
});
