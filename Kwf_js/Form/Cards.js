Kwf.Form.Cards = Ext2.extend(Ext2.Panel,
{
    afterRender: function() {
        Kwf.Form.Cards.superclass.afterRender.call(this);

        var combobox = this.items.first();

        combobox.on('changevalue', function(value) {
            if (!this.disabled) this.changeValue(value);
        }, this);
    },

    enableRecursive: function() {
        this.enable();
        this.items.each(function(i) {
            i.enable();
        }, this);
        this.changeValue(this.items.first().getValue());
    },
    
    changeValue: function(value)
    {
        var cards = this.items.get(1);
        cards.items.each(function(i) {
            if (i.name != value) {
                i.hide(); //bugfix kitepowerbuchung muss es ganz verschwinden, nicht nur ausgrauen
                i.disableRecursive();
            } else {
                cards.getLayout().setActiveItem(i);
                i.show(); //bugfix für falsche anzeige bei kitepowerbuchung
                i.enableRecursive();
            }
        }, this);
    }

});
Ext2.reg('kwf.cards', Kwf.Form.Cards);
