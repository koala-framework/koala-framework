Ext.namespace('Vps.Form.Container.FieldSet');
Vps.Form.Container.FieldSet.Panel = Ext.extend(Vpc.Paragraphs.Panel,
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
        Vps.Form.Container.FieldSet.Panel.superclass.initComponent.call(this);
    }
});
Ext.reg('vps.form.container.fieldset', Vps.Form.Container.FieldSet.Panel);
