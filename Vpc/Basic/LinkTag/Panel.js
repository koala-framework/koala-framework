Ext.namespace('Vpc.Basic.LinkTag');
Vpc.Basic.LinkTag.Panel = Ext.extend(Vps.Auto.FormPanel, {
    initComponent : function()
    {
        Vpc.Basic.LinkTag.Panel.superclass.initComponent.call(this);
        this.on('loadform', function(o, e) {
            this.getForm().findField('LinkClass').on('changevalue', function(value) {
                var cards = this.findById('CardsContainer');
                cards.layout.setActiveItem(value);
                cards.items.each(function(item){
                    if (item.id != value) {
                        item.cascade(function(i) {
                            i.disable();
                        }, this);
                    } else {
                        item.cascade(function(i) {
                            i.enable();
                        }, this);
                    }
                }, this);
            }, this);
        }, this);
    }
});
