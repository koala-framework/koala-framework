Ext2.namespace('Kwc.Mail.Editable');
Kwc.Mail.Editable.Panel = Ext2.extend(Ext2.Panel, {
    layout: 'border',
    initComponent: function() {
        var settings = new Ext2.Panel({
            layout: 'card',
            title: trlKwf('Settings')
        });
        var componentsGrid = new Kwf.Auto.GridPanel({
            controllerUrl: this.componentsControllerUrl,
            region: 'west',
            width: 300
        });

        var content = new Kwf.Component.ComponentPanel({
            title: trlKwf('Content'),
            componentConfigs: this.componentConfigs,
            autoLoad: false,
            mainComponentText: trlKwf('Content'),
            mainComponentIcon: KWF_BASE_URL+'/assets/silkicons/email_open.png'
        });

        componentsGrid.on('selectionchange', function() {
            var record = componentsGrid.getSelected();
            if (!record) {
                settings.disable();
                return;
            }
            settings.enable();
            var i = false;
            settings.items.each(function(itm) {
                if (itm.controllerUrl == record.get('settings_controller_url')) {
                    i = itm;
                }
            }, this);
            if (!i) {
                i = new Kwf.Auto.FormPanel({
                    controllerUrl: record.get('settings_controller_url'),
                    autoLoad: false
                });
                settings.add(i);
                settings.doLayout();
            }
            settings.getLayout().setActiveItem(i);
            i.setBaseParams({
                componentId: record.id
            });
            i.load();

            content.setBaseParams({
                componentId: record.id+'-content'
            });
            content.load({
                editComponents: record.get('edit_components'),
                componentClass: record.get('edit_components')[0].componentClass,
                type: record.get('edit_components')[0].type
            });
        }, this);

        this.items = [{
            xtype: 'tabpanel',
            region: 'center',
            activeTab: 0,
            deferredRender: false,
            items: [
                settings,
                content
            ]
        }, componentsGrid];
        Kwc.Mail.Editable.Panel.superclass.initComponent.call(this);
    }
});
Ext2.reg('kwc.mail.editable', Kwc.Mail.Editable.Panel);
