Ext.form.Field.prototype.afterRenderExt = Ext.form.Field.prototype.afterRender;
Ext.form.Field.override({
    getName: function() {
        //http://extjs.com/forum/showthread.php?t=15236
        return this.rendered && this.el.dom.name ? this.el.dom.name : (this.name || this.hiddenName || '');
    },
    resetDirty: function() {
        this.originalValue = this.getValue();
    },
    setDefaultValue: function() {
        this.setValue(this.defaultValue || '');
        this.resetDirty();
    },
    clearValue: function() {
        this.setValue('');
        this.resetDirty();
    },

    // Für Hilfetexte afterRender in Formularfields überschreiben
    afterRender: function() {
        this.afterRenderExt();
        if (this.helpText){
            var wrapDiv = this.getEl().up('div.x-form-item');
            if (wrapDiv) {
                var helpEl = wrapDiv.createChild({
                    tag: 'a',
                    href: '#',
                    style: 'display: block; width: 16px; height: 16px; '+
                        'position: absolute; '+
                        'background-image: url(/assets/silkicons/information.png)'
                });
                helpEl.on('click', function(e) {
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
                Ext.get(helpEl).alignTo(this.getEl(), 'tr', [10, 2]);
            }
        }
    }
});
