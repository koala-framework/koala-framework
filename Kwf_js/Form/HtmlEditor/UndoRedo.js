Kwf.Form.HtmlEditor.UndoRedo = Ext2.extend(Ext2.util.Observable, {
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
        tb.insert(0, {
            handler: function() {
                this.cmp.relayCmd('undo');
            },
            scope: this,
            icon: '/assets/silkicons/arrow_undo.png',
            tooltip: {
                cls: 'x2-html-editor-tip',
                title: trlKwf('Undo (Ctrl+Z)'),
                text: trlKwf('Undo the last action.')
            },
            cls: 'x2-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        tb.insert(1, {
            handler: function() {
                this.cmp.relayCmd('redo');
            },
            scope: this,
            icon: '/assets/silkicons/arrow_redo.png',
            tooltip: {
                cls: 'x2-html-editor-tip',
                title: trlKwf('Redo'),
                text: trlKwf('Redo the last action.')
            },
            cls: 'x2-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        tb.insert(2, '-');
    }
});