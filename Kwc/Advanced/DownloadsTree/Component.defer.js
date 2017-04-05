var onReady = require('kwf/commonjs/on-ready-ext2');
onReady.onRender('.kwcClass .content', function(i) {
        var options = Ext2.decode(Ext2.get(i).down('.options').dom.value);
        this.downloads = new Kwf.Auto.GridPanel({
            controllerUrl: options.downloadsUrl,
            baseParams: {
                componentId: options.componentId
            },
            region: 'center'
        });
        this.projects = new Kwf.Auto.TreePanel({
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

        new Ext2.Panel({
            border: true,
            width: options.width,
            height: options.height,
            renderTo: i,
            layout: 'border',
            items: [this.downloads, this.projects]
        });
}, { defer: true });
