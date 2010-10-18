Vps.Form.HtmlEditor.InsertChar = Ext.extend(Ext.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.on('afterCreateToolbar', this.afterCreateToolbar, this);
        this.cmp.on('initialize', this.onInit, this, {delay:100, single: true});
    },
    // private
    onInit: function(){
    },

    // private
    afterCreateToolbar: function(tb) {
        tb.insert(7, {
            icon: '/assets/silkicons/text_letter_omega.png',
            handler: this.onInsertChar,
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: trlVps('Character'),
                text: trlVps('Insert a custom character.')
            },
            cls: 'x-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });

    },

    onInsertChar: function() {
        var win = Vps.Form.HtmlEditor.InsertChar.insertCharWindow; //statische var, nur ein window erstellen
        if (!win) {
            win = new Vps.Form.InsertCharWindow({
                modal: true,
                title: trlVps('Insert Custom Character'),
                width: 500,
                closeAction: 'hide',
                autoScroll: true
            });
            Vps.Form.HtmlEditor.insertCharWindow = win;
        }
        win.purgeListeners();
        win.on('insertchar', function(win, ch) {
            this.cmp.insertAtCursor(ch);
            win.hide();
        }, this);
        win.show();
    }
});