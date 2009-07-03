Ext.namespace('Vpc.Mail.Editable');
Vpc.Mail.Editable.Panel = Ext.extend(Ext.Panel, {
    layout: 'border',
    initComponent: function() {
        var settings = new Ext.Panel({
            layout: 'card',
            title: trlVps('Settings')
        });
        var componentsGrid = new Vps.Auto.GridPanel({
            controllerUrl: this.componentsControllerUrl,
            region: 'west',
            width: 200
        });

        var content = new Vps.Component.ComponentPanel({
            title: trlVps('Content'),
            componentConfigs: this.componentConfigs,
            autoLoad: false,
            mainComponentText: trlVps('Content'),
            mainComponentIcon: '/assets/silkicons/email_open.png'
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
                i = new Vps.Auto.FormPanel({
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
        Vpc.Mail.Editable.Panel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.mail.editable', Vpc.Mail.Editable.Panel);
