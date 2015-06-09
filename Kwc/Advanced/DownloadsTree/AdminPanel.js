Ext2.namespace('Kwc.Advanced.DownloadsTree.Downloads');

Kwc.Advanced.DownloadsTree.AdminPanelProjects = Ext2.extend(Kwf.Auto.TreePanel, {
    border: false,
    initComponent: function() {
        Kwc.Advanced.DownloadsTree.AdminPanelProjects.superclass.initComponent.call(this);
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

Kwc.Advanced.DownloadsTree.AdminPanel = Ext2.extend(Kwf.Binding.ProxyPanel, {
    initComponent: function() {
        this.downloads = new Kwf.Auto.GridPanel({
            controllerUrl: this.downloadsUrl,
            region: 'center'
        });
        this.projects = new Kwc.Advanced.DownloadsTree.AdminPanelProjects({
            controllerUrl: this.projectsUrl,
            region: 'west',
            width: 300,
            split: true,
            bindings: [{
                item: this.downloads,
                queryParam: 'project_id'
            }],
            editDialog: new Kwf.Auto.Form.Window({
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
        Kwc.Advanced.DownloadsTree.AdminPanel.superclass.initComponent.call(this);
    },
    applyBaseParams: function() {
        this.downloads.applyBaseParams.apply(this.downloads, arguments);
        return Kwc.Advanced.DownloadsTree.AdminPanel.superclass.applyBaseParams.apply(this, arguments);
    },
    setBaseParams : function(baseParams) {
        this.downloads.setBaseParams.apply(this.downloads, arguments);
        return Kwc.Advanced.DownloadsTree.AdminPanel.superclass.setBaseParams.apply(this, arguments);
    }
});
Ext2.reg('kwc.advanced.downloadstree', Kwc.Advanced.DownloadsTree.AdminPanel);
