Vps.Component.ComponentPanel = Ext.extend(Vps.Binding.AbstractPanel, {
    layout: 'card',
    mainComponentClass: 'Vpc_Paragraphs_Component',
    mainType: 'content',
    mainComponentId: '{0}',
    mainComponentText: 'Content',
    mainComponentIcon: '/assets/vps/images/paragraph_page.gif',
    //mainEditComponents
    //componentConfigs

    initComponent: function() {
        if (!this.componentConfigs) this.componentConfigs = {};
        if (!this.mainEditComponents) this.mainEditComponents = {};

        if (this.autoLoad !== false) {
            this.autoLoad = true;
        } else {
            delete this.autoLoad;
        }
        this.contentPanel = new Ext.Panel();
        Ext.apply(this, {
            tbar        : new Ext.Toolbar({ height: 25 }),
            items       : this.contentPanel,
            currentItem : this.contentPanel
        });
        this.componentsStack = [];

        Vps.Component.ComponentPanel.superclass.initComponent.call(this);
    },
    doAutoLoad : function()
    {
        //autoLoad kann in der zwischenzeit abgeschaltet werden, zB wenn
        //wir in einem Binding sind
        if (!this.autoLoad) return;

        this.load();
    },

    loadComponent: function(data) {
        var componentConfig = this.componentConfigs[data.componentClass+'-'+data.type];
        if (!componentConfig) {
            throw "couldn't find componentConfig";
        }
        var params;
        if (data.componentId) {
            params = { componentId: data.componentId };
            if (componentConfig.componentIdSuffix) {
                params.componentId += componentConfig.componentIdSuffix;
            }
        } else {
            params = this.getBaseParams();
        }
        var item;
        this.items.each(function(i) {
            if (i.componentClass+'-'+i.type == data.componentClass+'-'+data.type) {
                item = i;
                return false; //break each
            }
        }, this);

        if (item) {
            item.applyBaseParams(params);
            item.load();
            if (item.getAction && item.getAction('saveBack')) {
                if (this.getTopToolbar().items.getCount() > 0) {
                    item.getAction('saveBack').show();
                }
            }
        } else {
            var config = {
                autoScroll : true,
                componentClass: data.componentClass,
                baseParams: params,
                listeners: {
                    scope: this,
                    gotComponentConfigs: function(componentConfigs) {
                        Ext.applyIf(this.componentConfigs, componentConfigs);
                    },
                    editcomponent: this.loadComponent
                }
            };
            Ext.apply(config, componentConfig);
            if (config.title) delete config.title;

            var item = Ext.ComponentMgr.create(config);
            item.on('savebackaction', function() {
                this.componentsStack.pop();
                var data = this.componentsStack[this.componentsStack.length-1];
                this.loadComponent(data);
            }, this);
            item.on('loaded', function() {
                //muss hier auch nochmal gemacht werden
                if (item.getAction && item.getAction('saveBack')) {
                    if (this.getTopToolbar().items.getCount() > 0) {
                        item.getAction('saveBack').show();
                    }
                }
            }, this);
            this.add(item);
            this.doLayout();

            if (!data.icon) {
                data.icon = componentConfig.icon;
            }
            if (!data.text) {
                data.text = componentConfig.text;
            }
        }

        this.componentsStack.push(data);
        this.addToolbarButton(item, data, componentConfig);
        this.getLayout().setActiveItem(item);
    },

    addToolbarButton: function(item, data, componentConfig) {
        var toolbar = this.getTopToolbar();

        var count = toolbar.items.getCount();
        del = count;
        for (var x=0; x<count; x++){
            var i = toolbar.items.itemAt(x);
            if (i.componentId == data.componentId) {
                del = x > 0 ? x - 1 : x;
                x = count;
            }
        }
        for (var x=count-1; x>=del; x--){
            var i = toolbar.items.itemAt(x);
            toolbar.items.removeAt(x);
            i.destroy();
        }
        var menuButton = {};
        if (toolbar.items.getCount() >= 1) {
            var lastButton = toolbar.items.last();
            menuButton.text = lastButton.text;
            menuButton.icon = lastButton.icon;
            toolbar.items.remove(lastButton);
            lastButton.destroy();
        } else {
            menuButton.text = data.text;
            menuButton.icon = data.icon;
        }
        menuButton.cls = 'x-btn-text-icon';
        menuButton.menu = [];
        data.editComponents.each(function(ec) {
            var cfg = this.componentConfigs[ec.componentClass+'-'+ec.type];
            menuButton.menu.push({
                text: cfg.title,
                icon: cfg.icon,
                editComponent: ec,
                data: data,
                handler: function(o) {
                    var data = Vps.clone(o.editComponent);
                    if (o.data.text) data.text = o.data.text;
                    if (o.data.icon) data.icon = o.data.icon;
                    data.editComponents = o.data.editComponents;
                    if (o.data.componentId) data.componentId = o.data.componentId;
                    this.loadComponent(data);
                },
                scope: this
            });
        }, this);
        toolbar.addButton(menuButton);
        toolbar.add('»');
        var cfg = this.componentConfigs[data.componentClass+'-'+data.type];
        toolbar.addButton({
            text    : cfg.title,
            icon    : cfg.icon,
            cls     : 'x-btn-text-icon',
            handler : function (o) {
                o.item.reload();
            },
            scope   : this,
            data    : data,
            componentId: data.componentId,
            item: item
        });
    },

    clearToolbar: function() {
        var toolbar = this.getTopToolbar();
        toolbar.items.each(function(i) {
            toolbar.items.remove(i);
            i.destroy();
        });
    },

    load: function(data) {
        this.componentsStack = [];
        this.clearToolbar();
        if (!data) { data = {}; }
        Ext.applyIf(data, {
            componentClass: this.mainComponentClass,
            type: this.mainType,
            text: this.mainComponentText,
            icon: this.mainComponentIcon,
            editComponents: this.mainEditComponents
        });
        this.loadComponent(data);
    },

    isDirty: function() {
        return this.contentPanel.isDirty();
    },
    mabySubmit : function(cb, options)
    {
        return this.contentPanel.mabySubmit.apply(this.contentPanel, arguments);
    },
    applyBaseParams : function(baseParams) {
        if (baseParams.id) {
            baseParams.componentId = String.format(this.mainComponentId, baseParams.id);
            delete baseParams.id;
        }
        Ext.apply(this.baseParams, baseParams);
    },
    hasBaseParams : function(params) {
        var baseParams = this.getBaseParams();
        for (var i in params) {
            if (i == 'id') {
                if (baseParams.componentId != String.format(this.mainComponentId, params[i])) {
                    return false;
                }
            } else {
                if (params[i] != baseParams[i]) return false;
            }
        }
        return true;
    }
});

Ext.reg('vps.component', Vps.Component.ComponentPanel);
