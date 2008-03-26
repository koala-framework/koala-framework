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
    enableUndoRedo: true,

    initComponent : function()
    {
        if (!this.actions) this.actions = {};

        this.actions.insertImage = new Ext.Action({
            icon: '/assets/silkicons/picture.png',
            handler: this.createImage,
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: 'Image',
                text: trlVps('Insert new image or edit selected image.')
            },
            cls: 'x-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        this.actions.insertDownload = new Ext.Action({
            icon: '/assets/silkicons/folder_link.png',
            handler: this.createDownload,
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
        this.actions.insertLink = new Ext.Action({
            handler: this.createLink,
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
        this.actions.undo = new Ext.Action({
            handler: this.undo,
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
        this.actions.redo = new Ext.Action({
            handler: this.redo,
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

        //todo: lazy-loading von windows
        if (this.linkComponentConfig) {
            this.enableLinks = false;
            var panel = Ext.ComponentMgr.create(Ext.applyIf(this.linkComponentConfig, {
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
        }
        if (this.imageComponentConfig) {
            var panel = Ext.ComponentMgr.create(Ext.applyIf(this.imageComponentConfig, {
                baseCls: 'x-plain',
                formConfig: {
                    tbar: false
                }
            }));
            this.imageDialog = new Vps.Auto.Form.Window({
                autoForm: panel,
                width: 450,
                height: 400,
                autoLoad: false
            });
        }
        if (this.downloadComponentConfig) {
            var panel = Ext.ComponentMgr.create(Ext.applyIf(this.downloadComponentConfig, {
                baseCls: 'x-plain',
                formConfig: {
                    tbar: false
                }
            }));
            this.downloadDialog = new Vps.Auto.Form.Window({
                autoForm: panel,
                width: 450,
                height: 400,
                autoLoad: false
            });
        }

        Vps.Form.HtmlEditor.superclass.initComponent.call(this);
    },
    getAction : function(type)
    {
        return this.actions[type];
    },
    initEditor : function() {
        Vps.Form.HtmlEditor.superclass.initEditor.call(this);
        if (this.controllerUrl && this.enableTidy) {
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
        }
    },
    createToolbar: function(editor){
        Vps.Form.HtmlEditor.superclass.createToolbar.call(this, editor);
        var tb = this.getToolbar();

        if (this.linkDialog) {
            tb.insert(8,  this.getAction('insertLink'));
        }
        if (this.imageDialog) {
            tb.insert(9, this.getAction('insertImage'));
        }
        if (this.downloadDialog) {
            tb.insert(10,  this.getAction('insertDownload'));
        }
        if (this.linkDialog || this.imageDialog || this.downloadDialog) {
            tb.insert(11, '-');
        }

        tb.add('-');
        if (this.enableInsertChar) {
            tb.add({
                icon: '/assets/silkicons/text_letter_omega.png',
                handler: this.insertChar,
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
        }
        if (this.enablePastePlain) {
            tb.add({
                icon: '/assets/vps/images/pastePlain.gif',
                handler: this.insertPlainText,
                scope: this,
                tooltip: {
                    cls: 'x-html-editor-tip',
                    title: trlVps('Insert Plain Text'),
                    text: trlVps('Insert text without formating.')
                },
                cls: 'x-btn-icon',
                clickEvent: 'mousedown',
                tabIndex: -1
            });
        }

        if (this.controllerUrl && this.enableTidy) {
            tb.add({
                icon: '/assets/silkicons/html_valid.png',
                handler: this.tidyHtml,
                scope: this,
                tooltip: {
                    cls: 'x-html-editor-tip',
                    title: trlVps('Clean Html'),
                    text: trlVps('Clean up Html and remove formatings.')
                },
                cls: 'x-btn-icon',
                clickEvent: 'mousedown',
                tabIndex: -1
            });
        }

        if (this.enableBlock) {
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
        }
        if (this.enableUndoRedo) {
            var offs = 0;
            if (this.enableBlock) offs += 2;
            if (this.enableFont) offs += 2;
            tb.insert(offs, this.getAction('undo'));
            tb.insert(offs+1, this.getAction('redo'));
            tb.insert(offs+2, '-');
        }
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
        if (this.blockSelect) {
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
        }
        var a = this.getFocusElement('a');
        if (a && a.tagName && a.tagName.toLowerCase() == 'a') {
            var expr = new RegExp(this.component_id+'-(l|d)([0-9]+)');
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
        var ret = '<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;}</style>';
        if (this.cssFile) {
            ret += '<link rel="stylesheet" type="text/css" href="'+this.cssFile+'" />';
        }
        ret += '</head><body class="content"></body></html>';
        return ret;
    },
    setValue : function(v) {
        if (v && v.component_id) {
            this.component_id = v.component_id;
        }
        if (typeof v.content != 'undefined') v = v.content;
        Vps.Form.HtmlEditor.superclass.setValue.call(this, v);
    },

    createImage: function() {
        var img = this.getFocusElement('img');
        if (img && img.tagName && img.tagName.toLowerCase() == 'img') {
            var expr = new RegExp('/media/[^/]+/'+this.component_id+'-i([0-9]+)/');
            var m = img.src.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.imageDialog.showEdit({
                    component_id: this.component_id+'-i'+nr
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
            params: {component_id: this.component_id},
            url: this.controllerUrl+'/jsonAddImage',
            success: function(response, options, r) {
                this.imageDialog.showEdit({
                    component_id: r.component_id
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
            var expr = new RegExp(this.component_id+'-l([0-9]+)');
            var m = a.href.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.linkDialog.un('datachange', this._insertLink, this);
                this.linkDialog.showEdit({
                    component_id: this.component_id+'-l'+nr
                });
                return;
            }
        }
        Ext.Ajax.request({
            params: {component_id: this.component_id},
            url: this.controllerUrl+'/jsonAddLink',
            success: function(response, options, r) {
                this.linkDialog.un('datachange', this._insertLink, this);
                this.linkDialog.showEdit({
                    component_id: r.component_id
                });
                this.linkDialog.on('datachange', this._insertLink, this, { single: true });
            },
            scope: this
        });
    },

    _insertLink : function() {
        var params = this.linkDialog.getAutoForm().getBaseParams();
        this.relayCmd('createlink', params.component_id);
    },

    createDownload: function() {
        var a = this.getFocusElement('a');
        if (a && a.tagName && a.tagName.toLowerCase() == 'a') {
            var expr = new RegExp(this.component_id+'-d([0-9]+)');
            var m = a.href.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.downloadDialog.un('datachange', this._insertDownloadLink, this);
                this.downloadDialog.showEdit({
                    component_id: this.component_id+'-d'+nr
                });
                return;
            }
        }
        Ext.Ajax.request({
            params: {component_id: this.component_id},
            url: this.controllerUrl+'/jsonAddDownload',
            success: function(response, options, r) {
                this.downloadDialog.un('datachange', this._insertDownloadLink, this);
                this.downloadDialog.showEdit({
                    component_id: r.component_id
                });
                this.downloadDialog.on('datachange', this._insertDownloadLink, this, { single: true });
            },
            scope: this
        });
    },

    _insertDownloadLink : function() {
        var params = this.downloadDialog.getAutoForm().getBaseParams();
        this.relayCmd('createlink', params.component_id);
    },

    insertChar: function()
    {
        var win = Vps.Form.HtmlEditor.insertCharWindow; //statische var, nur ein window erstellen
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
            this.insertAtCursor(ch);
            win.hide();
        }, this);
        win.show();
    },

    insertPlainText: function()
    {
        Ext.Msg.show({
            title : trlVps('Insert Plain Text'),
            msg : '',
            buttons: Ext.Msg.OKCANCEL,
            fn: function(btn, text) {
                if (btn == 'ok') {
                    text = text.replace(/\r/g, '');
                    text = text.replace(/\n/g, '</p>\n<p>');
                    text = String.format('<p>{0}</p>', text);
                    this.insertAtCursor(text);
                }
            },
            scope : this,
            minWidth: 500,
            prompt: true,
            multiline: 300
        });
    },

    tidyHtml: function()
    {
        if (!this.enableTidy) return;

        Ext.getBody().mask(trlVps('Cleaning...'));
        Ext.Ajax.request({
            url: this.controllerUrl+'/jsonTidyHtml',
            params: {
                component_id: this.component_id,
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
    },

    undo: function() {
        this.relayCmd('undo');
    },
    redo: function() {
        this.relayCmd('redo');
    }
});
Ext.reg('htmleditor', Vps.Form.HtmlEditor);
