Kwf.Form.HtmlEditor.InsertChar = Ext2.extend(Ext2.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.on('initialize', this.onInit, this, {delay:100, single: true});
    },
    // private
    onInit: function(){
    },

    // private
    afterCreateToolbar: function() {
        var tb = this.cmp.getToolbar();
        tb.insert(9, {
            icon: KWF_BASE_URL+'/assets/silkicons/text_letter_omega.png',
            handler: this.onInsertChar,
            scope: this,
            tooltip: {
                cls: 'x2-html-editor-tip',
                title: trlKwf('Character'),
                text: trlKwf('Insert a custom character.')
            },
            cls: 'x2-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });

    },

    onInsertChar: function() {
        var win = Kwf.Form.HtmlEditor.InsertChar.insertCharWindow; //statische var, nur ein window erstellen
        if (!win) {
            win = new Kwf.Form.InsertCharWindow({
                modal: true,
                title: trlKwf('Insert Custom Character'),
                width: 500,
                closeAction: 'hide',
                autoScroll: true
            });
            Kwf.Form.HtmlEditor.insertCharWindow = win;
        }

        var bookmark = this.cmp.tinymceEditor.selection.getBookmark();
        win.purgeListeners();
        win.on('insertchar', function(win, ch) {
            this.cmp.tinymceEditor.selection.moveToBookmark(bookmark);
            this.cmp.insertAtCursor(ch);
            win.hide();
        }, this);
        win.show();
    }
});