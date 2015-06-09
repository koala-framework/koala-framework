Ext2.namespace('Kwc.Articles.Directory');
Kwc.Articles.Directory.AuthorsPanel = Ext2.extend(Kwf.Binding.ProxyPanel, {
    initComponent: function() {
        this.articlesGrid = new Kwf.Auto.GridPanel({
            controllerUrl: this.articlesControllerUrl,
            region: 'east',
            width: 500,
            split: true
        }, this);

        this.proxyItem = new Kwf.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            region: 'center',
            bindings: [{
                item: this.articlesGrid,
                queryParam: 'author_id'
            }]
        }, this);

        this.layout = 'border';
        this.items = [this.proxyItem, this.articlesGrid];

        Kwc.Articles.Directory.AuthorsPanel.superclass.initComponent.call(this);
    },
    applyBaseParams: function(baseParams) {
        this.articlesGrid.applyBaseParams(baseParams);
        Kwc.Articles.Directory.AuthorsPanel.superclass.applyBaseParams.apply(this, arguments);
    },
    setBaseParams : function(baseParams) {
        this.articlesGrid.setBaseParams(baseParams);
        Kwc.Articles.Directory.AuthorsPanel.superclass.setBaseParams.apply(this, arguments);
    }
}, this);
Ext2.reg('kwc.articles.directory.authorsPanel', Kwc.Articles.Directory.AuthorsPanel);