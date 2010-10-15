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
    stylesIdPattern: null,
    enableStyles: false,

    initComponent : function()
    {
        if (!this.actions) this.actions = {};

        this.actions.insertImage = new Ext.Action({
            icon: '/assets/silkicons/picture.png',
            handler: this.createImage,
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: trlVps('Image'),
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
            handler: function() {
                this.relayCmd('undo');
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
        this.actions.redo = new Ext.Action({
            handler: function() {
                this.relayCmd('redo');
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
        this.actions.editStyles = new Ext.Action({
            icon: '/assets/silkicons/style_edit.png',
            handler: function() {
                this.stylesEditorDialog.show();
            },
            scope: this,
            tooltip: {
                cls: 'x-html-editor-tip',
                title: trlVps('Edit Styles'),
                text: trlVps('Modify and Create Styles.')
            },
            cls: 'x-btn-icon',
            clickEvent: 'mousedown',
            tabIndex: -1
        });
        this.actions.insertChar = new Ext.Action({
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
        this.actions.insertPlainText = new Ext.Action({
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
        if (this.stylesEditorConfig && this.enableStyles) {
            this.stylesEditorDialog = Ext.ComponentMgr.create(this.stylesEditorConfig);
            this.stylesEditorDialog.on('hide', this._reloadStyles, this);
        }

        Vps.Form.HtmlEditor.superclass.initComponent.call(this);
    },
    getAction : function(type)
    {
        return this.actions[type];
    },
    initEditor : function() {
        Vps.Form.HtmlEditor.superclass.initEditor.call(this);
        if (this.cssFiles) {
            this.cssFiles.forEach(function(f) {
                var s = this.doc.createElement('link');
                s.setAttribute('type', 'text/css');
                s.setAttribute('href', f);
                s.setAttribute('rel', 'stylesheet');
                this.doc.getElementsByTagName("head")[0].appendChild(s);
            }, this);
        }

        if (this.controllerUrl && this.enableTidy) {
            Ext.EventManager.on(this.doc, 'keydown', function(e) {
                if(e.ctrlKey){
                    var c = e.getCharCode();
                    if(c > 0){
                        c = String.fromCharCode(c).toLowerCase();
                        if (c == 'v') {
                            if (!this.pasteDelayTask) {
                                var pasteClean = function() {
                                    this.syncValue();

                                    var bookmark = this.tinymceEditor.selection.getBookmark();
                                    this.tidyHtml({
                                        params: { allowCursorSpan: true },
                                        callback: function() {
                                            this.tinymceEditor.selection.moveToBookmark(bookmark);
                                            this.syncValue();
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
        }

        var dom = new tinymce.dom.DOMUtils(this.doc, {
            /*
            keep_values : true,
            url_converter : t.convertURL,
            url_converter_scope : t,
            hex_colors : s.force_hex_style_colors,
            class_filter : s.class_filter,
            update_styles : 1,
            fix_ie_paragraphs : 1,
            valid_styles : s.valid_styles
            */
        });
        this.tinymceEditor = {
            settings: {
                forced_root_block: 'p'
            },
            dom: dom,
            selection: new tinymce.dom.Selection(dom, this.win, null/*t.serializer*/),
            schema: new tinymce.dom.Schema(),
            nodeChanged: function(o) {
                //TODO
            }
        };
        this.formatter = new tinymce.Formatter(this.tinymceEditor);
    },
    onFirstFocus : function(){
        Vps.Form.HtmlEditor.superclass.onFirstFocus.apply(this, arguments);
        //TODO nicht nur onFirstFocus
        tinyMCE.activeEditor = this.tinymceEditor;
    },
    // private
    // überschrieben wegen spezieller ENTER behandlung im IE die wir nicht wollen
    fixKeys : function(){ // load time branching for fastest keydown performance
        if(Ext.isIE){
            return function(e){
                var k = e.getKey(), r;
                if(k == e.TAB){
                    e.stopEvent();
                    r = this.doc.selection.createRange();
                    if(r){
                        r.collapse(true);
                        r.pasteHTML('&nbsp;&nbsp;&nbsp;&nbsp;');
                        this.deferFocus();
                    }
                //}else if(k == e.ENTER){
                    //entfernt, wir wollen dieses verhalten genau so wie der IE es macht
                }
            };
        }else if(Ext.isOpera){
            return function(e){
                var k = e.getKey();
                if(k == e.TAB){
                    e.stopEvent();
                    this.win.focus();
                    this.execCmd('InsertHTML','&nbsp;&nbsp;&nbsp;&nbsp;');
                    this.deferFocus();
                }
            };
        }else if(Ext.isWebKit){
            return function(e){
                var k = e.getKey();
                if(k == e.TAB){
                    e.stopEvent();
                    this.execCmd('InsertText','\t');
                    this.deferFocus();
                }
             };
        }
    }(),

    onRender: function(ct, position)
    {
        Vps.Form.HtmlEditor.superclass.onRender.call(this, ct, position);

        //re-enable items that are possible for not-yet-active editor
        if (this.stylesEditorToolbarItem) this.stylesEditorToolbarItem.enable();
        if (this.tidyToolbarItem) this.tidyToolbarItem.enable();
    },
    createToolbar: function(editor)
    {
        Vps.Form.HtmlEditor.superclass.createToolbar.call(this, editor);
        var tb = this.getToolbar();
        
        if (this.tb.items.map.underline) {
            this.tb.items.map.underline.hide();
        }

        if (this.downloadDialog) {
            tb.insert(6,  this.getAction('insertDownload'));
        }
        if (this.linkDialog) {
            tb.insert(6,  this.getAction('insertLink'));
        }
        if (this.imageDialog) {
            tb.insert(6, this.getAction('insertImage'));
        }
        if (this.linkDialog || this.imageDialog || this.downloadDialog) {
            tb.insert(6, '-');
        }

        if (this.enableInsertChar) {
            tb.insert(tb.items.getCount()-1, this.getAction('insertChar'));
        }
        if (this.enablePastePlain) {
            tb.insert(tb.items.getCount()-1, this.getAction('insertPlainText'));
        }

        if (this.enableInsertChar || this.enablePastePlain) {
            tb.insert(tb.items.getCount()-1, '-');
        }

        if (this.enableUndoRedo) {
            var offs = 0;
            if (this.enableFont) offs += 2;
            tb.insert(offs, this.getAction('undo'));
            tb.insert(offs+1, this.getAction('redo'));
            tb.insert(offs+2, '-');
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
        if (this.enableStyles) {
            var table = document.createElement('table');
            table.cellspacing = '0';
            tb.stylesTr = table.appendChild(document.createElement('tbody'))
                        .appendChild(document.createElement('tr'));
            tb.stylesTr.appendChild(document.createElement('td'));
            tb.el.appendChild(table);
            tb.tr = tb.stylesTr;
            tb.originalTr = tb.tr;
            if (this.stylesEditorDialog) {
                this.stylesEditorToolbarItem = tb.insert(0, this.getAction('editStyles'));
            }
            this._renderInlineStylesSelect();
            this._renderBlockStylesSelect();
            tb.tr = tb.originalTr;
        }

        // Jumpmark: #JM1
        // nach einfügen mit Strg+V in Firefox ist der knochen nicht sichtbar
        // dieses element wird nur dazu missbraucht, nach dem einfügen mit
        // Strg+V den focus aus dem editor zu nehmen um ihn dann wieder
        // reinplatzieren zu können
        tb.el.createChild({
            tag: 'a', cls: 'blurNode', href: '#', style: 'position: absolute; left: -5000px;' 
        });
    },

    createInlineStylesOptions : function(){
        var buf = [];
        for (var i in this.inlineStyles) {
            buf.push(
                '<option value="',i,'"',
                    (i == 'span' ? ' selected="true">' : '>'),
                    this.inlineStyles[i],
                '</option>'
            );
        }
        return buf.join('');
    },

    createBlockStylesOptions : function(){
        var buf = [];
        for (var i in this.blockStyles) {
            buf.push(
                '<option value="',i,'"',
                    (i == 'p' ? ' selected="true">' : '>'),
                    this.blockStyles[i],
                '</option>'
            );
        }
        return buf.join('');
    },

    createBlockOptions : function(){
        var buf = [];
        for (var i in this.formatBlocks) {
            buf.push(
                '<option value="',i,'"',
                    (i == 'p' ? ' selected="true">' : '>'),
                    this.formatBlocks[i],
                '</option>'
            );
        }
        return buf.join('');
    },
    updateToolbar: function(){
        Vps.Form.HtmlEditor.superclass.updateToolbar.call(this);
        if (this.blockSelect) {
            if (Ext.isIE) {
                var selectedBlock = false;
                var el = this.getFocusElement();
                while (el) {
                    for(var i in this.formatBlocks) {
                        if (el.tagName && i == el.tagName.toLowerCase()) {
                            if(i != this.blockSelect.dom.value){
                                this.blockSelect.dom.value = i;
                            }
                            selectedBlock = true;
                            break;
                        }
                    }
                    if (selectedBlock) break;
                    el = el.parentNode;
                }
                if (!selectedBlock) {
                    if('p' != this.blockSelect.dom.value){
                        this.blockSelect.dom.value = 'p';
                    }
                }
            } else {
                var name = (this.doc.queryCommandValue('FormatBlock')||'p').toLowerCase();
                if(name != this.blockSelect.dom.value){
                    this.blockSelect.dom.value = name;
                }
            }
        }
        if (this.blockStylesSelect) {
            var el = this.getFocusElement('block');
            var selectedStyle = false;
            if (el) {
                //zuerst alle mit einem className
                for(var i in this.blockStyles) {
                    var selector = i.split('.');
                    var tag = selector[0];
                    var className = selector[1];
                    if (className
                        && (!tag || el.tagName.toLowerCase() == tag)
                        && el.className == className) {
                        if(i != this.blockStylesSelect.dom.value){
                            this.blockStylesSelect.dom.value = i;
                        }
                        selectedStyle = true;
                        break;
                    }
                }

                //falls nichts passend alle nochmal
                if (!selectedStyle) {
                    for(var i in this.blockStyles) {
                        var selector = i.split('.');
                        var tag = selector[0];
                        var className = selector[1];
                        if ((!tag || el.tagName.toLowerCase() == tag)
                            && (!className || el.className == className)) {
                            if(i != this.blockStylesSelect.dom.value){
                                this.blockStylesSelect.dom.value = i;
                            }
                            selectedStyle = true;
                            break;
                        }
                    }
                }
            }
            if (!selectedStyle) {
                if ('p' != this.blockStylesSelect.dom.value) {
                    this.blockStylesSelect.dom.value = 'p';
                }
            }
        }
        if (this.inlineStylesSelect) {
            var el = this.getFocusElement('span');
            var selectedStyle = false;
            if (el) {
                for(var i in this.inlineStyles) {
                    var selector = i.split('.');
                    var tag = selector[0];
                    var className = selector[1];
                    if (i != 'span' && (!tag || el.tagName.toLowerCase() == tag)
                        && (!className || el.className == className)) {
                        if(i != this.inlineStylesSelect.dom.value){
                            this.inlineStylesSelect.dom.value = i;
                        }
                        selectedStyle = true;
                        break;
                    }
                }
            }
            if (!selectedStyle) {
                if ('span' != this.inlineStylesSelect.dom.value) {
                    this.inlineStylesSelect.dom.value = 'span';
                }
            }
        }
        var a = this.getFocusElement('a');
        if (a && a.tagName && a.tagName.toLowerCase() == 'a') {
            var expr = new RegExp(this.componentId+'-(l|d)([0-9]+)');
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
                var selection = this.win.getSelection();
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
        var ret = '<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;}</style>\n';
        ret += '</head><body class="webStandard vpcText"></body></html>';
        return ret;
    },
    setValue : function(v) {
        if (v && v.componentId) {
            this.componentId = v.componentId;
            if (this.stylesIdPattern) {
                var m = this.componentId.match(this.stylesIdPattern);
                m = m ? m[0] : null;
                if (this.ownStylesParam != m) {
                    this.ownStylesParam = m;
                    this._reloadStyles();
                }
            }
        }
        if (this.stylesEditorDialog) {
            if (this.ownStylesParam) {
                this.stylesEditorDialog.master.hide();
            } else {
                this.stylesEditorDialog.master.show();
            }
            this.stylesEditorDialog.applyBaseParams({
                componentId: this.componentId,
                componentClass: this.componentClass
            });
        }
        if (v && (typeof v.content) != 'undefined') v = v.content;
        Vps.Form.HtmlEditor.superclass.setValue.call(this, v);
    },

    createImage: function() {
        var img = this.getFocusElement('img');
        if (img && img.tagName && img.tagName.toLowerCase() == 'img') {
            this._currentImage = img;
            var expr = new RegExp('/media/[^/]+/'+this.componentId+'-i([0-9]+)/');
            var m = img.src.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.linkDialog.un('datachange', this._insertImage, this);
                this.linkDialog.un('datachange', this._modifyImage, this);
                this.imageDialog.showEdit({
                    componentId: this.componentId+'-i'+nr
                });
                this.imageDialog.on('datachange', this._modifyImage, this);
                return;
            }
        }
        Ext.Ajax.request({
            params: {componentId: this.componentId},
            url: this.controllerUrl+'/json-add-image',
            success: function(response, options, r) {
                this.linkDialog.un('datachange', this._insertImage, this);
                this.linkDialog.un('datachange', this._modifyImage, this);
                this.imageDialog.showEdit({
                    componentId: r.componentId
                });
                this.imageDialog.on('datachange', this._insertImage, this);
            },
            scope: this
        });
    },
    _insertImage: function(r) {
        var html = '<img src="'+r.imageUrl+'?'+Math.random()+'" ';
        html += 'width="'+r.imageDimension.width+'" ';
        html += 'height="'+r.imageDimension.height+'" />';
        this.insertAtCursor(html);
    },
    _modifyImage: function(r) {
        this._currentImage.src = r.imageUrl+'?'+Math.random();
        this._currentImage.width = r.imageDimension.width;
        this._currentImage.height = r.imageDimension.height;
    },
    createLink: function() {
        if (!this.linkDialog) {
            return Vps.Form.HtmlEditor.superclass.createLink.call(this);
        }
        var a = this.getFocusElement('a');
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
            url: this.controllerUrl+'/json-add-link',
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
        this.relayCmd('createlink', params.componentId);
    },

    createDownload: function() {
        var a = this.getFocusElement('a');
        if (a && a.tagName && a.tagName.toLowerCase() == 'a') {
            var expr = new RegExp(this.componentId+'-d([0-9]+)');
            var m = a.href.match(expr);
            if (m) {
                var nr = parseInt(m[1]);
            }
            if (nr) {
                this.downloadDialog.un('datachange', this._insertDownloadLink, this);
                this.downloadDialog.showEdit({
                    componentId: this.componentId+'-d'+nr
                });
                return;
            }
        }
        Ext.Ajax.request({
            params: {componentId: this.componentId},
            url: this.controllerUrl+'/json-add-download',
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

    mask: function(txt) {
        this.el.up('div').mask(txt);
    },
    unmask: function() {
        this.el.up('div').unmask();
    },

    tidyHtml: function(tidyOptions)
    {
        if (!this.enableTidy) return;

        this.mask(trlVps('Cleaning...'));

        var params = {
            componentId: this.componentId,
            html: this.getValue()
        };
        if (tidyOptions && tidyOptions.params) {
            Ext.applyIf(params, tidyOptions.params);
        }
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-tidy-html',
            params: params,
            failure: function() {
                this.unmask();
            },
            success: function(response, options, r) {
                this.unmask();

                // Um den Knochen in Firefox sichtbar zu halten.
                // Weiteres zum blurNode: Suche nach #JM1 in dieser Datei.
                this.el.up('div').child('.blurNode', true).focus();
                this.deferFocus();

                if (this.getValue() != r.html) {
                    this.setValue(r.html);
                }

                if (tidyOptions && tidyOptions.callback) {
                    tidyOptions.callback.call(tidyOptions.scope || this);
                }
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
            if (tag == 'block') tag = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                                       'pre', 'code', 'address'];
            var isNeededTag = function(t) {
                if (tag.indexOf) {
                    return tag.indexOf(t) != -1;
                } else {
                    return tag == t;
                }
            };
            while (elm && elm.parentNode &&
                    (!elm.tagName || !isNeededTag(elm.tagName.toLowerCase()))) {
                elm = elm.parentNode;
            }
            if (!elm || !elm.tagName || !isNeededTag(elm.tagName.toLowerCase())) return null;
        }
        return elm;
    },

    //protected
    toggleSourceEdit : function(sourceEditMode) {
        Vps.Form.HtmlEditor.superclass.toggleSourceEdit.call(this, sourceEditMode);

        //re-enable items that are possible in sourceedit
        if (this.stylesEditorToolbarItem) this.stylesEditorToolbarItem.enable();
        if (this.tidyToolbarItem) this.tidyToolbarItem.enable();

        this.tidyHtml();
    },

    //returns null if there is no selection
    getSelectionRange: function()
    {
        var sel;
        if (Ext.isIE) {
            sel = this.doc.selection;
        } else {
            sel = this.win.getSelection();
        }
        if (!sel) return null;
        if (sel.isCollapsed) return null;
        if (Ext.isIE && sel.createRange().htmlText === '') return null;
        var range;
        if (Ext.isIE) {
            range = sel.createRange();
        } else {
            range = sel.getRangeAt(0);
        }
        if (!range) return null;

        return range;
    },

    _onSelectBlockStyle: function() {
        var v = this.blockStylesSelect.dom.value;
        var tag = '';
        var className = '';
        if (v.indexOf('.') == -1) {
            tag = v;
        } else {
            var i = v.split('.');
            tag = i[0];
            className = i[1];
        }
        if (tag) {
            v = tag;
            if (Ext.isIE) {
                v = '<'+v+'>';
            }
            this.relayCmd('formatblock', v);
        }
        this.deferFocus();
        (function() {
            var elm = this.getFocusElement(tag || 'block');
            if (elm) {
                elm.className = className;
            }
            this.deferFocus();
            this.updateToolbar();
        }).defer(11, this);
    },
    _onSelectInlineStyle: function() {
        var v = this.inlineStylesSelect.dom.value;
        var tag = '';
        var className = '';
        if (v.indexOf('.') == -1) {
            tag = v;
        } else {
            var i = v.split('.');
            tag = i[0];
            className = i[1];
        }
        var elm = this.getFocusElement(tag);
        if (elm && elm.tagName && elm.tagName.toLowerCase() == tag) {
            elm.className = className;
        } else {
            var range = this.getSelectionRange();
            if (range) {
                if (range.surroundContents) {
                    var span = this.doc.createElement(tag);
                    span.className = className;
                    range.surroundContents(span);
                } else {
                    //IE
                    range.pasteHTML('<'+tag+' class="'+className+'">'+range.htmlText+'</'+tag+'>');
                }
            } else {
                //auskommentiert weils nicht korrekt funktioniert; einfach gar nichts tun wenn nix markiert
                //this.insertAtCursor('<'+tag+' class="'+className+'">&nbsp;</'+tag+'>');
                //this.win.getSelection().getRangeAt(0).selectNode(this.win.getSelection().focusNode);
            }
        }
        this.deferFocus();
        this.updateToolbar();
    },
    _reloadStyles: function() {
        var reloadCss = function(doc) {
            var href = doc.location.protocol+'/' + '/'+
                        doc.location.hostname + this.stylesCssFile;
            href = href.split('?')[0];
            var links = doc.getElementsByTagName("link");
            for (var i = 0; i < links.length; i++) {
                var l = links[i];
                if (l.type == 'text/css' && l.href
                    && l.href.split('?')[0] == href) {
                    l.parentNode.removeChild(l);
                }
            }
            var s = doc.createElement('link');
            s.setAttribute('type', 'text/css');
            s.setAttribute('href', href+'?'+Math.random());
            s.setAttribute('rel', 'stylesheet');
            doc.getElementsByTagName("head")[0].appendChild(s);
        };
        reloadCss.call(this, document);
        if (this.doc) reloadCss.call(this, this.doc);
        Ext.Ajax.request({
            params: {
                componentId: this.componentId
            },
            url: this.controllerUrl+'/json-styles',
            success: function(response, options, result) {
                this.inlineStyles = result.inlineStyles;
                this.blockStyles = result.blockStyles;
                this._renderInlineStylesSelect();
                this._renderBlockStylesSelect();
                if (this.activated) this.updateToolbar();
            },
            scope: this
        });
    },

    _renderInlineStylesSelect: function() {
        var stylesLength = 0;
        for (var i in this.inlineStyles) stylesLength++;
        if (this.inlineStyles && stylesLength > 1) {
            if (!this.inlineStylesSelect) {
                this.inlineStylesSelect = this.getToolbar().el.createChild({
                    tag:'select',
                    cls:'x-font-select',
                    html: this.createInlineStylesOptions()
                });
                this.inlineStylesSelect.on('change', this._onSelectInlineStyle, this);
                var offs = 0;
                if (this.blockStylesToolbarItem && !this.blockStylesToolbarItem.hidden) {
                    offs = 3;
                }
                var tb = this.getToolbar();
                tb.tr = tb.stylesTr;
                this.inlineStylesToolbarText = tb.insert(offs, trlVps('Inline')+':');
                this.inlineStylesToolbarItem = tb.insert(offs+1, this.inlineStylesSelect.dom);
                this.inlineStylesSeparator = tb.insert(offs+2, '-');
                tb.tr = tb.originalTr;
            } else {
                this.inlineStylesToolbarText.show();
                this.inlineStylesToolbarItem.show();
                this.inlineStylesSeparator.show();
                this.inlineStylesSelect.update(this.createInlineStylesOptions());
            }
        } else {
            if (this.inlineStylesSelect) {
                this.inlineStylesToolbarText.hide();
                this.inlineStylesToolbarItem.hide();
                this.inlineStylesSeparator.hide();
            }
        }
    },
    _renderBlockStylesSelect: function() {
        var stylesLength = 0;
        for (var i in this.blockStyles) stylesLength++;
        if (this.blockStyles && stylesLength > 1) {
            if (!this.blockStylesSelect) {
                this.blockStylesSelect = this.getToolbar().el.createChild({
                    tag:'select',
                    cls:'x-font-select',
                    html: this.createBlockStylesOptions()
                });
                this.blockStylesSelect.on('change', this._onSelectBlockStyle, this);
                var tb = this.getToolbar();
                tb.tr = tb.stylesTr;
                this.blockStylesToolbarText = tb.insert(0, trlVps('Block')+':');
                this.blockStylesToolbarItem = tb.insert(1, this.blockStylesSelect.dom);
                this.blockStylesSeparator = tb.insert(2, '-');
                tb.tr = tb.originalTr;
            } else {
                this.blockStylesToolbarText.show();
                this.blockStylesToolbarItem.show();
                this.blockStylesSeparator.show();
                this.blockStylesSelect.update(this.createBlockStylesOptions());
            }
        } else {
            if (this.blockStylesSelect) {
                this.blockStylesToolbarText.hide();
                this.blockStylesToolbarItem.hide();
                this.blockStylesSeparator.hide();
            }
        }
    },

    //syncValue schreibt den inhalt vom iframe in die textarea
    //das darf aber nur gemacht werden wenn wir nicht in der html-code ansicht sind!
    //behebt also einen bug von ext
    syncValue : function(){
        if (!this.sourceEditMode) {
            Vps.Form.HtmlEditor.superclass.syncValue.call(this);
        }
    },

    /**
     * Protected method that will not generally be called directly. Pushes the value of the textarea
     * into the iframe editor.
     */
    pushValue : function(){
        if(this.initialized){
            var v = this.el.dom.value;
            if(!this.activated && v.length < 1){
                v = this.defaultValue;
            }
            if(this.fireEvent('beforepush', this, v) !== false){
                //BEGIN ÄNDERUNG
                if(Ext.isGecko){
                    //FF kann scheinbar nicht mit strong und em umgehen, mit b und i aber schon
                    v = v.replace('<strong>', '<b>').replace('</strong>', '</b>');
                    v = v.replace('<em>', '<i>').replace('</em>', '</i>');
                }
                //END ÄNDERUNG
                this.getEditorBody().innerHTML = v;
                if(Ext.isGecko){
                    // Gecko hack, see: https://bugzilla.mozilla.org/show_bug.cgi?id=232791#c8
                    var d = this.doc,
                        mode = d.designMode.toLowerCase();

                    d.designMode = mode.toggle('on', 'off');
                    d.designMode = mode;
                }
                this.fireEvent('push', this, v);
            }
        }
    }
});
Ext.reg('htmleditor', Vps.Form.HtmlEditor);
