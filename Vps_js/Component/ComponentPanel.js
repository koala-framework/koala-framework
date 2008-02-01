Vps.Component.ComponentPanel = Ext.extend(Vps.Auto.AbstractPanel, {
    layout: 'fit',
    mainComponentClass: 'Vpc_Paragraphs_Component',
    mainComponentId: '{0}',
    mainComponentText: 'Content',

    initComponent: function() {
        this.contentPanel = new Ext.Panel();
        Ext.apply(this, {
            tbar        : [],
            items       : this.contentPanel
        });
        this.baseParams = {};
        Vps.Component.ComponentPanel.superclass.initComponent.call(this);
    },

    loadComponent: function(data) {
        var params;
        if (data.componentId) {
            params = { component_id: data.componentId };
        } else {
            params = this.getBaseParams();
        }
        Ext.Ajax.request({
            url: '/admin/component/edit/' + data.componentClass + '/jsonIndex',
            params: params,
            success: function(r, options, response) {
                var cls = eval(response['class']);
                if (cls) {
                    var panel2 = new cls(Ext.applyIf(response.config, {
                        region          : 'center',
                        autoScroll      : true,
                        closable        : true
                    }));
                    panel2.on('editcomponent', this.loadComponent, this);
                    this.addToolbarButton(data);

                    this.remove(this.contentPanel);
                    //this.contentPanel.destory();
                    this.add(panel2);
                    this.contentPanel = panel2;
                    this.layout.rendered = false;
                    this.doLayout();
                }
            },
            scope: this
        });
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

    load: function() {
        this.clearToolbar();
        this.loadComponent({
            componentClass: this.mainComponentClass,
            text: this.mainComponentText,
            icon: '/assets/vps/images/paragraph_page.gif'
        });
    },

    isDirty: function() {
        return this.contentPanel.isDirty();
    },
    mabySubmit : function(cb, options)
    {
        return this.contentPanel.mabySubmit.apply(this, arguments);
    },
    applyBaseParams : function(baseParams) {
        if (baseParams.id) {
            baseParams.component_id = String.format(this.mainComponentId, baseParams.id);
            delete baseParams.id;
        }
        Ext.apply(this.baseParams, baseParams);
    },
    hasBaseParams : function(params) {
        var baseParams = this.getBaseParams();
        for (var i in params) {
            if (i == 'id') {
                if (baseParams.component_id != String.format(this.mainComponentId, params[i])) {
                    return false;
                }
            } else {
                if (params[i] != baseParams[i]) return false;
            }
        }
        return true;
    }
});
