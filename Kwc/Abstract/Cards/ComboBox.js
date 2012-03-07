Ext.ns('Kwc.Abstract.Cards');
Kwc.Abstract.Cards.ComboBox = Ext.extend(Kwf.Form.ComboBox, {
    visibleCardForms: null,
    setValue: function(v) {
        if (v.visibleCardForms) {
            this.visibleCardForms = v.visibleCardForms;
            v = v.value;
        }
        Kwc.Abstract.Cards.ComboBox.superclass.setValue.call(this, v);
    },

    //(much) simplified copy from Ext.form.ComboBox
    doQuery : function(q, forceAll){
        this.selectedIndex = -1;
        this.store.filterBy(function(record) {
            if (this.visibleCardForms.indexOf(record.get('id')) != -1) {
                return true;
            }
            return false;
        }, this);
        this.onLoad();
    }
});
Ext.reg('kwc.abstract.cards.combobox', Kwc.Abstract.Cards.ComboBox);
