Kwf.Binding.TabPanel = Ext.extend(Kwf.Binding.AbstractPanel,
{
    layout: 'fit',
    initComponent : function()
    {
        if (!this.tabPanelSettings) this.tabPanelSettings = {};
        this.tabPanel = new Ext.TabPanel(Ext.applyIf(this.tabPanelSettings, {
            deferredRender: false,
            activeTab: this.activeTab || 0,
            enableTabScroll: this.enableTabScroll || false
        }));
        this.tabItems = [];
        
        for (var i in this.tabs) {
            var b = {};
            if (this.baseParams) {
                for (var j in this.baseParams) {
                    if (j == 'componentId' && this.tabs[i].componentIdSuffix) {
                        b[j] = this.baseParams[j] + this.tabs[i].componentIdSuffix;
                    } else {
                        b[j] = this.baseParams[j];
                    }
                }
            }
            // das tab muss gecloned werden, da sonst die baseParams nur vom
            // ersten verwendet werden, weil sie wegen dem applyIf nicht
            // nochmal kopiert werden, und dann bearbeitet man falsche
            // datensätze...
            var tab = Kwf.clone(this.tabs[i]);
            if (tab.needsComponentPanel) {
                var componentIdSuffix = tab.componentIdSuffix;
                delete tab.componentIdSuffix;
                var item = new Kwf.Component.ComponentPanel({
                    title: tab.title,
                    mainComponentClass: 'Dummy',
                    mainType: 'content',
                    componentIdSuffix: componentIdSuffix,
                    componentConfigs: {
                        'Dummy-content': tab
                    },
                    mainEditComponents: ['Dummy-content'],
                    baseParams  : b
                });
            } else {
                var item = Ext.ComponentMgr.create(Ext.applyIf(tab, {
                    autoScroll  : true,
                    closable    : false,
                    title       : i,
                    baseParams  : b,
                    autoLoad    : this.autoLoad
                }));
            }

            this.relayEvents(item, ['editcomponent', 'gotComponentConfigs', 'datachange']);
            this.tabPanel.add(item);
            this.tabItems.push(item);
        }
        if (this.baseParams) delete this.baseParams;
        this.tabItems.each(function(i) {
            if (this.tabPanel.getActiveTab() != i) {
                i.setAutoLoad(false);
            }
            i.on('activate', function(i) {
                if (!this.disabled) {
                    i.load();
                }
            }, this);
        }, this);

        this.items = this.tabPanel;
        Kwf.Binding.TabPanel.superclass.initComponent.call(this);

    },

    mabySubmit: function() {
        var arg = arguments;
        var ret = true;
        this.tabItems.each(function(i) {
            if (!i.mabySubmit.apply(i, arg)) {
                ret = false;
                return false;
            }
        }, this);
        return ret;
    },
    submit: function() {
        var arg = arguments;
        this.tabItems.each(function(i) {
            i.submit.apply(i, arg);
        }, this);
    },
    reset: function() {
        var arg = arguments;
        this.tabItems.each(function(i) {
            i.reset.apply(i, arg);
        }, this);
    },
    load: function() {
        var arg = arguments;
        this.tabItems.each(function(i) {
            if (this.tabPanel.getActiveTab() == i) {
                i.load.apply(i, arg);
            }
        }, this);
    },
    reload: function() {
        var arg = arguments;
        this.tabItems.each(function(i) {
            i.reload.apply(i, arg);
        }, this);
    },

    getSelectedId: function() {
    },
    selectId: function(id) {
    },

    isDirty: function() {
        var arg = arguments;
        var ret = false;
        this.tabItems.each(function(i) {
            if (i.isDirty.apply(i, arg)) {
                ret = true;
                return false;
            }
        }, this);
        return ret;
    },
    applyBaseParams: function(baseParams) {
        this.tabItems.each(function(item) {
            var b = {};
            for (var i in baseParams) {
                if (i == 'componentId' && item.componentIdSuffix) {
                    b[i] = baseParams[i] + item.componentIdSuffix;
                } else {
                    b[i] = baseParams[i];
                }
            }
            item.applyBaseParams(b);
        }, this);
    },
    setBaseParams : function(baseParams) {
        this.tabItems.each(function(i) {
            i.setBaseParams({});
            i.applyBaseParams(baseParams);
        }, this);
    },
    getBaseParams : function() {
        //Annahme: alle haben die gleichen baseParams
        this.tabItems[0].getBaseParams();
    },
    hasBaseParams: function() {
        //Annahme: alle haben die gleichen baseParams
        this.tabItems[0].hasBaseParams();
    },
    setAutoLoad: function(v) {
        this.tabItems.each(function(i) {
            i.setAutoLoad(v);
        }, this);
    },
    getAutoLoad: function() {
        //Annahme: alle haben die gleiches autoLoad
        return this.tabItems[0].getAutoLoad.apply(this.proxyItem, arguments);
    }
});
Ext.reg('kwf.tabpanel', Kwf.Binding.TabPanel);
