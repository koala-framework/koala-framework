Ext.ns('Kwf.Maintenance');
Kwf.Maintenance.SetupRequirements = Ext.extend(Ext.Panel, {
    border: false,
    cls: 'kwfSetupRequirements',
    autoScroll: true,
    initComponent: function() {
        this.resultTemplate = new Ext.XTemplate(
            '<ul>',
            '<tpl for="checks">',
            '<li class="{status}">',
                '{checkText}',
                '<tpl if="message">:<br />{message}</tpl>',
            '</li>',
            '</tpl>',
            '</ul>'
        );
        this.continueButton = new Ext.Button({
            text: 'Continue',
            handler: function() {
                this.fireEvent('continue');
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
        Ext.Ajax.request({
            url: '/kwf/maintenance/setup/json-check-requirements',
            mask: this.body,
            success: function(response, options, result) {
                this.resultTemplate.overwrite(this.body, result);
            },
            scope: this
        });
    }
});
