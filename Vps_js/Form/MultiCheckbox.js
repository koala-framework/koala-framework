Vps.Form.MultiCheckbox = Ext.extend(Vps.Form.FieldSet, {
    afterRender: function() {
        Vps.Form.MultiCheckbox.superclass.afterRender.call(this);

        if (this.showCheckAllLinks && this.header) {
            var checkAllWrapper = this.header.createChild({ tag: 'span', cls: 'vpsCheckAllWrapper' });
            checkAllWrapper.createChild({ tag: 'span', html: ' ('});
            var checkAllLink = checkAllWrapper.createChild({
                tag: 'a',
                href: '#',
                html: this.checkAllText
            });
            checkAllWrapper.createChild({ tag: 'span', html: ' / '});
            var checkNoneLink = checkAllWrapper.createChild({
                tag: 'a',
                href: '#',
                html: this.checkNoneText
            });
            checkAllWrapper.createChild({ tag: 'span', html: ')'});

            checkAllLink.on('click', function(ev) {
                ev.stopEvent();
                this.items.each(function(it) {
                    if (it.xtype == 'checkbox') {
                        it.setValue(true);
                    }
                });
            }, this);

            checkNoneLink.on('click', function(ev) {
                ev.stopEvent();
                this.items.each(function(it) {
                    if (it.xtype == 'checkbox') {
                        it.setValue(false);
                    }
                });
            }, this);
        }
    }
});

Ext.reg('multicheckbox', Vps.Form.MultiCheckbox);
