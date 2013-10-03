Ext.ns('Kwc.LinkTag.Intern');
Kwc.LinkTag.Intern.AnchorField = Ext.extend(Kwf.Form.ComboBox, {
    afterRender: function() {
        Kwc.LinkTag.Intern.AnchorField.superclass.afterRender.call(this);
        var pageSelect = this.ownerCt.items.items[0];
        pageSelect.on('changevalue', function(target) {
            this.currentTarget = this.ownerCt.items.items[0].getValue();
            this.clearValue();
            delete this.lastQuery;
        }, this);
    },

    getParams : function(q){
        var ret = Kwc.LinkTag.Intern.AnchorField.superclass.getParams.call(this, q);
        ret.target = this.currentTarget;
        return ret;
    }
});

Ext.reg('kwc.linktag.intern.anchor', Kwc.LinkTag.Intern.AnchorField);
