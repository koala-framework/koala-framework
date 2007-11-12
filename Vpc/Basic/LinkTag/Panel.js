Ext.namespace('Vpc.Basic.LinkTag');
Vpc.Basic.LinkTag.Panel = Ext.extend(Vps.Auto.FormPanel, {
    initComponent : function()
    {
        Vpc.Basic.LinkTag.Panel.superclass.initComponent.call(this);
        this.on('loadform', function(o, e) {

            var baseParams = this.baseParams;
            var field = this.getForm().findField('LinkClass');
            field.on('changevalue', function(value) {
                var container = this.findById('CardsContainer');
                container.layout.setActiveItem(value);
            }, this);
        }, this);
    }
});
