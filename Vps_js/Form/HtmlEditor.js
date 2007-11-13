Vps.Form.HtmlEditor = Ext.extend(Ext.form.HtmlEditor, {
    formatBlocks : {
        'h1': 'Heading 1',
        'h2': 'Heading 2',
        'h3': 'Heading 3',
        'h4': 'Heading 4',
        'h5': 'Heading 5',
        'h6': 'Heading 6',
        'p': 'Normal',
        'address': 'Address',
        'pre': 'Formatted'
    },

    initComponent : function() {
        if (this.linkComponentConfig) {
            this.enableLinks = true;
            var cls = eval(this.linkComponentConfig['class']);
            var panel = new cls(this.linkComponentConfig.config);
            this.linkDialog = new Vps.Auto.Form.Window({
                autoForm: panel,
                width: 665,
                height: 400
            });
        }
        if (this.imageComponentConfig) {
            var cls = eval(this.imageComponentConfig['class']);
            var panel = new cls(this.imageComponentConfig.config);
            this.imageDialog = new Vps.Auto.Form.Window({
                autoForm: panel,
                width: 450,
                height: 400
            });
        }

        Vps.Form.HtmlEditor.superclass.initComponent.call(this);
    },
    createToolbar: function(editor){
        Vps.Form.HtmlEditor.superclass.createToolbar.call(this, editor);
        var tb = this.getToolbar();
        if (this.imageComponent) {
            tb.add('-');
            tb.add({
                icon: '/assets/silkicons/image.png',
                handler: this.createImage,
                scope: this,
                tooltip: {
                    cls: 'x-html-editor-tip',
                    title: 'Image',
                    text: 'Insert new image or edit selected image.'
                },
                cls: 'x-btn-icon',
                clickEvent: 'mousedown',
                tabIndex: -1
            });
        }
        tb.add({
            icon: '/assets/silkicons/text_letter_omega.png',
            handler: this.insertChar,
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: 'Character',
                text: 'Insert a custom character.'
            },
            cls: 'x-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        tb.add({
            icon: '/assets/silkicons/paste_plain.png',
            handler: this.insertPlainText,
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: 'Insert Plain Text',
                text: 'Insert text without formating.'
            },
            cls: 'x-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        tb.add({
            icon: '/assets/silkicons/html_valid.png',
            handler: this.tidyHtml,
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: 'Clean Html',
                text: 'Clean up Html and remove formatings.'
            },
            cls: 'x-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        this.blockSelect = tb.el.createChild({
            tag:'select',
            cls:'x-font-select',
            html: this.createBlockOptions()
        });
        this.blockSelect.on('change', function(){
            var v = this.blockSelect.dom.value;
            if (Ext.isIE) {
                v = '<'+v+'>';
            }
            this.relayCmd('formatblock', v);
            this.deferFocus();
        }, this);

    },

    createBlockOptions : function(){
        var buf = [];
        for (var i in this.formatBlocks) {
            fb = this.formatBlocks[i];
            buf.push(
                '<option value="',i,'"', /*style="font-family:',ff,';"',*/
                    (i == 'p' ? ' selected="true">' : '>'),
                    fb,
                '</option>'
            );
        }
        return buf.join('');
    },
    updateToolbar: function(){
        Vps.Form.HtmlEditor.superclass.updateToolbar.call(this);
        if (Ext.isIE) {
            var el = this.getFocusElement();
            while (el) {
                for(var i in this.formatBlocks) {
                    if (el.tagName && i == el.tagName.toLowerCase()) {
                        if(i != this.blockSelect.dom.value){
                            this.blockSelect.dom.value = i;
                        }
                        return;
                    }
                }
                el = el.parentNode;
            }
        } else {
            var name = (this.doc.queryCommandValue('FormatBlock')||'p').toLowerCase();
            if(name != this.blockSelect.dom.value){
                this.blockSelect.dom.value = name;
            }
        }
    },
