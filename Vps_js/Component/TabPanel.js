Ext.namespace('Vps.Component');
Vps.Component.TabPanel = Ext.extend(Ext.TabPanel,
{
    initComponent : function()
    {
        this.addEvents({
            loadtabs: true
        });
        this.deferredRender = false;

        Vps.Component.TabPanel.superclass.initComponent.call(this);

        this.tabItems = [];
        var x = 0;
        for (var i in this.itemUrls) { x++; }
        this.itemUrlsLength = x;

        var pos = 0;
        for (var i in this.itemUrls) {
            Ext.Ajax.request({
                url: this.itemUrls[i] + 'jsonIndex/',
                params : {
                    title : i,
                    pos : pos
                },
                success: function(r, p) {
                    response = Ext.decode(r.responseText);
                    response.title = p.params.title;
                    this.addTabItem(response, p.params.pos);
                },
                scope: this
            });
            pos++;
        }
    },

    addTabItem : function(item, pos)
    {
        this.tabItems[pos] = item;
        var count = 0;
        for (var i in this.tabItems) { count++; }
        if (count - 3 == this.itemUrlsLength) {
            for (var x = 0; x < this.tabItems.length; x++) {
                var response = this.tabItems[x];
                cls = eval(response['class']);
                if (cls) {
                    var item = new cls(Ext.applyIf(response.config, {
                        autoScroll  : true,
                        closable    : false,
                        title       : response.title,
                        id          : response.title
                    }));
                    this.add(item);
                    if (x == 0) {
                        this.setActiveTab(item);
                    }
                }
            }
            this.doLayout();
            this.fireEvent('loadtabs', this);
        }

    }
});