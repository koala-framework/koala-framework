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
                var label = wrapDiv.child('label');
                if (label) {
                    if (this.width) {
                        var style = 'position:absolute; margin-left:' + (this.width + 10) + 'px'
                    } else {
                        var style = 'margin-bottom: 0px; margin-left: 5px; padding: 0px;';
                    }
                    var helpImage = label.createChild({
                        tag: 'img', 
                        src: '/assets/silkicons/information.png',
                        style: style,
                        width: 16,
                        height: 16
                    });
                    Ext.QuickTips.register({
                        target:  helpImage,
                        title: '',
                        text: this.helpText,
                        enabled: true
                    });
                }
            }
        }
    }
});
