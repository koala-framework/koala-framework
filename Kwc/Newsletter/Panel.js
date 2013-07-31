Ext.namespace('Kwc.Newsletter');
Kwc.Newsletter.Panel = Ext.extend(Kwc.Directories.Item.Directory.Panel,
{
    initComponent: function()
    {
        Kwc.Newsletter.Panel.superclass.initComponent.call(this);
        this.actions.add.setText(trlKwf('Add new newsletter'));
    },

    onAdd : function()
    {
        this.el.mask(trlKwf('Adding...'));
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-insert',
            params: this.getBaseParams(),
            success: function(response, options, r) {
                this.reload();
            },
            callback: function() {
                this.el.unmask();
            },
            scope : this
        });
        this.fireEvent('addaction', this);
    }
});
Ext.reg('kwc.newsletter', Kwc.Newsletter.Panel);
