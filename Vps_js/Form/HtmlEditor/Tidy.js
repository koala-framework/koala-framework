Vps.Form.HtmlEditor.Tidy = Ext.extend(Ext.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.on('initialize', this.onInit, this, {delay:100, single: true});
        this.cmp.afterMethod('createToolbar', this.afterCreateToolbar, this);
        this.cmp.afterMethod('toggleSourceEdit', this.toggleSourceEdit, this);
    },

    // private
    afterCreateToolbar: function() {
        var tb = this.cmp.getToolbar();
        // Jumpmark: #JM1
        // nach einfügen mit Strg+V in Firefox ist der knochen nicht sichtbar
        // dieses element wird nur dazu missbraucht, nach dem einfügen mit
        // Strg+V den focus aus dem editor zu nehmen um ihn dann wieder
        // reinplatzieren zu können
        tb.el.createChild({
            tag: 'a', cls: 'blurNode', href: '#', style: 'position: absolute; left: -5000px;'
        });
    },

    // private
    onInit: function(){
        Ext.EventManager.on(this.cmp.doc, 'keydown', function(e) {
            if(e.ctrlKey){
                var c = e.getCharCode();
                if(c > 0){
                    c = String.fromCharCode(c).toLowerCase();
                    if (c == 'v') {
                        if (!this.pasteDelayTask) {
                            var pasteClean = function() {
                                this.cmp.syncValue();

                                var bookmark = this.cmp.tinymceEditor.selection.getBookmark();
                                this.tidyHtml({
                                    params: { allowCursorSpan: true },
                                    callback: function() {
                                        this.cmp.tinymceEditor.selection.moveToBookmark(bookmark);
                                        this.cmp.syncValue();
                                    },
                                    scope: this
                                });
                            };
                            this.pasteDelayTask = new Ext.util.DelayedTask(pasteClean, this);
                        }
                        this.pasteDelayTask.delay(1);
                    }
                }
            }
        }, this);
    },

    toggleSourceEdit : function(sourceEditMode) {
        this.tidyHtml();
    },

    tidyHtml: function(tidyOptions)
    {
        this.cmp.mask(trlVps('Cleaning...'));

        var params = {
            componentId: this.cmp.componentId,
            html: this.cmp.getValue()
        };
        if (tidyOptions && tidyOptions.params) {
            Ext.applyIf(params, tidyOptions.params);
        }
        Ext.Ajax.request({
            url: this.cmp.controllerUrl+'/json-tidy-html',
            params: params,
            failure: function() {
                this.cmp.unmask();
            },
            success: function(response, options, r) {
                this.cmp.unmask();

                // Um den Knochen in Firefox sichtbar zu halten.
                // Weiteres zum blurNode: Suche nach #JM1 in dieser Datei.
                this.cmp.el.up('div').child('.blurNode', true).focus();
                this.cmp.deferFocus();

                if (this.cmp.getValue() != r.html) {
                    this.cmp.setValue(r.html);
                }

                if (tidyOptions && tidyOptions.callback) {
                    tidyOptions.callback.call(tidyOptions.scope || this);
                }
            },
            scope: this
        });
    }
});