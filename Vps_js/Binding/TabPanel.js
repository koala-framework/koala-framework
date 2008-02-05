Vps.Binding.TabPanel = Ext.extend(Vps.Binding.AbstractPanel,
{
    layout: 'fit',
    initComponent : function()
    {
        var tabPanel = new Ext.TabPanel({
            deferredRender: false,
            activeTab: this.activeTab
        });
        this.tabItems = [];
        for (var i in this.tabs) {
            var b = {};
            if (this.baseParams) {
                for (var j in this.baseParams) {
                    if (j == 'component_id' && this.tabs[i].componentIdSuffix) {
                        b[j] = this.baseParams[j] + this.tabs[i].componentIdSuffix;
                    } else {
                        b[j] = this.baseParams[j];
                    }
                }
            }
            var item = Ext.ComponentMgr.create(Ext.applyIf(this.tabs[i], {
                autoScroll  : true,
                closable    : false,
                title       : i,
                id          : i,
                baseParams  : b
            }));
            this.relayEvents(item, ['editcomponent']);
            tabPanel.add(item);
            this.tabItems.push(item);
        }
        if (this.baseParams) delete this.baseParams;

        this.items = tabPanel;
        Vps.Binding.TabPanel.superclass.initComponent.call(this);
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
            i.load.apply(i, arg);
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
                if (i == 'component_id' && item.componentIdSuffix) {
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
        this.tabItems.first().getBaseParams();
    },
    hasBaseParams: function() {
        //Annahme: alle haben die gleichen baseParams
        this.tabItems.first().hasBaseParams();
    }
});
Ext.reg('vps.tabpanel', Vps.Binding.TabPanel);