//     getDocMarkup : function(){
//         return '<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;}</style></head><body></body></html>';
//     },
    setValue : function(v) {
        if (v.page_id && v.component_key) {
            this.page_id = v.page_id;
            this.component_key = v.component_key;
        }
        if (v.content) v = v.content;
        Vps.Form.HtmlEditor.superclass.setValue.call(this, v);
    },

    createImage: function() {
        var img = this.getFocusElement();
        if (img && img.tagName && img.tagName.toLowerCase() == 'img') {
            var expr = new RegExp('/media/[0-9]+/[^/]+/'+this.page_id+this.component_key+'-i([0-9]+)/');
            var m = img.src.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.imageDialog.showEdit({
                    page_id: this.page_id,
                    component_key: this.component_key+'-i'+nr
                });
                this.imageDialog.on('datachange', function(r) {
                    img.src = r.imageUrl;
                    img.width = r.imageDimension.width;
                    img.height = r.imageDimension.height;
                }, this, {single: true});
                return;
            }
        }
        Ext.Ajax.request({
            params: {page_id: this.page_id, component_key: this.component_key},
            url: this.controllerUrl+'/jsonAddImage',
            success: function(response, options, r) {
                this.imageDialog.showEdit({
                    page_id: r.page_id,
                    component_key: r.component_key
                });
                this.imageDialog.on('datachange', function(r) {
                    var html = '<img src="'+r.imageUrl+'" ';
                    html += 'width="'+r.imageDimension.width+'" ';
                    html += 'height="'+r.imageDimension.height+'" />'
                    this.insertAtCursor(html);
                }, this, {single: true});
            },
            scope: this
        });
    },
    createLink: function() {
        var a = this.getFocusElement();
        if (a && a.parentNode && (!a.tagName || a.tagName.toLowerCase() != 'a')) {
            a = a.parentNode;
        }
        if (a && a.tagName && a.tagName.toLowerCase() == 'a') {
            var expr = new RegExp(this.page_id+this.component_key+'-l([0-9]+)');
            var m = a.href.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.linkDialog.un('datachange', this._insertLink, this);
                this.linkDialog.showEdit({
                    page_id: this.page_id,
                    component_key: this.component_key+'-l'+nr
                });
                return;
            }
        }
        Ext.Ajax.request({
            params: {page_id: this.page_id, component_key: this.component_key},
            url: this.controllerUrl+'/jsonAddLink',
            success: function(response, options, r) {
                this.linkDialog.un('datachange', this._insertLink, this);
                this.linkDialog.showEdit({
                    page_id: r.page_id,
                    component_key: r.component_key
                });
                this.linkDialog.on('datachange', this._insertLink, this, { single: true });
            },
            scope: this
        });

    },
    _insertLink : function() {
        var params = this.linkDialog.getAutoForm().getBaseParams();
        this.relayCmd('createlink', params.page_id+params.component_key);
    },

    insertChar: function()
    {
        var win = Vps.Form.HtmlEditor.insertCharWindow; //statische var, nur ein window erstellen
        if (!win) {
            win = new Vps.Form.InsertCharWindow({
                modal: true,
                title: 'Insert Custom Character',
                width: 500,
                closeAction: 'hide',
                autoScroll: true
            });
            win.on('insertchar', function(win, char) {
                this.insertAtCursor(char);
                win.hide();
            }, this);
            Vps.Form.HtmlEditor.insertCharWindow = win;
        }
        win.show();
    },

    insertPlainText: function()
    {
        Ext.Msg.prompt('Insert Plain Text', '',
            function(btn, text) {
                if (btn == 'ok') {
                    this.insertAtCursor(text);
                }
            }, this, true);
    },

    tidyHtml: function()
    {
        Ext.getBody().mask('Cleaning...');
        Ext.Ajax.request({
            url: this.controllerUrl+'/jsonTidyHtml',
            params: { html: this.getValue() },
            success: function(response, options, r) {
                this.setValue(r.html);
            },
            callback: function() {
                Ext.getBody().unmask();
            },
            scope: this
        });
    },

    //private
	getFocusElement : function()
	{
        if (Ext.isIE) {
            var rng = this.doc.selection.createRange();
            return rng.item ? rng.item(0) : rng.parentElement();
        } else {
            this.win.focus(); //Von mir
            var sel = this.win.getSelection();
            if (!sel) return null;
            if (sel.rangeCount < 0) return null;

            var rng = sel.getRangeAt(0);
            if (!rng) return null;

            var elm = rng.commonAncestorContainer;

            // Handle selection a image or other control like element such as anchors
            if (!rng.collapsed) {
                // Is selection small
                if (rng.startContainer == rng.endContainer) {
                    if (rng.startOffset - rng.endOffset < 2) {
                        if (rng.startContainer.hasChildNodes())
                            elm = rng.startContainer.childNodes[rng.startOffset];
                    }
                }
            }

            return elm;
        }
    }
});
Ext.reg('htmleditor', Vps.Form.HtmlEditor);
