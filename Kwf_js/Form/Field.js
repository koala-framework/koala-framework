Ext.form.Field.prototype.afterRenderExt = Ext.form.Field.prototype.afterRender;

Ext.form.Field.prototype.helpTextOffset = [10, 2];
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
                    style: 'display: block; width: 16px; height: 16px; margin-bottom: -16px; '+
                        'background-image: url(/assets/silkicons/information.png)'
                });
                helpEl.on('click', function(e) {
                    e.stopEvent();
                    var helpWindow = new Ext.Window({
                        html: this.helpText,
                        width: 450,
                        bodyStyle: 'padding: 10px; background-color: white;',
                        autoHeight: true,
                        bodyBorder : false,
                        title: trlKwf('Info'),
                        resize: false
                    });
                    helpWindow.show();
                }, this);
                this.helpEl = Ext.get(helpEl);
            }
        }
        if (this.comment){
            var wrapDiv = this.getEl().up('div.x-form-element');
            if (wrapDiv) {
                var commentEl = wrapDiv.createChild({
                    html: this.comment,
                    tag: 'span'
                });
                this.commentEl = Ext.get(commentEl);
            }
        }
        this.alignHelpAndComment();
        this.alignHelpAndComment.defer(10, this);

        //re-align when tab is shown
        if (this.ownerCt) {
            this.ownerCt.bubble(function(c) {
                if (c.ownerCt instanceof Ext.TabPanel) {
                    c.on('show', this.alignHelpAndComment, this);
                }
            }, this);
        }
    },
    alignHelpAndComment: function() {
        if (this.helpEl) {
            this.helpEl.alignTo(this.getEl(), 'tr', this.helpTextOffset);
        }
        if (this.commentEl) {
            this.commentEl.alignTo(this.getEl(), 'tr', [5, 3]);
        }
    },
    setFormBaseParams: function(params) {
    }
});
