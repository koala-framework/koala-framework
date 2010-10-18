Vps.Form.HtmlEditor = Ext.extend(Ext.form.HtmlEditor, {
    enableUndoRedo: true,
    enableInsertChar: true,
    enablePastePlain: true,
    enableStyles: false,

    initComponent : function()
    {
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
        if (this.enableStyles) {
            this.plugins.push(new Vps.Form.HtmlEditor.Styles({
                inlineStyles: this.inlineStyles,
                blockStyles: this.blockStyles,
                stylesEditorConfig: this.stylesEditorConfig,
                stylesIdPattern: this.stylesIdPattern
            }));
        }

        Vps.Form.HtmlEditor.superclass.initComponent.call(this);
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
    },

    getDocMarkup : function(){
        var ret = '<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;}</style>\n';
        ret += '</head><body class="webStandard vpcText"></body></html>';
        return ret;
    },
    setValue : function(v) {
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
