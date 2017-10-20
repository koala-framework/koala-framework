Ext2.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.TabPanel = Ext2.extend(Kwf.Binding.TabPanel,
{
    initComponent : function()
    {
        this.border = false;
        this.backButton = new Ext2.Action({
            text: trlKwf('Back'),
            icon: KWF_BASE_URL+'/assets/silkicons/arrow_left.png',
            cls: 'x2-btn-text-icon',
            handler: this.onBack,
            disabled: true,
            scope: this
        });
        this.nextButton = new Ext2.Action({
            text: trlKwf('Next'),
            icon: KWF_BASE_URL+'/assets/silkicons/arrow_right.png',
            cls: 'x2-btn-text-icon',
            handler: this.onNext,
            scope: this
        });
        this.tabPanelSettings = {
            bbar: ['->', this.backButton, '-', this.nextButton]
        };
        Kwc.Newsletter.Detail.TabPanel.superclass.initComponent.call(this);
    },

    onNext: function()
    {
        var activeItem = this.tabItems.indexOf(this.tabPanel.getActiveTab());
        if (this.backButton.isDisabled()) this.backButton.enable();
        this.tabPanel.setActiveTab(activeItem + 1);
        if (activeItem+2 == this.tabItems.length) this.nextButton.disable();
    },

    onBack: function()
    {
        var activeItem = this.tabItems.indexOf(this.tabPanel.getActiveTab());
        if (this.nextButton.isDisabled()) this.nextButton.enable();
        this.tabPanel.setActiveTab(activeItem - 1);
        if (activeItem-1 == 0) this.backButton.disable();
    }
});
Ext2.reg('kwc.newsletter.detail.tabpanel', Kwc.Newsletter.Detail.TabPanel);
