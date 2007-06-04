Vps.Admin.Page.Form = function(renderTo, config)
{
    Ext.apply(this, config);
    this.events = {
        'saved' : true
    };

    this.form = new Ext.form.Form({
        labelAlign: 'right',
        labelWidth: 75,
        url: '/admin/page/ajaxSaveComponent',
        baseParams: {}
    });
    

    this.form.fieldset({legend:'Decorators', hideLabels:true});
    for (var dName in config.decorators) {
        this.form.add(new Ext.form.Checkbox({
            boxLabel: config.decorators[dName],
            name: 'decorators[' + dName + ']',
            disabled: true
        }));
    }
    this.form.end();

    this.form.addButton({
        id: 'save',
        disabled: true,
        text    : 'Speichern',
        handler : this.onSave,
        scope   : this
    });

    this.form.render(renderTo);
    
    this.setup = function(id, selectedDecorators) {
        this.form.items.each(function(b) { b.enable(); });
        this.form.buttons[0].enable();
        this.form.baseParams.id = id;
        this.form.reset();
        for (var i in selectedDecorators) {
            var d = this.form.findField('decorators[' + selectedDecorators[i] + ']');
            if (d) { d.setValue(true); }
        }
    }
}

Ext.extend(Vps.Admin.Page.Form, Ext.util.Observable,
{
    onSave: function(o, e) {
        this.form.submit({
            success: function(form, a, b) {
                this.fireEvent('saved', a.result);
            },
            scope: this
        })
    }

});
