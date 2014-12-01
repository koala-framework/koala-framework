Ext2.ns('Kwf.Maintenance');
Kwf.Maintenance.SetupRequirements = Ext2.extend(Ext2.Panel, {
    border: false,
    cls: 'kwfSetupRequirements',
    autoScroll: true,
    initComponent: function() {
        this.resultTemplate = new Ext2.XTemplate(
            '<ul>',
            '<tpl for="checks">',
            '<li class="{status}">',
                '{checkText}',
                '<tpl if="message">:<br />{message}</tpl>',
            '</li>',
            '</tpl>',
            '</ul>'
        );
        this.continueButton = new Ext2.Button({
            text: 'Continue',
            handler: function() {
                if (this.hasFailedCheck) {
                    Ext2.Msg.alert('Error', 'Please fix all failed (red) checks before continuing, else the setup will fail.');
                } else {
                    this.fireEvent('continue');
                }
            },
            scope: this
        });
        this.buttons = [{
            text: 'Refresh',
            handler: function() {
                this.refresh();
            },
            scope: this
        }, this.continueButton];
        Kwf.Maintenance.SetupRequirements.superclass.initComponent.call(this);
    },
    refresh: function() {
        Ext2.Ajax.request({
            url: '/kwf/maintenance/setup/json-check-requirements',
            mask: this.body,
            success: function(response, options, result) {
                this.resultTemplate.overwrite(this.body, result);
                this.hasFailedCheck = false;
                result.checks.each(function(c) {
                    if (c.status == 'failed') {
                        this.hasFailedCheck = true;
                    }
                }, this);
            },
            scope: this
        });
    }
});
