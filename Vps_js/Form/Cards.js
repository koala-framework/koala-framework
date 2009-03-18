Vps.Form.Cards = Ext.extend(Ext.Panel,
{
    afterRender: function() {
        Vps.Form.Cards.superclass.afterRender.call(this);

        var combobox = this.items.first();

        combobox.on('changevalue', function(value) {
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
        }, this);
    },

    enableRecursive: function() {
        this.enable();
        this.items.each(function(i) {
            i.enable();
        }, this);
        var combobox = this.items.first();
        var value = combobox.getValue();
        var cards = this.items.get(1);
        cards.items.each(function(i) {
            if (i.name != value) {
                i.disableRecursive();
            } else {
                i.enableRecursive();
            }
        }, this);
    }

});
Ext.reg('vps.cards', Vps.Form.Cards);
