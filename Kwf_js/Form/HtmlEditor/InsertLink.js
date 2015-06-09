Kwf.Form.HtmlEditor.InsertLink = function(config) {
    Ext2.apply(this, config);

    var panel = Ext2.ComponentMgr.create(Ext2.applyIf(this.componentConfig, {
        baseCls: 'x2-plain',
        formConfig: {
            tbar: false
        },
        autoLoad: false
    }));
    this.linkDialog = new Kwf.Auto.Form.Window({
        autoForm: panel,
        width: 665,
        height: 400
    });
};
Ext2.extend(Kwf.Form.HtmlEditor.InsertLink, Ext2.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.afterMethod('updateToolbar', this.updateToolbar, this);
    },

    // private
    afterCreateToolbar: function() {
        var tb = this.cmp.getToolbar();
        tb.insert(5, '-');
        this.action = new Ext2.Action({
            testId: 'createlink',
            handler: this.onInsertLink,
            scope: this,
            tooltip: {
                cls: 'x2-html-editor-tip',
                title: trlKwf('Hyperlink'),
                text: trlKwf('Create new Link for the selected text or edit selected Link.')
            },
            cls: 'x2-btn-icon x2-edit-createlink',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        tb.insert(6, this.action);
    },

    onInsertLink: function() {
        var a = this.cmp.getFocusElement('a');
        if (a) {
            var expr = new RegExp(this.cmp.componentId+'-l([0-9]+)');
            var m = a.href.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.linkDialog.un('datachange', this._insertLink, this);
                this.linkDialog.showEdit({
                    componentId: this.cmp.componentId+'-l'+nr
                });
                return;
            }
        }
        this.beforeFocusBookmark = this.cmp.tinymceEditor.selection.getBookmark(1);
        Ext2.Ajax.request({
            params: {componentId: this.cmp.componentId},
            url: this.cmp.controllerUrl+'/json-add-link',
            success: function(response, options, r) {
                this.linkDialog.un('datachange', this._insertLink, this);
                this.linkDialog.showEdit({
                    componentId: r.componentId
                });
                this.linkDialog.on('datachange', this._insertLink, this, { single: true });
            },
            scope: this
        });
    },
    _insertLink : function() {
        var params = this.linkDialog.getAutoForm().getBaseParams();

        this.cmp.tinymceEditor.selection.moveToBookmark(this.beforeFocusBookmark);
        this.beforeFocusBookmark = null;
        this.cmp.focus();

        this.cmp.relayCmd('createlink', params.componentId);
        this.cmp.updateToolbar();
    },

    // private
    updateToolbar: function() {
        var a = this.cmp.getFocusElement('a');
        if (a) {
            var expr = new RegExp(this.cmp.componentId+'-l[0-9]+');
            var m = a.href.match(expr);
            if (m) {
                this.action.enable();
            } else {
                this.action.disable();
            }
        } else {
            if (this.cmp.tinymceEditor.selection.isCollapsed()) {
                this.action.disable();
            } else {
                this.action.enable();
            }
        }
    }

});