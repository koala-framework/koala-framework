Vps.Form.HtmlEditor.InsertDownload = function(config) {
    Ext.apply(this, config);

    var panel = Ext.ComponentMgr.create(Ext.applyIf(this.componentConfig, {
        baseCls: 'x-plain',
        formConfig: {
            tbar: false
        },
        autoLoad: false
    }));
    this.downloadDialog = new Vps.Auto.Form.Window({
        autoForm: panel,
        width: 450,
        height: 400
    });
};
Ext.extend(Vps.Form.HtmlEditor.InsertDownload, Ext.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.afterMethod('updateToolbar', this.updateToolbar, this);
    },

    // private
    afterCreateToolbar: function() {
        var tb = this.cmp.getToolbar();
        this.action = new Ext.Action({
            icon: '/assets/silkicons/folder_link.png',
            handler: this.onInsertDownload,
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: trlVps('Download'),
                text: trlVps('Create new Download for the selected text or edit selected Download.')
            },
            cls: 'x-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        tb.insert(7, this.action);
    },

    onInsertDownload: function() {
        var a = this.cmp.getFocusElement('a');
        if (a) {
            var expr = new RegExp(this.cmp.componentId+'-d([0-9]+)');
            var m = a.href.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.downloadDialog.un('datachange', this._insertDownloadLink, this);
                this.downloadDialog.showEdit({
                    componentId: this.cmp.componentId+'-d'+nr
                });
                return;
            }
        }
        Ext.Ajax.request({
            params: {componentId: this.cmp.componentId},
            url: this.cmp.controllerUrl+'/json-add-download',
            success: function(response, options, r) {
                this.downloadDialog.un('datachange', this._insertDownloadLink, this);
                this.downloadDialog.showEdit({
                    componentId: r.componentId
                });
                this.downloadDialog.on('datachange', this._insertDownloadLink, this, { single: true });
            },
            scope: this
        });
    },
    _insertDownloadLink : function() {
        var params = this.downloadDialog.getAutoForm().getBaseParams();
        this.relayCmd('createlink', params.componentId);
        this.cmp.updateToolbar();
    },

    // private
    updateToolbar: function() {
        var a = this.cmp.getFocusElement('a');
        if (a) {
            var expr = new RegExp(this.cmp.componentId+'-d[0-9]+');
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