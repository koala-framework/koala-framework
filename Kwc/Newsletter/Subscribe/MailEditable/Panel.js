Ext2.namespace('Kwc.Newsletter.Subscribe.MailEditable');
Kwc.Newsletter.Subscribe.MailEditable.Panel = Ext2.extend(Kwc.Mail.Editable.Panel, {
    layout: 'border',
    initComponent: function() {
        Kwc.Newsletter.Subscribe.MailEditable.Panel.superclass.initComponent.call(this);

        this.preview = new Kwc.Newsletter.Subscribe.MailEditable.PreviewPanel({
            title: trlKwf('Preview'),
            region: 'center',
            controllerUrl: this.previewControllerUrl
        });

        this.content.on('datachange', function () {
            this.preview.load();
        }, this);

        this.items.get(0).add(this.preview);
    },

    onComponentsGridSelectionChange: function () {
        Kwc.Newsletter.Subscribe.MailEditable.Panel.superclass.onComponentsGridSelectionChange.call(this);

        this.preview.setBaseParams({
            componentId: this.componentsGrid.getSelected().id
        });
        this.preview.load();
    }
});
Ext2.reg('kwc.newsletter.subscribe.mailEditable', Kwc.Newsletter.Subscribe.MailEditable.Panel);
