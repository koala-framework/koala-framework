Vps.Form.HtmlEditor = Ext.extend(Ext.form.HtmlEditor, {
    enableUndoRedo: true,
    enableInsertChar: true,
    enablePastePlain: true,
    enableStyles: false,

    initComponent : function()
    {
        this.plugins = [];
        if (this.enableFormat) {
            this.plugins.push(new Vps.Form.HtmlEditor.Formats());
            this.enableFormat = false; //ext implementation deaktivieren, unsere ist besser
        }
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
        if (this.downloadComponentConfig) {
            this.plugins.push(new Vps.Form.HtmlEditor.InsertDownload({
                componentConfig: this.downloadComponentConfig
            }));
        }
        if (this.linkComponentConfig || this.downloadComponentConfig) {
            this.plugins.push(new Vps.Form.HtmlEditor.RemoveLink());
        }
        if (this.imageComponentConfig) {
            this.plugins.push(new Vps.Form.HtmlEditor.InsertImage({
                componentConfig: this.imageComponentConfig
            }));
        }
        if (this.controllerUrl && this.enableTidy) {
            this.plugins.push(new Vps.Form.HtmlEditor.Tidy());
        }
        if (this.enableStyles) {
            this.plugins.push(new Vps.Form.HtmlEditor.Styles({
                styles: this.styles,
                stylesEditorConfig: this.stylesEditorConfig,
                stylesIdPattern: this.stylesIdPattern
            }));
        }
        this.plugins.push(new Vps.Form.HtmlEditor.BreadCrumbs());

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

        //wann text mit maus markiert wird muss die toolbar upgedated werden (link einfügen enabled)
        //dazu auch auf mouseup schauen
        Ext.EventManager.on(this.doc, {
            'mouseup': this.onEditorEvent,
            buffer:100,
            scope: this
        });

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
                //TODO this.onEditorEvent ?
            },
            extEditor: this,
            getDoc: function() {
                return this.extEditor.getDoc();
            },
            getBody: function() {
                return this.extEditor.getEditorBody();
            }

        };
        var lo = {
            mouseup : 'onMouseUp',
            mousedown : 'onMouseDown',
            click : 'onClick',
            keyup : 'onKeyUp',
            keydown : 'onKeyDown',
            keypress : 'onKeyPress',
            submit : 'onSubmit',
            dblclick : 'onDblClick'
        };
        var t = this.tinymceEditor;
        function eventHandler(e, o) {
            // Generic event handler
            //if (t.onEvent.dispatch(t, e, o) !== false) {
                // Specific event handler
                t[lo[e.fakeType || e.type]].dispatch(t, e, o);
            //}
        };

        for(var k in lo) {
            t[lo[k]] = new tinymce.util.Dispatcher(t);
            dom.bind(t.getDoc(), k, eventHandler);
        }
        this.formatter = new tinymce.Formatter(this.tinymceEditor);

        Ext.fly(this.getWin()).on('focus', function() {
            //unschön, aber tinyMCE braucht das
            tinyMCE.activeEditor = this.tinymceEditor;
        }, this);
        
        this.originalValue = this.getEditorBody().innerHTML; // wegen isDirty, es wird der html vom browser dom mit dem originalValue verglichen, wo dann zB aus <br /> ein <br> wird
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

    getDocMarkup : function(){
        var ret = '<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;}</style>\n';
        ret += '</head><body class="webStandard vpcText"></body></html>';
        return ret;
    },
    setValue : function(v) {
        if (v && v.componentId) {
            this.componentId = v.componentId;
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
        if (tag == 'block') tag = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                                    'pre', 'code', 'address'];
        var isNeededTag = function(t) {
            t = t.tagName.toLowerCase();
            if (tag.indexOf) {
                return tag.indexOf(t) != -1;
            } else {
                return tag == t;
            }
        };
        var ret = null;
        this.getParents().each(function(el) {
            if (isNeededTag(el)) {
                ret = el;
                return false;
            }
        }, this);
        return ret;
    },

    //basiert auf Editor::nodeChanged
    getParents: function() {
        var s = this.tinymceEditor.selection;
        var n = (Ext.isIE ? s.getNode() : s.getStart()) || this.tinymceEditor.getBody();
        n = Ext.isIE && n.ownerDocument != this.tinymceEditor.getDoc() ? this.tinymceEditor.getBody() : n; // Fix for IE initial state
        var parents = [];
        this.tinymceEditor.dom.getParent(n, function(node) {
            if (node.nodeName == 'BODY')
                return true;

            parents.push(node);
        });
        return parents;
    },

    //syncValue schreibt den inhalt vom iframe in die textarea
    //das darf aber nur gemacht werden wenn wir nicht in der html-code ansicht sind!
    //behebt also einen bug von ext
    syncValue : function(){
        if (!this.sourceEditMode) {
            Vps.Form.HtmlEditor.superclass.syncValue.call(this);
        }
    }
});
Ext.reg('htmleditor', Vps.Form.HtmlEditor);
