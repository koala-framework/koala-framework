Vps.Form.HtmlEditor.InsertLink = function(config) {
    Ext.apply(this, config);

    var panel = Ext.ComponentMgr.create(Ext.applyIf(this.componentConfig, {
        baseCls: 'x-plain',
        formConfig: {
            tbar: false
        },
        autoLoad: false
    }));
    this.linkDialog = new Vps.Auto.Form.Window({
        autoForm: panel,
        width: 665,
        height: 400
    });
};
Ext.extend(Vps.Form.HtmlEditor.InsertLink, Ext.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.afterMethod('updateToolbar', this.updateToolbar, this);
    },

    // private
    afterCreateToolbar: function() {
        var tb = this.cmp.getToolbar();
        tb.insert(5, '-');
        this.action = new Ext.Action({
            handler: this.onInsertLink,
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: trlVps('Hyperlink'),
                text: trlVps('Create new Link for the selected text or edit selected Link.')
            },
            cls: 'x-btn-icon x-edit-createlink',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        tb.insert(6, this.action);
    },

    onInsertLink: function() {
        var a = this.cmp.getFocusElement('a');
        if (a && a.tagName && a.tagName.toLowerCase() == 'a') {
            var expr = new RegExp(this.componentId+'-l([0-9]+)');
            var m = a.href.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.linkDialog.un('datachange', this._insertLink, this);
                this.linkDialog.showEdit({
                    componentId: this.componentId+'-l'+nr
                });
                return;
            }
        }
        Ext.Ajax.request({
            params: {componentId: this.componentId},
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
        this.cmp.relayCmd('createlink', params.componentId);
    },

    // private
    updateToolbar: function() {
        var a = this.cmp.getFocusElement('a');
        if (a && a.tagName && a.tagName.toLowerCase() == 'a') {
            var expr = new RegExp(this.componentId+'-l[0-9]+');
            var m = a.href.match(expr);
            if (m) {
                this.action.enable();
            } else {
                this.action.disable();
            }
        } else {
            if (Ext.isIE) {
                var selection = this.cmp.doc.selection;
            } else {
                var selection = this.cmp.win.getSelection();
            }
            if (selection == '') {
                this.action.disable();
            } else {
                this.action.enable();
            }
        }
    }

});