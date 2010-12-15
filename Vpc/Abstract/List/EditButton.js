Ext.namespace('Vpc.Abstract.List');
Vpc.Abstract.List.EditButton = Ext.extend(Ext.form.Field, {
    //bodyStyle: 'margin-left: 110px',
    defaultAutoCreate : {tag: "input", type: "hidden"},

    initComponent: function()
    {
        if (!this.editButtonText) this.editButtonText = trlVps('Edit');

        Vpc.Abstract.List.EditButton.superclass.initComponent.call(this);
    },

    afterRender: function() {
        Vpc.Abstract.List.EditButton.superclass.afterRender.apply(this, arguments);
        this.button = new Ext.Button({
            text: this.editButtonText,
            renderTo: this.el.parent(),
            icon: '/assets/silkicons/page_white_edit.png',
            cls: 'x-btn-text-icon',
            scope: this,
            enabled: false,
            handler: function() {
                this.bubble(function(i) {
                    if (i instanceof Vpc.Abstract.List.PanelWithEditButton) {
                        var data = Vps.clone(i.editComponents[0]);
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
        Vpc.Abstract.List.EditButton.superclass.setValue.apply(this, arguments);
        this.button.setDisabled(!v);
    }
});
Ext.reg('vpc.listeditbutton', Vpc.Abstract.List.EditButton);
