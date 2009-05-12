Ext.namespace('Vpc.Formular.Contact');
Vpc.Formular.Contact.Panel = Ext.extend(Vpc.Paragraphs.Panel,
{
    initComponent: function()
    {
        this.actions.settings = new Ext.Action({
            text: 'Settings',
            cls: 'x-btn-text-icon',
            icon: '/assets/silkicons/wrench.png',
            handler: function() {
                if (!this.settingsWindow) {
                    this.settingsWindow = new Vps.Auto.Form.Window({
                        width:  450,
                        height: 200,
                        title:  trlVps('Settings'),
                        modal:  true,
                        controllerUrl: this.controllerUrl + '!Settings'
                        });
                }
                this.settingsWindow.showEdit(this.getBaseParams());
            },
            scope: this
        });
        Vpc.Formular.Contact.Panel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.formular.contact', Vpc.Formular.Contact.Panel);
