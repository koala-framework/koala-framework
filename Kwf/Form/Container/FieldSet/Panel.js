Ext.namespace('Kwf.Form.Container.FieldSet');
Kwf.Form.Container.FieldSet.Panel = Ext.extend(Kwc.Paragraphs.Panel,
{
    initComponent: function()
    {
        this.actions.settings = new Ext.Action({
            text: 'Settings',
            cls: 'x-btn-text-icon',
            icon: '/assets/silkicons/wrench.png',
            handler: function() {
                if (!this.settingsWindow) {
                    this.settingsWindow = new Kwf.Auto.Form.Window({
                        width:  450,
                        height: 200,
                        title:  trlKwf('Settings'),
                        modal:  true,
                        controllerUrl: this.controllerUrl + '!Settings'
                        });
                }
                this.settingsWindow.showEdit(this.getBaseParams());
            },
            scope: this
        });
        Kwf.Form.Container.FieldSet.Panel.superclass.initComponent.call(this);
    }
});
Ext.reg('kwf.form.container.fieldset', Kwf.Form.Container.FieldSet.Panel);
