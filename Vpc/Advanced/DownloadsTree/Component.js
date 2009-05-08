Vps.onContentReady(function() {
    Ext.query('.vpcAdvancedDownloadsTree .content').each(function(i) {
        var componentId = Ext.get(i).down('.componentId').dom.value;
        var projectsClass = Ext.get(i).down('.projectsClass').dom.value;
        var downloadsClass = Ext.get(i).down('.downloadsClass').dom.value;
        this.downloads = new Vps.Auto.GridPanel({
            controllerUrl: '/admin/component/edit/' + downloadsClass,
            region: 'center'
        });
        this.projects = new Vps.Auto.TreePanel({
            controllerUrl: '/admin/component/edit/' + projectsClass,
            region: 'north',
            height: 220,
            baseParams: {
                component_id: componentId
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
            width: 490,
            height: 500,
            renderTo: i,
            layout: 'border',
            items: [downloads, projects]
        });
    }, this); 
});
