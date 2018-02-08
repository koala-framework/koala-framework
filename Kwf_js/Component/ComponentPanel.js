Kwf.Component.ComponentPanel = Ext2.extend(Kwf.Binding.AbstractPanel, {
    layout: 'card',
    mainComponentClass: 'Kwc_Paragraphs_Component',
    mainType: 'content',
    mainComponentId: '{0}',
    mainComponentText: null,
    mainComponentIcon: null,
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
        this.contentPanel = new Ext2.Panel();
        Ext2.apply(this, {
            tbar        : new Ext2.Toolbar({ height: 25 }),
            items       : this.contentPanel,
            currentItem : this.contentPanel
        });
        this.componentsStack = [];

        Kwf.Component.ComponentPanel.superclass.initComponent.call(this);
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
        if (!componentConfig) throw new Error("couldn't find componentConfig "+data.componentClass+'-'+data.type);
        var params;
        if (data.componentId) {
            params = { componentId: data.componentId };
            if (data.componentIdSuffix) {
                params.componentId += data.componentIdSuffix;
            } else if (componentConfig.componentIdSuffix) {
                //deprecated!
                throw new Error("don't use componentConfig.componentIdSuffix");
                //params.componentId += componentConfig.componentIdSuffix;
            }
        } else {
            params = this.getBaseParams();
            data.componentId = params.componentId;
        }
        var item;
        this.items.each(function(i) {
            if (i.componentClass == data.componentClass && i.type == data.type) {
                // render always kann bei der ExtConfig.php mitgegeben werden
                // um zu erzwingen, dass immer gerendert wird.
                // Bsp: 2x Table in einem Paragraph mit unterschiedlicher col-anzahl
                //      wenn beide nacheinander bearbeitet werden, würde sonst die
                //      zweite die col-anzahl der ersten verwenden was falsch wär
                if (i.renderAlways) {
                    this.remove(i);
                } else {
                    item = i;
                    return false; //break each
                }
            }
        }, this);

        this.componentsStack.push(data);
        this.updateToolbar();

        if (item) {
            item.applyBaseParams(params);
            item.load({}, {focusAfterLoad: true});
            if (item.getAction && item.getAction('saveBack')) {
                if (this.getTopToolbar().items.getCount() > 2) {
                    item.getAction('saveBack').show();
                } else {
                    item.getAction('saveBack').hide();
                }
            }
        } else {
            var config = {
                autoScroll : true,
                componentClass: data.componentClass,
                type: data.type,
                baseParams: params,
                focusAfterAutoLoad: true,
                autoHeight: this.autoHeight,
                listeners: {
                    scope: this,
                    gotComponentConfigs: function(componentConfigs) {
                        Ext2.applyIf(this.componentConfigs, componentConfigs);
                    },
                    editcomponent: this.loadComponent
                }
            };
            Ext2.apply(config, componentConfig);
            if (config.title) delete config.title;

            var item = Ext2.ComponentMgr.create(config);
            this.relayEvents(item, ['editcomponent', 'gotComponentConfigs', 'datachange']);
            item.on('savebackaction', function() {
                this.componentsStack.pop();
                var data = this.componentsStack.pop();
                this.loadComponent(data);
            }, this);
            item.on('loaded', function() {
                //muss hier auch nochmal gemacht werden
                if (item.getAction && item.getAction('saveBack')) {
                    var count = this.getTopToolbar().items.getCount();
                    if (count > 2 && 
                        this.getTopToolbar().items.items[count-3].xtype == 'splitbutton'
                    ) {
                        item.getAction('saveBack').show();
                    } else {
                        item.getAction('saveBack').hide();
                    }
                }
            }, this);
            this.add(item);
            this.doLayout();
        }
        this.getLayout().setActiveItem(item);
    },

    updateToolbar: function() {
        this.clearToolbar();
        var toolbar = this.getTopToolbar();
        for (var i=0; i < this.componentsStack.length; i++) {
            var data = this.componentsStack[i];
            var b = {};
            if (i > 0) {
                b.xtype = 'splitbutton';
                var cfg = this.componentConfigs[this.componentsStack[i-1].componentClass
                                                +'-'+this.componentsStack[i-1].type];
                b.text = cfg.title;
                b.icon = cfg.icon;
                b.scope = this;
                b.stackIndex = i-1;
                b.handler = function(o) {
                    var data = this.componentsStack[o.stackIndex];
                    this.componentsStack = this.componentsStack.slice(0, o.stackIndex);
                    this.loadComponent(data);
                };
            } else {
                if (!this.mainComponentText) {
                    //zB bei news wenn als eigener controller angezeigt
                    continue;
                }
                b.xtype = 'button';
                b.text = this.mainComponentText;
                b.icon = this.mainComponentIcon;
            }
            b.cls = 'x2-btn-text-icon';
            b.menu = [];
            data.editComponents.each(function(ec) {
                var cfg = this.componentConfigs[ec.componentClass+'-'+ec.type];
                b.menu.push({
                    text: cfg.title,
                    icon: cfg.icon,
                    stackIndex: i,
                    componentClass: ec.componentClass,
                    type: ec.type,
                    componentIdTemplate: ec.componentIdTemplate,
                    componentIdSuffix: ec.componentIdSuffix,
                    componentId: ec.componentId, //gesetzt wenn aus Pages - weil da gibts unterschiedliche
                    handler: function(o) {
                        var data = Kwf.clone(this.componentsStack[o.stackIndex]);
                        if (o.componentId) data.componentId = o.componentId;
                        data.componentClass = o.componentClass;
                        data.type = o.type;
                        data.componentIdTemplate = o.componentIdTemplate;
                        data.componentIdSuffix = o.componentIdSuffix;
                        this.componentsStack = this.componentsStack.slice(0, o.stackIndex);
                        this.loadComponent(data);
                    },
                    scope: this
                });
            }, this);

            toolbar.add(b);
            toolbar.add('»');
        }

        var data = this.componentsStack[this.componentsStack.length-1];
        var cfg = this.componentConfigs[data.componentClass+'-'+data.type];
        toolbar.add({
            cls: 'x2-btn-text-icon',
            text: cfg.title,
            icon: cfg.icon,
            scope: this,
            handler: function() {
                this.getLayout().activeItem.reload();
            }
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
        if (!data) { data = {}; }
        Ext2.applyIf(data, {
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
        Ext2.apply(this.baseParams, baseParams);
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

Ext2.reg('kwf.component', Kwf.Component.ComponentPanel);
