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
        this.actions = {};

        //todo: lazy-loading von windows
        if (this.linkComponentConfig) {
            this.enableLinks = false;
            var cls = eval(this.linkComponentConfig['class']);
            var panel = new cls(Ext.applyIf(this.linkComponentConfig.config, {
                baseCls: 'x-plain',
                formConfig: {
                    tbar: false
                }
            }));
            this.linkDialog = new Vps.Auto.Form.Window({
                autoForm: panel,
                width: 665,
                height: 400
            });
        }
        if (this.imageComponentConfig) {
            var cls = eval(this.imageComponentConfig['class']);
            var panel = new cls(Ext.applyIf(this.imageComponentConfig.config, {
                baseCls: 'x-plain',
                formConfig: {
                    tbar: false
                }
            }));
            this.imageDialog = new Vps.Auto.Form.Window({
                autoForm: panel,
                width: 450,
                height: 400
            });
        }
        if (this.downloadComponentConfig) {
            var cls = eval(this.downloadComponentConfig['class']);
            var panel = new cls(Ext.applyIf(this.downloadComponentConfig.config, {
                baseCls: 'x-plain',
                formConfig: {
                    tbar: false
                }
            }));
            this.downloadDialog = new Vps.Auto.Form.Window({
                autoForm: panel,
                width: 450,
                height: 400
            });
        }

        Vps.Form.HtmlEditor.superclass.initComponent.call(this);
    },
    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'insertImage') {
            this.actions[type] = new Ext.Action({
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
        } else if (type == 'insertDownload') {
            this.actions[type] = new Ext.Action({
                icon: '/assets/silkicons/page_white.png',
                handler: this.createDownload,
                scope: this,
                tooltip: {
                    cls: 'x-html-editor-tip',
                    title: 'Download',
                    text: 'Create new Download for the selected text or edit selected Download.'
                },
                cls: 'x-btn-icon',
                clickEvent: 'mousedown',
                tabIndex: -1
            });
        } else if (type == 'insertLink') {
            this.actions[type] = new Ext.Action({
                handler: this.createLink,
                scope: this,
                tooltip: {
                    cls: 'x-html-editor-tip',
                    title: 'Hyperlink',
                    text: 'Create new Link for the selected text or edit selected Link.'
                },
                cls: 'x-btn-icon x-edit-createlink',
                clickEvent: 'mousedown',
                tabIndex: -1
            });
        } else {
            throw 'unknown action-type: ' + type;
        }
        return this.actions[type];
    },
    initEditor : function() {
        Vps.Form.HtmlEditor.superclass.initEditor.call(this);
        Ext.EventManager.on(this.doc, 'keypress', function(e) {
            if(e.ctrlKey){
                var c = e.getCharCode(), cmd;
                if(c > 0){
                    c = String.fromCharCode(c);
                    if (c == 'v') {
                        //tidy on paste
                        Ext.getBody().mask('Cleaning...');
                        this.tidyHtml.defer(500, this);
                    }
                }
            }
        }, this);
    },
    createToolbar: function(editor){
        Vps.Form.HtmlEditor.superclass.createToolbar.call(this, editor);
        var tb = this.getToolbar();
        tb.insert(7, '-');
        if (this.linkDialog) {
            tb.insert(8,  this.getAction('insertLink'));
        }
        if (this.imageDialog) {
            tb.insert(9, this.getAction('insertImage'));
        }
        if (this.downloadDialog) {
            tb.insert(10,  this.getAction('insertDownload'));
        }
        this.linkComponentConfig
        tb.add('-');
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
        tb.insert(0, this.blockSelect.dom);
        tb.insert(1, '-');

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
        var a = this.getFocusElement('a');
        if (a && a.tagName && a.tagName.toLowerCase() == 'a') {
            var expr = new RegExp(this.page_id+this.component_key+'-(l|d)([0-9]+)');
            var m = a.href.match(expr);
            if (m) {
                if (m[1] == 'l') {
                    this.getAction('insertLink').enable();
                    this.getAction('insertDownload').disable();
                } else if (m[1] == 'd') {
                    this.getAction('insertLink').disable();
                    this.getAction('insertDownload').enable();
                }
            } else {
                this.getAction('insertLink').disable();
                this.getAction('insertDownload').disable();
            }
        } else {
            if (Ext.isIE) {
                var selection = this.doc.selection;
            } else {
                var selection = this.doc.getSelection();
            }
            if (selection == '') {
                this.getAction('insertLink').disable();
                this.getAction('insertDownload').disable();
            } else {
                this.getAction('insertLink').enable();
                this.getAction('insertDownload').enable();
            }
        }
    },
    getDocMarkup : function(){
        return '<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;}</style>'+
               '<link rel="stylesheet" type="text/css" href="/assets/AllFrontend.css" />'+
               '</head><body class="content"></body></html>';
    },
    setValue : function(v) {
        if (v.page_id && v.component_key) {
            this.page_id = v.page_id;
            this.component_key = v.component_key;
        }
        if (v.content) v = v.content;
        Vps.Form.HtmlEditor.superclass.setValue.call(this, v);
    },

    createImage: function() {
        var img = this.getFocusElement('img');
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
        var a = this.getFocusElement('a');
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

    createDownload: function() {
        var a = this.getFocusElement('a');
        if (a && a.tagName && a.tagName.toLowerCase() == 'a') {
            var expr = new RegExp(this.page_id+this.component_key+'-d([0-9]+)');
            var m = a.href.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.downloadDialog.un('datachange', this._insertDownloadLink, this);
                this.downloadDialog.showEdit({
                    page_id: this.page_id,
                    component_key: this.component_key+'-d'+nr
                });
                return;
            }
        }
        Ext.Ajax.request({
            params: {page_id: this.page_id, component_key: this.component_key},
            url: this.controllerUrl+'/jsonAddDownload',
            success: function(response, options, r) {
                this.downloadDialog.un('datachange', this._insertDownloadLink, this);
                this.downloadDialog.showEdit({
                    page_id: r.page_id,
                    component_key: r.component_key
                });
                this.downloadDialog.on('datachange', this._insertDownloadLink, this, { single: true });
            },
            scope: this
        });
    },

    _insertDownloadLink : function() {
        var params = this.downloadDialog.getAutoForm().getBaseParams();
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
            params: {
                page_id: this.page_id,
                component_key: this.component_key,
                html: this.getValue()
            },
            success: function(response, options, r) {
                if (this.getValue() != r.html) {
                    this.setValue(r.html);
                }
            },
            callback: function() {
                Ext.getBody().unmask();
            },
            scope: this
        });
    },

    //private
	getFocusElement : function(tag)
	{
        if (Ext.isIE) {
            var rng = this.doc.selection.createRange();
            var elm = rng.item ? rng.item(0) : rng.parentElement();
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
        }
        if (tag && elm) {
            while (elm && elm.parentNode &&
                    (!elm.tagName || elm.tagName.toLowerCase() != tag)) {
                elm = elm.parentNode;
            }
        }
        return elm;
    },

    //protected
    toggleSourceEdit : function(sourceEditMode) {
        this.tidyHtml();
        Vps.Form.HtmlEditor.superclass.toggleSourceEdit.call(this, sourceEditMode);
    }
});
Ext.reg('htmleditor', Vps.Form.HtmlEditor);
