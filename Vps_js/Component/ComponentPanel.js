Vps.Component.ComponentPanel = Ext.extend(Ext.Panel, {

    initComponent: function() {
        Ext.apply(this, {
            layout      : 'fit',
            region      : 'center',
            closable    : true,
            tbar        : [],
            items       : [new Ext.Panel({
                region  : 'center',
                id      : 'componentPanel'
            })]
        });
        Vps.Component.ComponentPanel.superclass.initComponent.call(this);
    },

    loadComponent: function(data) {
        Ext.Ajax.request({
            url: '/admin/component/edit/' + data.componentClass + '/jsonIndex',
            params: { page_id: data.pageId, component_key: data.componentKey },
            success: function(r) {
                response = Ext.decode(r.responseText);
                cls = eval(response['class']);
                if (cls) {
                    var panel2 = new cls(Ext.applyIf(response.config, {
                        region          : 'center',
                        autoScroll      : true,
                        closable        : true,
                        id              : 'componentPanel'
                    }));
                    panel2.on('editcomponent', this.loadComponent, this);
                    this.addToolbarButton(data);

                    this.remove(this.items.item('componentPanel'));
                    this.add(panel2);
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
                item.params.pageId == data.pageId &&
                item.params.componentKey == data.componentKey
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
                toolbar.addSeparator();
        }
        toolbar.addButton({
            text    : data.text,
            handler : function (o, e) {
                this.loadComponent(data);
            },
            params: data,
            scope   : this
        });
    }
});
