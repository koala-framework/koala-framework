Vps.Form.ComboBoxFilter = Ext.extend(Ext.Panel, {

    layout: 'form',
    border: false,
    baseCls: 'x-form-item',

    initComponent : function()
    {
        Vps.Form.ComboBoxFilter.superclass.initComponent.call(this);

        this.filterBox = this.items.items[0];
        this.saveBox   = this.items.items[1];

        this.filterBox.on('select', function(box, r, idx) {
            if (r.data.id) {
                this.saveBox.enable();
            } else {
                this.saveBox.disable();
            }
        }, this);

        this.saveBox.on('changevalue', function(packageId) {
            this.saveBox.store.clearFilter();

            var saveStoreData = this.saveBox.store.getAt(
                this.saveBox.store.find('id', packageId)
            );

            if (saveStoreData && saveStoreData.data.filterId) {
                this.filterBox.setValue(saveStoreData.data.filterId);
                this.saveBox.enable();
            } else {
                this.saveBox.disable();
            }
        }, this);

        this.saveBox.on('expand', function(box, r, idx) {
            this.saveBox.store.filterBy(function(r, id) {
                if (!r.data.filterId ||
                    (r.data.filterId && r.data.filterId == this.filterBox.getValue())
                ) {
                    return true;
                }
                return false;
            }, this);
        }, this);

        this.filterBox.setValue('');

    }


});
Ext.reg('comboboxfilter', Vps.Form.ComboBoxFilter);
