Ext.namespace('Vpc.Columns');
Vpc.Columns.EditButton = Ext.extend(Ext.form.Field, {
    //bodyStyle: 'margin-left: 110px',
    defaultAutoCreate : {tag: "input", type: "hidden"},
    initComponent: function() {
        Vpc.Columns.EditButton.superclass.initComponent.call(this);
    },
    afterRender: function() {
        Vpc.Columns.EditButton.superclass.afterRender.apply(this, arguments);
        this.button = new Ext.Button({
            text: trlVps('Edit Column'),
            renderTo: this.el.parent(),
            icon: '/assets/silkicons/page_white_edit.png',
            cls: 'x-btn-text-icon',
            scope: this,
            enabled: false,
            handler: function() {
                this.bubble(function(i) {
                    if (i instanceof Vpc.Columns.Panel) {
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
        Vpc.Columns.EditButton.superclass.setValue.apply(this, arguments);
        this.button.setDisabled(!v);
    }
});
Ext.reg('vpc.columns.editbutton', Vpc.Columns.EditButton);
