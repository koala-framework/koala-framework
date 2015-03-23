Kwf.Form.HtmlEditor.PastePlain = Ext2.extend(Ext2.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
    },

    // private
    afterCreateToolbar: function() {
        var tb = this.cmp.getToolbar();
        tb.insert(10, {
            icon: '/assets/kwf/images/pastePlain.gif',
            handler: this.onPastePlain,
            scope: this,
            tooltip: {
                cls: 'x2-html-editor-tip',
                title: trlKwf('Insert Plain Text'),
                text: trlKwf('Insert text without formating.')
            },
            cls: 'x2-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1

        });
        tb.insert(11, '-');
    },

    onPastePlain: function() {
        var bookmark = this.cmp.tinymceEditor.selection.getBookmark();
        Ext2.Msg.show({
            title : trlKwf('Insert Plain Text'),
            msg : '',
            buttons: Ext2.Msg.OKCANCEL,
            fn: function(btn, text) {
                if (btn == 'ok') {
                    this.cmp.tinymceEditor.selection.moveToBookmark(bookmark);
                    text = text.replace(/\r/g, '');
                    text = text.replace(/\n/g, '</p>\n<p>');
                    text = String.format('<p>{0}</p>', text);
                    this.cmp.tinymceEditor.editorCommands.execCommand('mceInsertContent', false, text);
                }
            },
            scope : this,
            minWidth: 500,
            prompt: true,
            multiline: 300
        });
    }
});
