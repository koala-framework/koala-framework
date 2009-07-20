Ext.namespace('Vpc.Editable');
Vpc.Editable.Panel = Ext.extend(Ext.Panel, {
    layout: 'border',
    initComponent: function() {
        var componentsGrid = new Vps.Auto.GridPanel({
            controllerUrl: this.componentsControllerUrl,
            region: 'west',
            width: 200
        });

        var content = new Vps.Component.ComponentPanel({
            region: 'center',
            componentConfigs: this.componentConfigs,
            autoLoad: false,
            mainComponentText: trlVps('Content'),
            mainComponentIcon: '/assets/silkicons/page_white_text.png'
        });

        componentsGrid.on('selectionchange', function() {
            var record = componentsGrid.getSelected();
            if (!record) {
                content.disable();
                return;
            }
            content.enable();
            content.setBaseParams({
                componentId: record.id+'-content'
            });
            content.load({
                editComponents: record.get('edit_components'),
                componentClass: record.get('edit_components')[0].componentClass,
                type: record.get('edit_components')[0].type
            });
        }, this);

        this.items = [content, componentsGrid];
        Vpc.Editable.Panel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.editable', Vpc.Editable.Panel);
