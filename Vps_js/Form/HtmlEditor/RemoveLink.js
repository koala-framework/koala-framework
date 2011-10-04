Vps.Form.HtmlEditor.RemoveLink = Ext.extend(Ext.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.afterMethod('updateToolbar', this.updateToolbar, this);
    },

    // private
    afterCreateToolbar: function() {
        var tb = this.cmp.getToolbar();
        this.action = new Ext.Action({
            handler: this.onRemoveLink,
            icon: '/assets/silkicons/link_break.png',
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: trlVps('Remove Hyperlink'),
                text: trlVps('Remove the selected link.')
            },
            cls: 'x-btn-icon',
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