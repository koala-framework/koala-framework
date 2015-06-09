Ext2.namespace('Kwc.Abstract.List');
Kwc.Abstract.List.EditButton = Ext2.extend(Ext2.form.Field, {
    //bodyStyle: 'margin-left: 110px',
    defaultAutoCreate : {tag: "input", type: "hidden"},

    initComponent: function()
    {
        if (!this.editButtonText) this.editButtonText = trlKwf('Edit');

        Kwc.Abstract.List.EditButton.superclass.initComponent.call(this);
    },

    afterRender: function() {
        Kwc.Abstract.List.EditButton.superclass.afterRender.apply(this, arguments);
        this.button = new Ext2.Button({
            text: this.editButtonText,
            renderTo: this.el.parent(),
            icon: '/assets/silkicons/page_white_edit.png',
            cls: 'x2-btn-text-icon',
            scope: this,
            enabled: false,
            handler: function() {
                this.bubble(function(i) {
                    if (i instanceof Kwc.Abstract.List.PanelWithEditButton) {
                        var data = Kwf.clone(i.editComponents[0]);
                        data.componentId = i.getBaseParams().componentId + '-' + this.value;
                        data.editComponents = i.editComponents;
                        i.fireEvent('editcomponent', data);
                        return false;
                    }
                }, this);
            }
        });
    },

    setValue: function(v) {
        Kwc.Abstract.List.EditButton.superclass.setValue.apply(this, arguments);
        this.button.setDisabled(!v);
    }
});
Ext2.reg('kwc.listeditbutton', Kwc.Abstract.List.EditButton);
