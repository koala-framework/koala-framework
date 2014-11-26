Kwf.Form.FieldSet = Ext2.extend(Ext2.form.FieldSet, {
    checkboxToggle: false,
    checkboxCollapse: true,
    initComponent: function() {
        this.monitorResize = true;
        if (this.checkboxToggle && this.checkboxName) {
            this.hiddenCheckboxValue = new Kwf.Form.Hidden({
                name: this.checkboxName
            });
            this.hiddenCheckboxValue.on('changevalue', function(value) {
                if (value=='0' || !value) {
                    if (this.checkboxCollapse) this.collapse();
                    this.items.each(function(i) {
                        if (i != this.hiddenCheckboxValue) {
                            i.disableRecursive();
                        }
                    }, this);
                    this.cascade(function(i) {
                        if (i != this && i != this.hiddenCheckboxValue) {
                            if (i.clearInvalid) {
                                i.clearInvalid();
                            }
                        }
                    }, this);
                    this.checkbox.dom.checked = false;
                } else {
                    if (this.checkboxCollapse) this.expand();
                    this.items.each(function(i) {
                        if (i != this.hiddenCheckboxValue) {
                            i.enableRecursive();
                        }
                    }, this);
                    this.checkbox.dom.checked = true;
                }

            }, this);
            this.add(this.hiddenCheckboxValue);
            delete this.checkboxName;
        }
        Kwf.Form.FieldSet.superclass.initComponent.call(this);
    },
    onCheckClick : function() {
        this.hiddenCheckboxValue.setValue(this.checkbox.dom.checked ? '1' : '0');
    },

    enableRecursive: function() {
        if (this.hiddenCheckboxValue && (!this.hiddenCheckboxValue.getValue() || this.hiddenCheckboxValue.getValue()=='0')) {
            this.items.each(function(i) {
                i.disableRecursive();
            }, this);
            this.enable();
        } else {
            Kwf.Form.FieldSet.superclass.enableRecursive.call(this);
        }
    },

    afterRender: function() {
        Kwf.Form.FieldSet.superclass.afterRender.call(this);
        if (this.helpText) {
            this.helpEl = this.getEl().createChild({
                tag: 'a',
                href: '#',
                style: 'display: block; width: 16px; height: 16px; float: right; '+
                       'background-image: url(/assets/silkicons/information.png)'
            }, this.getEl().down('legend'));
            this.helpEl.on('click', function(e) {
                e.stopEvent();
                var helpWindow = new Ext2.Window({
                    html: this.helpText,
                    width: 400,
                    bodyStyle: 'padding: 10px; background-color: white;',
                    autoHeight: true,
                    bodyBorder : false,
                    title: trlKwf('Info'),
                    resize: false
                });
                helpWindow.show();
            }, this);
            this.helpEl.alignTo(this.el, 'tr', [-10, -2]);
            this.on('afterlayout', function() {
                this.helpEl.alignTo(this.el, 'tr', [-30, -2]);
            }, this);
        }
        if (this.hiddenCheckboxValue) {
            //damit init-value in hiddenCheckboxValue geschrieben wird
            Kwf.Form.Hidden.superclass.setValue.call(this.hiddenCheckboxValue, this.checkbox.dom.checked ? '1' : '0');
        }
    }
});

Ext2.reg('fieldset', Kwf.Form.FieldSet);
