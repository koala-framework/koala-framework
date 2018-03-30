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
            mainComponentIcon: '/assets/silkicons/email_open.png'
        });
        this.content.on('datachange', function () {
            this.preview.items.each(function(itm) {
                itm.load();
            }, this);
        }, this);

        this.preview = new Ext2.Panel({
            layout: 'card',
            title: trlKwf('Preview')
        });

        this.componentsGrid.on('selectionchange', this.onComponentsGridSelectionChange, this);

        this.items = [{
            xtype: 'tabpanel',
            region: 'center',
            activeTab: 0,
            deferredRender: false,
            items: [
                this.settings,
                this.content,
                this.preview
            ]
        }, this.componentsGrid];
        Kwc.Mail.Editable.Panel.superclass.initComponent.call(this);
    },

    onComponentsGridSelectionChange: function() {
        var record = this.componentsGrid.getSelected();
        if (!record) {
            this.settings.disable();
            this.preview.disable();
            return false;
        }
        this.settings.enable();
        this.preview.enable();

        this.renderSettingsPanel(record);
        this.renderPreviewPanel(record);

        this.content.setBaseParams({
            componentId: record.id+'-content'
        });
        this.content.load({
            editComponents: record.get('edit_components'),
            componentClass: record.get('edit_components')[0].componentClass,
            type: record.get('edit_components')[0].type
        });
    },

    renderSettingsPanel: function (record) {
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
    },

    renderPreviewPanel: function (record) {
        var i = false;
        this.preview.items.each(function(itm) {
            if (itm.controllerUrl == record.get('preview_controller_url')) {
                i = itm;
            }
        }, this);
        if (!i) {
            i = new Kwc.Mail.Editable.PreviewPanel({
                title: trlKwf('Preview'),
                region: 'center',
                controllerUrl: record.get('preview_controller_url')
            });
            this.preview.add(i);
            this.preview.doLayout();
        }
        this.preview.getLayout().setActiveItem(i);
        i.setBaseParams({
            componentId: record.id
        });
        i.load();
    }
});
Ext2.reg('kwc.mail.editable', Kwc.Mail.Editable.Panel);
