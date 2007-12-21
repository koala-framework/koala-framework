Vps.Form.FieldSet = Ext.extend(Ext.form.FieldSet, {
    checkboxToggle: false,
    checkboxCollapse: true,
    initComponent: function() {
        if (this.checkboxToggle && this.checkboxName) {
            this.hiddenCheckboxValue = new Vps.Form.FieldSetHiddenCheckboxValue({
                name: this.checkboxName
            });
            this.hiddenCheckboxValue.on('valuechange', function(field, value) {
                if (value=='0' || !value) {
                    if (this.checkboxCollapse) this.collapse();
                    this.cascade(function(i) {
                        if (i != this && i != this.hiddenCheckboxValue) {
                            i.disable();
                            i.clearInvalid();
                            i.disabledByFieldset = true; //hack, Kitepower ServiceDialog aktiviert das feld sonst wida
                        }
                    }, this);
                    this.checkbox.dom.checked = false;
                } else {
                    if (this.checkboxCollapse) this.expand();
                    this.cascade(function(i) {
                        if (i != this && i != this.hiddenCheckboxValue) {
                            i.enable();
                            delete i.disabledByFieldset;
                        }
                    }, this);
                    this.checkbox.dom.checked = true;
                }
                
            }, this);
            this.add(this.hiddenCheckboxValue);
            delete this.checkboxName;
        }
        Vps.Form.FieldSet.superclass.initComponent.call(this);
    },
    onCheckClick : function() {
        this.hiddenCheckboxValue.setValue(this.checkbox.dom.checked ? '1' : '0');
    }
});

Vps.Form.FieldSetHiddenCheckboxValue = Ext.extend(Ext.form.Hidden, {
    initComponent: function() {
        this.addEvents('valuechange');
        Vps.Form.FieldSetHiddenCheckboxValue.superclass.initComponent.call(this);
    },
    setValue : function(v) {
        Vps.Form.FieldSetHiddenCheckboxValue.superclass.setValue.call(this, v);
        this.fireEvent('valuechange', this, v);
    }
});

Ext.reg('fieldset', Vps.Form.FieldSet);
