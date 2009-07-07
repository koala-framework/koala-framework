Ext.namespace('Vpc.Advanced.DownloadsTree.Downloads');

Vpc.Advanced.DownloadsTree.AdminPanelProjects = Ext.extend(Vps.Auto.TreePanel, {
    border: false,
    initComponent: function() {
        Vpc.Advanced.DownloadsTree.AdminPanelProjects.superclass.initComponent.call(this);
        this.editDialog.on('addaction', function() {
            if (!this.getSelectedId() || this.getSelectedId() == '0') {
                this.showFieldset();
            } else {
                this.hideFieldset();
            }
        }, this);
        this.editDialog.on('loadform', function() {
            var node = this.tree.getSelectionModel().getSelectedNode();
            if (node.attributes.data.parent_id) {
                this.hideFieldset();
            } else {
                this.showFieldset();
            }
        }, this);
    },

    showFieldset: function() {
        this.editDialog.cascade(function(i) {
            if (i.permissionsField) {
                i.show();
            }
        }, this);
    },
    hideFieldset: function() {
        this.editDialog.cascade(function(i) {
            if (i.permissionsField) {
                i.hide();
            }
        }, this);
    }
});

Vpc.Advanced.DownloadsTree.AdminPanel = Ext.extend(Vps.Binding.ProxyPanel, {
    initComponent: function() {
        this.downloads = new Vps.Auto.GridPanel({
            controllerUrl: this.downloadsUrl,
            region: 'center'
        });
        this.projects = new Vpc.Advanced.DownloadsTree.AdminPanelProjects({
            controllerUrl: this.projectsUrl,
            region: 'west',
            width: 300,
            split: true,
            bindings: [{
                item: this.downloads,
                queryParam: 'project_id'
            }],
            editDialog: new Vps.Auto.Form.Window({
                controllerUrl: this.projectUrl,
                width: 400,
                height: 400
            })
        });
        this.projects.on('selectionchange', function(node) {
            if (!node || !parseInt(node.id)) {
                this.downloads.getAction('add').disable();
            } else {
                this.downloads.getAction('add').enable();
            }
        }, this);
        this.layout = 'border';
        this.items = [this.projects, this.downloads];
        this.proxyItem = this.projects;
        Vpc.Advanced.DownloadsTree.AdminPanel.superclass.initComponent.call(this);
    },
    applyBaseParams: function() {
        this.downloads.applyBaseParams.apply(this.downloads, arguments);
        return Vpc.Advanced.DownloadsTree.AdminPanel.superclass.applyBaseParams.apply(this, arguments);
    },
    setBaseParams : function(baseParams) {
        this.downloads.setBaseParams.apply(this.downloads, arguments);
        return Vpc.Advanced.DownloadsTree.AdminPanel.superclass.setBaseParams.apply(this, arguments);
    }
});
Ext.reg('vpc.advanced.downloadstree', Vpc.Advanced.DownloadsTree.AdminPanel);
