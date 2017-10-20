Kwf.Form.HtmlEditor.UndoRedo = Ext2.extend(Ext2.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.on('initialize', this.onInit, this, {delay:100, single: true});
        this.cmp.afterMethod('updateToolbar', this.updateToolbar, this);
    },
    onInit: function(){
        this.cmp.tinymceEditor.on('Undo Redo AddUndo TypingUndo ClearUndos', (function() {
            this.updateToolbar();
        }).bind(this));
    },
    updateToolbar: function() {
        this.undoAction.setDisabled(!this.cmp.tinymceEditor.undoManager.hasUndo());
        this.redoAction.setDisabled(!this.cmp.tinymceEditor.undoManager.hasRedo());
    },
    afterCreateToolbar: function() {
        this.undoAction = new Ext2.Action({
            handler: function() {
                this.cmp.tinymceEditor.undoManager.undo();
            },
            scope: this,
            disabled: true,
            icon: KWF_BASE_URL+'/assets/silkicons/arrow_undo.png',
            tooltip: {
                cls: 'x2-html-editor-tip',
                title: trlKwf('Undo (Ctrl+Z)'),
                text: trlKwf('Undo the last action.')
            },
            cls: 'x2-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        this.redoAction = new Ext2.Action({
            handler: function() {
                this.cmp.tinymceEditor.undoManager.redo();
            },
            scope: this,
            disabled: true,
            icon: KWF_BASE_URL+'/assets/silkicons/arrow_redo.png',
            tooltip: {
                cls: 'x2-html-editor-tip',
                title: trlKwf('Redo'),
                text: trlKwf('Redo the last action.')
            },
            cls: 'x2-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });

        var tb = this.cmp.getToolbar();
        tb.insert(0, this.undoAction);
        tb.insert(1, this.redoAction);
        tb.insert(2, '-');
    }
});
