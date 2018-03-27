Ext2.namespace('Kwc.Newsletter.Subscribe.MailEditable');
Kwc.Newsletter.Subscribe.MailEditable.Panel = Ext2.extend(Kwc.Mail.Editable.Panel, {
    layout: 'border',
    initComponent: function() {
        Kwc.Newsletter.Subscribe.MailEditable.Panel.superclass.initComponent.call(this);

        this.preview = new Ext2.Panel({
            layout: 'card',
            title: trlKwf('Preview')
        });

        this.content.on('datachange', function () {
            this.preview.items.each(function(itm) {
                itm.load();
            }, this);
        }, this);

        this.items.get(0).add(this.preview);
    },

    onComponentsGridSelectionChange: function () {
        var ret = Kwc.Newsletter.Subscribe.MailEditable.Panel.superclass.onComponentsGridSelectionChange.call(this);
        if (ret === false) return false;

        var record = this.componentsGrid.getSelected();

        var i = false;
        this.preview.items.each(function(itm) {
            if (itm.controllerUrl == record.get('preview_controller_url')) {
                i = itm;
            }
        }, this);
        if (!i) {
            i = new Kwc.Newsletter.Subscribe.MailEditable.PreviewPanel({
                title: trlKwf('Preview'),
                region: 'center',
                controllerUrl: record.get('preview_controller_url')
            });
            this.preview.add(i);
            this.preview.doLayout();
        }
        this.preview.getLayout().setActiveItem(i);
        i.setBaseParams({
            componentId: record.get('id')
        });
        i.load();
    }
});
Ext2.reg('kwc.newsletter.subscribe.mailEditable', Kwc.Newsletter.Subscribe.MailEditable.Panel);
