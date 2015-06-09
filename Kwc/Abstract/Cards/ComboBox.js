Ext2.ns('Kwc.Abstract.Cards');
Kwc.Abstract.Cards.ComboBox = Ext2.extend(Kwf.Form.ComboBox, {
    setValue: function(v) {
        Kwc.Abstract.Cards.ComboBox.superclass.setValue.call(this, v);
    },

    doQuery : function(q, forceAll){
        var form = this.findParentBy(function(p) { return (p instanceof Kwf.Auto.FormPanel); });
        this.store.baseParams.id = form.getBaseParams().id;
        this.store.baseParams.parent_id = form.getBaseParams().parent_id;
        delete this.lastQuery;
        return Kwc.Abstract.Cards.ComboBox.superclass.doQuery.apply(this, arguments);
    }
});
Ext2.reg('kwc.abstract.cards.combobox', Kwc.Abstract.Cards.ComboBox);
