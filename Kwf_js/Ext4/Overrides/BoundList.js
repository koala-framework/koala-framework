Ext4.define('Kwf.Ext4.Overrides.BoundList', {
    override: 'Ext.view.BoundList',
    showCheckbox: false,
    initComponent: function() {
        this.callParent(arguments);
        if (this.showCheckbox) {
            this.cls = 'kwf-boundlist-checkbox';
        }
    }
});
