Kwf.Form.HtmlEditor = Ext2.extend(Ext2.form.HtmlEditor, {
    enableUndoRedo: true,
    enableInsertChar: true,
    enablePastePlain: true,
    enableStyles: false,

    initComponent : function()
    {
        if (!this.plugins) this.plugins = [];
        if (this.enableFormat) {
            this.plugins.push(new Kwf.Form.HtmlEditor.Formats());
            this.enableFormat = false; //ext implementation deaktivieren, unsere ist besser
        }
        if (this.enableUndoRedo) {
            this.plugins.push(new Kwf.Form.HtmlEditor.UndoRedo());
        }
        if (this.enableInsertChar) {
            this.plugins.push(new Kwf.Form.HtmlEditor.InsertChar());
        }
        if (this.enablePastePlain) {
            this.plugins.push(new Kwf.Form.HtmlEditor.PastePlain());
        }
        this.plugins.push(new Kwf.Form.HtmlEditor.Indent());
        if (this.linkComponentConfig) {
            this.plugins.push(new Kwf.Form.HtmlEditor.InsertLink({
                componentConfig: this.linkComponentConfig
            }));
        }
        if (this.downloadComponentConfig) {
            this.plugins.push(new Kwf.Form.HtmlEditor.InsertDownload({
                componentConfig: this.downloadComponentConfig
            }));
        }
        if (this.linkComponentConfig || this.downloadComponentConfig) {
            this.plugins.push(new Kwf.Form.HtmlEditor.RemoveLink());
        }
        if (this.imageComponentConfig) {
            this.plugins.push(new Kwf.Form.HtmlEditor.InsertImage({
                componentConfig: this.imageComponentConfig
            }));
        }
        if (this.controllerUrl && this.enableTidy) {
            this.plugins.push(new Kwf.Form.HtmlEditor.Tidy());
        }
        if (this.enableStyles) {
            this.plugins.push(new Kwf.Form.HtmlEditor.Styles({
                styles: this.styles,
                stylesEditorConfig: this.stylesEditorConfig,
                stylesIdPattern: this.stylesIdPattern
            }));
        }
        this.plugins.push(new Kwf.Form.HtmlEditor.BreadCrumbs());

        Kwf.Form.HtmlEditor.superclass.initComponent.call(this);
    },
    initEditor : function() {
        Kwf.Form.HtmlEditor.superclass.initEditor.call(this);
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
        Ext2.EventManager.on(this.doc, {
            'mouseup': this.onEditorEvent,
            buffer:100,
            scope: this
        });

        var KwfEditor = Ext2.extend(tinymce.Editor, {
            orgVisibility: '',
            extEditor: this,
            getDoc: function() {
                return this.extEditor.getDoc();
            },
            getWin: function() {
                return this.extEditor.getWin();
            },
            getBody: function() {
                return this.extEditor.getEditorBody();
            },
            getElement: function() {
                return this.extEditor.el.dom;
            },
            focus: function(skip_focus) {
                tinyMCE.activeEditor = this;
                if (!skip_focus) {
                    this.extEditor.focus();
                }
            },
            setContent: function(content, args) {
                return tinymce.Editor.prototype.setContent.apply(this, arguments);
            }
        });

        var mceSettings = {
            fix_list_elements: true, // indenting bullet lists causes invalid ul/li sequence, with this setting the lists get repaired
            forced_root_block: 'p',
            browser_spellcheck: true
        };
        this.tinymceEditor = new KwfEditor(this.el.id, mceSettings, tinymce.EditorManager);
        this.tinymceEditor.initContentBody(true);

        this.tinymceEditor.on('nodeChange', (function(e) {
            this.updateToolbar();
        }).bind(this));


        this.formatter = this.tinymceEditor.formatter;

        this.originalValue = this.getEditorBody().innerHTML; // wegen isDirty, es wird der html vom browser dom mit dem originalValue verglichen, wo dann zB aus <br /> ein <br> wird
    },

    // private
    // überschrieben wegen spezieller ENTER behandlung im IE die wir nicht wollen
    fixKeys : function() { // load time branching for fastest keydown performance
        if (Ext2.isIE) {
            return function(e) {
                var k = e.getKey(), r;
                if (k == e.TAB){
                    e.stopEvent();
                    r = this.doc.selection.createRange();
                    if (r) {
                        r.collapse(true);
                        r.pasteHTML('&nbsp;&nbsp;&nbsp;&nbsp;');
                        this.deferFocus();
                    }
                //} else if (k == e.ENTER) {
                    //entfernt, wir wollen dieses verhalten genau so wie der IE es macht
                }
            };
        } else if (Ext2.isOpera) {
            return function(e) {
                var k = e.getKey();
                if (k == e.TAB) {
                    e.stopEvent();
                    this.win.focus();
                    this.execCmd('InsertHTML','&nbsp;&nbsp;&nbsp;&nbsp;');
                    this.deferFocus();
                }
            };
        } else if (Ext2.isWebKit) {
            return function(e) {
                var k = e.getKey();
                if (k == e.TAB) {
                    e.stopEvent();
                    this.execCmd('InsertText','\t');
                    this.deferFocus();
                }
             };
        }
    }(),

    getDocMarkup : function(){
        var ret = '<html><head>'+
            '<style type="text/css">'+
                'body{border:0;margin:0;padding:3px;height:98%;cursor:text;}'+
            '</style>\n';
        ret += '</head><body class="kwfUp-webStandard kwcText mce-content-body" id="tinymce" data-id="content"></body></html>';
        return ret;
    },
    setValue : function(v) {
        if (v && v.componentId) {
            this.componentId = v.componentId;
        }
        if (v && (typeof v.content) != 'undefined') v = v.content;
        Kwf.Form.HtmlEditor.superclass.setValue.call(this, v);
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
        var n = (Ext2.isIE ? s.getNode() : s.getStart()) || this.tinymceEditor.getBody();
        n = Ext2.isIE && n.ownerDocument != this.tinymceEditor.getDoc() ? this.tinymceEditor.getBody() : n; // Fix for IE initial state
        var parents = [];
        this.tinymceEditor.dom.getParent(n, function(node) {
            if (node.nodeName == 'BODY')
                return true;

            parents.push(node);
        });
        return parents;
    },

    //syncValue schreibt den inhalt vom iframe in die textarea
    syncValue : function(){
        if (!this.sourceEditMode && this.initialized) {
            var html = this.tinymceEditor.getContent();
            html = this.cleanHtml(html);
            if (this.fireEvent('beforesync', this, html) !== false) {
                this.el.dom.value = html;
                this.fireEvent('sync', this, html);
            }
        }
    },
    /**
     * Protected method that will not generally be called directly. Pushes the value of the textarea
     * into the iframe editor.
     */
    pushValue : function(){
        if (!this.sourceEditMode && this.tinymceEditor) {
            var v = this.el.dom.value;
            if(!this.activated && v.length < 1){
                v = this.defaultValue;
            }
            this.tinymceEditor.setContent(v);
        }
    },

    insertAtCursor : function(text) {
        if (!this.activated) {
            return;
        }
        this.tinymceEditor.editorCommands.execCommand('mceInsertContent', false, text);
    },

    execCmd : function(cmd, value) {
        this.tinymceEditor.editorCommands.execCommand(cmd, false, value);
        this.syncValue();
    }
});
Ext2.reg('htmleditor', Kwf.Form.HtmlEditor);
