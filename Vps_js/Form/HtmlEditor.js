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
    enableInsertChar: true,
    enablePastePlain: true,
    stylesIdPattern: null,
    enableStyles: false,

    initComponent : function()
    {
        if (!this.actions) this.actions = {};

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

        if (this.stylesEditorConfig && this.enableStyles) {
            this.stylesEditorDialog = Ext.ComponentMgr.create(this.stylesEditorConfig);
            this.stylesEditorDialog.on('hide', this._reloadStyles, this);
        }

        this.plugins = [];
        if (this.enableUndoRedo) {
            this.plugins.push(new Vps.Form.HtmlEditor.UndoRedo());
        }
        if (this.enableInsertChar) {
            this.plugins.push(new Vps.Form.HtmlEditor.InsertChar());
        }
        if (this.enablePastePlain) {
            this.plugins.push(new Vps.Form.HtmlEditor.PastePlain());
        }
        if (this.linkComponentConfig) {
            this.plugins.push(new Vps.Form.HtmlEditor.InsertLink({
                componentConfig: this.linkComponentConfig
            }));
        }
        if (this.imageComponentConfig) {
            this.plugins.push(new Vps.Form.HtmlEditor.InsertImage({
                componentConfig: this.imageComponentConfig
            }));
        }
        if (this.downloadComponentConfig) {
            this.plugins.push(new Vps.Form.HtmlEditor.InsertDownload({
                componentConfig: this.downloadComponentConfig
            }));
        }
        if (this.controllerUrl && this.enableTidy) {
            this.plugins.push(new Vps.Form.HtmlEditor.Tidy());
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
        var num = 0;
        for(var i in this.inlineStyles) {
            var selector = i.split('.');
            var tag = selector[0];
            var className = selector[1];
            this.formatter.register('inline'+num, {
                inline: tag,
                classes: className
            });
            ++num;
        }

        num = 0;
        for(var i in this.blockStyles) {
            var selector = i.split('.');
            var tag = selector[0];
            var className = selector[1];
            this.formatter.register('block'+num, {
                block: tag,
                classes: className
            });
            ++num;
        }
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
    },
    createToolbar: function(editor)
    {
        Vps.Form.HtmlEditor.superclass.createToolbar.call(this, editor);
        var tb = this.getToolbar();
        
        if (this.tb.items.map.underline) {
            this.tb.items.map.underline.hide();
        }

        this.fireEvent('afterCreateToolbar', tb);

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
            this.inlineStylesSelect.dom.value = 'p';
            var num = 0;
            for(var i in this.blockStyles) {
                if (this.formatter.match('block'+num)) {
                    this.blockStylesSelect.dom.value = i;
                }
                num++;
            }
        }
        if (this.inlineStylesSelect) {
            this.inlineStylesSelect.dom.value = 'span';
            var num = 0;
            for(var i in this.inlineStyles) {
                if (this.formatter.match('inline'+num)) {
                    this.inlineStylesSelect.dom.value = i;
                }
                num++;
            }
        }
        this.fireEvent('updateToolbar');
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

    mask: function(txt) {
        this.el.up('div').mask(txt);
    },
    unmask: function() {
        this.el.up('div').unmask();
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
    },

    _onSelectBlockStyle: function() {
        var v = this.blockStylesSelect.dom.value;
        var num = 0;
        for(var i in this.blockStyles) {
            this.formatter.remove('block'+num);
            ++num;
        }

        num = 0;
        for(var i in this.blockStyles) {
            if (i == v) {
                this.formatter.apply('block'+num);
                break;
            }
            ++num;
        }
        this.deferFocus();
        this.updateToolbar();
    },
    _onSelectInlineStyle: function() {
        var v = this.inlineStylesSelect.dom.value;
        var num = 0;
        for(var i in this.inlineStyles) {
            this.formatter.remove('inline'+num);
            ++num;
        }

        num = 0;
        for(var i in this.inlineStyles) {
            if (i == v) {
                this.formatter.apply('inline'+num);
                break;
            }
            ++num;
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
                //TODO re-register styles to formatter
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
