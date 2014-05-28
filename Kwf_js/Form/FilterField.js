Kwf.Form.FilterField = Ext2.extend(Ext2.Panel, {
    layout: 'form',
    border: false,
    baseCls: 'x2-form-item',
    initComponent: function() {
        Kwf.Form.FilterField.superclass.initComponent.call(this);

        this.filterField   = this.items.items[0];
        this.filteredField = this.items.items[1];

        this.filterField.on('changevalue', function() {
            var value = this.filterField.getValue();
            if (value) {
                this.filteredField.enable();

                var filteredStoreData = this.filteredField.store.getAt(
                    this.filteredField.store.find('id', new RegExp("^" + this.filteredField.getValue() + "$"))
                );
                if (!filteredStoreData || filteredStoreData.data[this.filterColumn] != value) {
                    this.filteredField.setValue(null);
                }
            } else {
                this.filteredField.disable();
            }
        }, this);

        var f = this.filteredField;
        while (f instanceof Kwf.Form.FilterField) {
            f = f.filteredField;
        }
        if (!f.store) {
            throw new Error("filtered field doesn't have a store");
        }
        f.on('beforequery', function(qe) {
            this.filteredField.store.baseParams[this.filterColumn] = this.filterField.getValue();
            if (this.lastFilterValue != this.filterField.getValue()) {
                delete this.filteredField.lastQuery; //to force query being made always (documented ext feature)
                this.lastFilterValue = this.filterField.getValue();
            }
        }, this);

        this.filteredField.on('changevalue', function(value) {
            this.filteredField.store.clearFilter();

            var filteredStoreData = this.filteredField.store.getAt(
                this.filteredField.store.find('id', new RegExp("^" + value + "$"))
            );
            if (filteredStoreData && filteredStoreData.data[this.filterColumn]) {
                this.filterField.setValue(filteredStoreData.data[this.filterColumn]);
//                 this.filteredField.enable();
            } else if (!this.firstChangeDone && !this.filterField.defaultValue) {
                this.filteredField.valueToSetAfterLoad = value;
//                 this.filteredField.disable();
            }
            this.firstChangeDone = true;
        }, this);

        this.filteredField.store.on('load', function() {
            if (this.filteredField.valueToSetAfterLoad) {
                this.filteredField.setValue(this.filteredField.valueToSetAfterLoad);
                this.filteredField.valueToSetAfterLoad = null;
            }
        }, this);
    }
});
Ext2.reg('filterfield', Kwf.Form.FilterField);

