Ext2.ns('Kwc.Basic.LinkTag');
Kwc.Basic.LinkTag.ComboBox = Ext2.extend(Kwc.Abstract.Cards.ComboBox, {
    setValue: function(v) {
        Kwc.Basic.LinkTag.ComboBox.superclass.setValue.call(this, v);
        if (v == 'none') {
            this.el.up('.kwc-basic-linktag-form').child('fieldset.kwc-basic-linktag-seo').dom.style.display = 'none';
        } else {
            this.el.up('.kwc-basic-linktag-form').child('fieldset.kwc-basic-linktag-seo').dom.style.display = '';
        }
    }
});
Ext2.reg('kwc.basic.linktag.combobox', Kwc.Basic.LinkTag.ComboBox);
