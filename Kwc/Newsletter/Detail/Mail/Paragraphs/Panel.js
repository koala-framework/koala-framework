Ext2.namespace('Kwc.Newsletter.Detail.Mail.Paragraphs');
Kwc.Newsletter.Detail.Mail.Paragraphs.Panel = Ext2.extend(Kwc.Paragraphs.Panel,
    {
        initComponent: function () {
            Kwc.Newsletter.Detail.Mail.Paragraphs.Panel.superclass.initComponent.call(this);
            this.getTopToolbar().remove(this.actions.showPreviewWeb);
        }
    }
);
Ext2.reg('kwc.newsletter.detail.mail.paragraphs', Kwc.Newsletter.Detail.Mail.Paragraphs.Panel);
