Vps.onContentReady(function() {
    Ext.query('.vpcAdvancedDownloadsTree .content').each(function(i) {
        var options = Ext.decode(Ext.get(i).down('.options').dom.value);
        this.downloads = new Vps.Auto.GridPanel({
            controllerUrl: options.downloadsUrl,
            baseParams: {
                componentId: options.componentId
            },
            region: 'center'
        });
        this.projects = new Vps.Auto.TreePanel({
            controllerUrl: options.projectsUrl,
            region: 'north',
            height: 220,
            baseParams: {
                componentId: options.componentId
            },
            bindings: [{
                item: this.downloads,
                queryParam: 'project_id'
            }]
        });
        this.projects.on('dblclick', function() {
            var node = this.projects.getSelectedNode();
            if (node.expanded) {
                node.collapse();
            } else {
                node.expand();
            }
        }, this);

        new Ext.Panel({
            border: true,
            width: options.width,
            height: options.height,
            renderTo: i,
            layout: 'border',
            items: [this.downloads, this.projects]
        });
    }, this); 
});
