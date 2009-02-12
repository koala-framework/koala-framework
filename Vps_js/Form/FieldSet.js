Vps.Form.FieldSet = Ext.extend(Ext.form.FieldSet, {
    checkboxToggle: false,
    checkboxCollapse: true,
    initComponent: function() {
        this.monitorResize = true;
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
                            if (i.clearInvalid) {
                                i.clearInvalid();
                            }
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
    },
    afterRender: function() {
        Vps.Form.FieldSet.superclass.afterRender.call(this);
        if (this.helpText) {
            this.helpEl = this.getEl().createChild({
                tag: 'a',
                href: '#',
                style: 'display: block; width: 16px; height: 16px; float: right; '+
                       'background-image: url(/assets/silkicons/information.png)'
            }, this.getEl().down('legend'));
            this.helpEl.on('click', function(e) {
                e.stopEvent();
                var helpWindow = new Ext.Window({
                    html: this.helpText,
                    width: 400,
                    bodyStyle: 'padding: 10px; background-color: white;',
                    autoHeight: true,
                    bodyBorder : false,
                    title: trlVps('Info'),
                    resize: false
                });
                helpWindow.show();
            }, this);
            this.helpEl.alignTo(this.el, 'tr', [-10, -2]);
            this.on('afterlayout', function() {
                this.helpEl.alignTo(this.el, 'tr', [-30, -2]);
            }, this);
        }
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
