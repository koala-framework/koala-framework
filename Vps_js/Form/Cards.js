Vps.Form.Cards = Ext.extend(Ext.Panel,
{
    afterRender: function() {
        Vps.Form.Cards.superclass.afterRender.call(this);

        var combobox = this.items.first();

        combobox.on('changevalue', function(value) {
            var cards = this.items.get(1);
            cards.items.each(function(i) {
                if (i.name != value) {
                    i.cascade(function(it) {
                        it.disable();
                    }, this);
                } else {
                    cards.getLayout().setActiveItem(i);
                    i.cascade(function(it) {
                        if (!it.disabledByFieldset) {
                            it.enable();
                        }
                    }, this);
                }
            }, this);
        }, this);
    }

});
Ext.reg('vps.cards', Vps.Form.Cards);
