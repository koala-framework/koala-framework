Ext2.namespace('Kwc.Editable');
Kwc.Editable.Panel = Ext2.extend(Ext2.Panel, {
    layout: 'border',
    initComponent: function() {
        var componentsGrid = new Kwf.Auto.GridPanel({
            controllerUrl: this.componentsControllerUrl,
            region: 'west',
            width: 300
        });

        var content = new Kwf.Component.ComponentPanel({
            region: 'center',
            componentConfigs: this.componentConfigs,
            autoLoad: false,
            mainComponentText: trlKwf('Content'),
            mainComponentIcon: KWF_BASE_URL+'/assets/silkicons/page_white_text.png'
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
        Kwc.Editable.Panel.superclass.initComponent.call(this);
    }
});
Ext2.reg('kwc.editable', Kwc.Editable.Panel);
