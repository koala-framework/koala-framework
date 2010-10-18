Vps.Form.HtmlEditor.UndoRedo = Ext.extend(Ext.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.on('initialize', this.onInit, this, {delay:100, single: true});
    },
    // private
    onInit: function(){
    },

    // private
    afterCreateToolbar: function(tb) {
        tb.insert(0, {
            handler: function() {
                this.cmp.relayCmd('undo');
            },
            scope: this,
            icon: '/assets/silkicons/arrow_undo.png',
            tooltip: {
                cls: 'x-html-editor-tip',
                title: trlVps('Undo (Ctrl+Z)'),
                text: trlVps('Undo the last action.')
            },
            cls: 'x-btn-icon',
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
                cls: 'x-html-editor-tip',
                title: trlVps('Redo'),
                text: trlVps('Redo the last action.')
            },
            cls: 'x-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        tb.insert(2, '-');
    }
});