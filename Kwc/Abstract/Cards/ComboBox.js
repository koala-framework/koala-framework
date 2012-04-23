Ext.ns('Kwc.Abstract.Cards');
Kwc.Abstract.Cards.ComboBox = Ext.extend(Kwf.Form.ComboBox, {
    setValue: function(v) {
        Kwc.Abstract.Cards.ComboBox.superclass.setValue.call(this, v);
    },

    doQuery : function(q, forceAll){
        this.store.baseParams.id = this.findParentByType('kwf.autoform').getBaseParams().id;
        this.store.baseParams.parent_id = this.findParentByType('kwf.autoform').getBaseParams().parent_id;
        delete this.lastQuery;
        return Kwc.Abstract.Cards.ComboBox.superclass.doQuery.apply(this, arguments);
    }
});
Ext.reg('kwc.abstract.cards.combobox', Kwc.Abstract.Cards.ComboBox);
