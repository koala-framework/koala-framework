Ext2.namespace('Kwc.Newsletter');
Kwc.Newsletter.Panel = Ext2.extend(Kwc.Directories.Item.Directory.Panel,
{
    initComponent: function()
    {
        Kwc.Newsletter.Panel.superclass.initComponent.call(this);
        this.actions.add.setText(trlKwf('Add new newsletter'));
    },

    onAdd : function()
    {
        this.el.mask(trlKwf('Adding...'));
        Ext2.Ajax.request({
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
Ext2.reg('kwc.newsletter', Kwc.Newsletter.Panel);
