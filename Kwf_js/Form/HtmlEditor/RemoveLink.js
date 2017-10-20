Kwf.Form.HtmlEditor.RemoveLink = Ext2.extend(Ext2.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.afterMethod('updateToolbar', this.updateToolbar, this);
    },

    // private
    afterCreateToolbar: function() {
        var tb = this.cmp.getToolbar();
        this.action = new Ext2.Action({
            handler: this.onRemoveLink,
            icon: KWF_BASE_URL+'/assets/silkicons/link_break.png',
            scope: this,
            tooltip: {
                cls: 'x2-html-editor-tip',
                title: trlKwf('Remove Hyperlink'),
                text: trlKwf('Remove the selected link.')
            },
            cls: 'x2-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        tb.insert(6, this.action);
    },

    onRemoveLink: function() {
        var a = this.cmp.getFocusElement('a');
        this.cmp.tinymceEditor.selection.select(a);
        this.cmp.execCmd('unlink');
        this.cmp.updateToolbar();
    },

    updateToolbar: function() {
        var a = this.cmp.getFocusElement('a');
        if (a) {
            this.action.enable();
        } else {
            this.action.disable();
        }
    }

});