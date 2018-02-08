Ext2.namespace('Kwc.Mail.Editable');
Kwc.Mail.Editable.Panel = Ext2.extend(Ext2.Panel, {
    layout: 'border',
    initComponent: function() {
        this.settings = new Ext2.Panel({
            layout: 'card',
            title: trlKwf('Settings')
        });
        this.componentsGrid = new Kwf.Auto.GridPanel({
            controllerUrl: this.componentsControllerUrl,
            region: 'west',
            width: 300
        });

        this.content = new Kwf.Component.ComponentPanel({
            title: trlKwf('Content'),
            componentConfigs: this.componentConfigs,
            autoLoad: false,
            mainComponentText: trlKwf('Content'),
            mainComponentIcon: KWF_BASE_URL+'/assets/silkicons/email_open.png'
        });

        this.componentsGrid.on('selectionchange', this.onComponentsGridSelectionChange, this);

        this.items = [{
            xtype: 'tabpanel',
            region: 'center',
            activeTab: 0,
            deferredRender: false,
            items: [
                this.settings,
                this.content
            ]
        }, this.componentsGrid];
        Kwc.Mail.Editable.Panel.superclass.initComponent.call(this);
    },

    onComponentsGridSelectionChange: function() {
        var record = this.componentsGrid.getSelected();
        if (!record) {
            this.settings.disable();
            return;
        }
        this.settings.enable();
        var i = false;
        this.settings.items.each(function(itm) {
            if (itm.controllerUrl == record.get('settings_controller_url')) {
                i = itm;
            }
        }, this);
        if (!i) {
            i = new Kwf.Auto.FormPanel({
                controllerUrl: record.get('settings_controller_url'),
                autoLoad: false
            });
            this.settings.add(i);
            this.settings.doLayout();
        }
        this.settings.getLayout().setActiveItem(i);
        i.setBaseParams({
            componentId: record.id
        });
        i.load();

        this.content.setBaseParams({
            componentId: record.id+'-content'
        });
        this.content.load({
            editComponents: record.get('edit_components'),
            componentClass: record.get('edit_components')[0].componentClass,
            type: record.get('edit_components')[0].type
        });
    }
});
Ext2.reg('kwc.mail.editable', Kwc.Mail.Editable.Panel);
