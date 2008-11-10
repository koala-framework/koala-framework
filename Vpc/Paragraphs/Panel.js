Ext.namespace('Vpc.Paragraphs');
Vpc.Paragraphs.Panel = Ext.extend(Vps.Auto.GridPanel,
{
    initComponent : function()
    {
        this.addEvents('editcomponent');

        var componentMenu = new Ext.menu.Menu({id: 'componentMenu'});
        this.addComponents(this.components, componentMenu);
        this.actions.addparagraph = new Ext.Action({
            text : trlVps('Add Paragraph'),
            icon : '/assets/vps/images/paragraphAdd.png',
            cls  : 'x-btn-text-icon',
            menu : componentMenu
        });

        Vpc.Paragraphs.Panel.superclass.initComponent.call(this);

        this.actions.edit.icon = '/assets/vps/images/paragraphEdit.png';
        this.actions['delete'].icon = '/assets/vps/images/paragraphDelete.png';

        this.on('beforerendergrid', function() {
            this.getStore().on('load', function() {
                Ext.each(Vps.contentReadyHandlers, function(i) {
                    i.fn.call(i.scope | window);
                }, this);
            }, this);
        }, this);
    },

    addComponents : function(components, addToItem)
    {
        if (components.length == 0) { return; }
        for (var i in components) {
            if (typeof components[i] == 'string') {
                addToItem.addItem(
                    new Ext.menu.Item({
                        id: components[i],
                        text: i,
                        handler: this.onParagraphAdd,
                        icon: this.componentIcons[components[i]],
                        scope: this
                    })
                );
            } else {
                var item = new Ext.menu.Item({text: i, menu: []});
                addToItem.addItem(item);
                this.addComponents(components[i], addToItem.items.items[addToItem.items.length - 1].menu);
            }
        }
    },

    edit : function(row) {
        var bp = this.getBaseParams();
        this.fireEvent('editcomponent', {
            componentClass: row.data.component_class,
            componentId: bp.componentId + '-' + row.data.id,
            text: row.data.component_name
        });
    },

    onParagraphAdd : function(o, e)
    {
        Ext.Ajax.request({
            url: this.controllerUrl + '/json-add-paragraph',
            params: Ext.apply ({ component : o.id}, this.getBaseParams()),
            success: function(r) {
                response = Ext.decode(r.responseText);
                if (response.hasController) {
                    this.fireEvent('editcomponent', {
                        componentClass: response.data.component_class,
                        componentId: this.getBaseParams().componentId + '-' + response.data.id,
                        text: response.data.component_name
                    });
                } else {
                    this.reload();
                }
            },
            scope: this
        });
    }
});

Ext.reg('vpc.paragraphs', Vpc.Paragraphs.Panel);
