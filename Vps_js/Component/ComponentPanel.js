Vps.Component.ComponentPanel = Ext.extend(Vps.Binding.AbstractPanel, {
    layout: 'card',
    mainComponentClass: 'Vpc_Paragraphs_Component',
    mainComponentId: '{0}',
    mainComponentText: 'Content',
    mainComponentIcon: '/assets/vps/images/paragraph_page.gif',
    componentEditUrl: '/admin/component/edit',
    initComponent: function() {
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
        var params;
        if (data.componentId) {
            params = { componentId: data.componentId };
        } else {
            params = this.getBaseParams();
        }
        var item;
        this.items.each(function(i) {
            if (i.componentClass == data.componentClass) {
                item = i;
                return false; //break each
            }
        }, this);

        if (item) {
            this._loadComponentPanel(item, data);
            item.applyBaseParams(params);
            item.load();
        } else {
            Ext.Ajax.request({
                url: this.componentEditUrl + '/' + data.componentClass + '/json-index',
                success: function(r, options, response) {
                    Ext.applyIf(response.config, {
                        autoScroll : true,
                        componentClass: data.componentClass,
                        baseParams: params
                    });
                    var panel = Ext.ComponentMgr.create(response.config);
                    panel.on('editcomponent', this.loadComponent, this);
                    panel.on('loaded', function() {
                        //muss hier oben auch nochmal gemacht werden
                        //weil in loadComponentPanel gibt es die action noch nicht
                        if (panel.getAction && panel.getAction('saveBack')) {
                            if (this.getTopToolbar().items.getCount() > 0) {
                                panel.getAction('saveBack').show();
                            }
                        }
                    }, this);
                    this.add(panel);
                    this.doLayout();

                    //TODO: nach welcher logik componentName zusammensetzen?
                    //wo soll diese Logik liegen?
                    //hier, in Komponente, in Paragraphs?
                    data.text = panel.componentName;
                    data.icon = panel.componentIcon;

                    this._loadComponentPanel(panel, data);
                },
                scope: this
            });
        }
    },

    _loadComponentPanel: function(panel, data)
    {
        this.componentsStack.push(data);
        this.addToolbarButton(data);

        this.getLayout().setActiveItem(panel);

        if (panel.getAction && panel.getAction('saveBack')) {
            if (this.getTopToolbar().items.getCount() > 0) {
                panel.getAction('saveBack').show();
            }
        }
        panel.on('savebackaction', function() {
            this.componentsStack.pop();
            var data = this.componentsStack[this.componentsStack.length-1];
            this.loadComponent(data);
        }, this);

    },

    addToolbarButton: function(data) {
        var toolbar = this.getTopToolbar();
        var count = toolbar.items.getCount();
        del = count;
        for (var x=0; x<count; x++){
            var item = toolbar.items.itemAt(x);
            if (item.params != undefined &&
                item.params.componentId == data.componentId
            ) {
                del = x > 0 ? x - 1 : x;
                x = count;
            }
        }
        for (var x=count-1; x>=del; x--){
            var item = toolbar.items.itemAt(x);
            toolbar.items.removeAt(x);
            item.destroy();
        }
        if (toolbar.items.getCount() >= 1) {
                toolbar.add('»');
        }
        toolbar.addButton({
            text    : data.text,
            icon    : data.icon,
            cls     : data.icon ? 'x-btn-text-icon' : 'x-btn-text',
            handler : function (o, e) {
                this.loadComponent(data);
            },
            params: data,
            scope   : this
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
            text: this.mainComponentText,
            icon: this.mainComponentIcon
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
