Vps.Form.ComboBoxFilter = Ext.extend(Ext.Panel, {

    layout: 'form',
    border: false,
    baseCls: 'x-form-item',

    initComponent : function()
    {
        Vps.Form.ComboBoxFilter.superclass.initComponent.call(this);

        this.filterBox = this.items.items[0];
        this.saveBox   = this.items.items[1];

        this.firstChangeDone = false;

        this.filterBox.on('select', function(box, r, idx) {
            if (r.data.id) {
                this.saveBox.enable();
                this.saveBox.setValue(null);
            } else {
                this.saveBox.disable();
            }
        }, this);

        this.saveBox.on('changevalue', function(contactId) {
            this.saveBox.store.clearFilter();

            var saveStoreData = this.saveBox.store.getAt(
                this.saveBox.store.find('id', contactId)
            );

            if (saveStoreData && saveStoreData.data[this.saveBox.filterField]) {
                this.filterBox.setValue(saveStoreData.data[this.saveBox.filterField]);
                this.saveBox.enable();
            } else if (!this.firstChangeDone && !this.filterBox.defaultValue) {
                this.saveBox.disable();
            }

            this.firstChangeDone = true;
        }, this);

        this.saveBox.store.on('beforeload', function() {
            this.saveBox.store.baseParams.customer_id = this.filterBox.getValue();
        }, this);

        this.saveBox.on('expand', function(box, r, idx) {
            if (typeof this.saveBox.store.proxy == 'undefined') {
                this.saveBox.store.filterBy(function(r, id) {
                    if (!r.data[this.saveBox.filterField] ||
                        (r.data[this.saveBox.filterField] && r.data[this.saveBox.filterField] == this.filterBox.getValue())
                    ) {
                        return true;
                    }
                    return false;
                }, this);
            } else {
                if (this.saveBox.store.data.items.length >= 1) {
                    this.saveBox.store.reload();
                }
            }
        }, this);

        this.filterBox.setValue('');

    }


});
Ext.reg('comboboxfilter', Vps.Form.ComboBoxFilter);
