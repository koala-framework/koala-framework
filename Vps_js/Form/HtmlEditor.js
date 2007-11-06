Vps.Form.HtmlEditor = Ext.extend(Ext.form.HtmlEditor, {
    initComponent : function() {
        
        Vps.Form.HtmlEditor.superclass.initComponent.call(this);
    },
    createToolbar: function(editor){
        Vps.Form.HtmlEditor.superclass.createToolbar.call(this, editor);
        var tb = this.getToolbar();
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
    },
//     getDocMarkup : function(){
//         return '<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;}</style></head><body></body></html>';
//     },
    createImage: function() {
        var img = this.getFocusElement();
        if (img && img.tagName && img.tagName.toLowerCase() == 'img') {
            Ext.Ajax.request({
                params: { src: img.src },
                url: this.controllerUrl+'/jsonEditImage',
                success: function(response, options, r) {
                    var c = eval(r.config.class);
                    Ext.apply(r.config.config, {
                        formConfig: { tbar: false },
                        baseCls: 'x-plain'
                    });
                    var dialog = new Vps.Auto.Form.Window({
                        autoForm: new c(r.config.config),
                        width: 450,
                        height: 400
                    });
                    dialog.on('datachange', function(r) {
                        img.src = r.imageUrl;
                        if (r.size.width) img.size.width = r.size.width;
                        if (r.size.height) img.size.height = r.size.height;
                    }, this);
                    dialog.showEdit();
                },
                scope: this
            });
        } else {
            Ext.Ajax.request({
                params: { content: this.getValue() },
                url: this.controllerUrl+'/jsonAddImage',
                success: function(response, options, r) {
                    var c = eval(r.config.class);
                    Ext.apply(r.config.config, {
                        formConfig: { tbar: false },
                        baseCls: 'x-plain'
                    });
                    var dialog = new Vps.Auto.Form.Window({
                        autoForm: new c(r.config.config),
                        width: 450,
                        height: 400
                    });
                    dialog.on('datachange', function(r) {
                        this.relayCmd('insertimage', r.imageUrl);
                    }, this);
                    dialog.showEdit();
                },
                scope: this
            });
        }
    },
    createLink: function() {
        var a = this.getFocusElement();
        if (a && a.parentNode && (!a.tagName || a.tagName.toLowerCase() != 'a')) {
            a = a.parentNode;
        }
        console.log(a);
        if (a && a.tagName && a.tagName.toLowerCase() == 'a') {
            Ext.Ajax.request({
                params: { href: a.href },
                url: this.controllerUrl+'/jsonEditLink',
                success: function(response, options, r) {
                    var c = eval(r.config.class);
                    Ext.apply(r.config.config, {
                        formConfig: { tbar: false },
                        baseCls: 'x-plain'
                    });
                    var dialog = new Vps.Auto.Form.Window({
                        autoForm: new c(r.config.config),
                        width: 660,
                        height: 300
                    });
                    dialog.on('datachange', function(r) {
                        img.href = r.href;
                    }, this);
                    dialog.showEdit();
                },
                scope: this
            });
        } else {
            Ext.Ajax.request({
                params: { content: this.getValue() },
                url: this.controllerUrl+'/jsonAddLink',
                success: function(response, options, r) {
                    var c = eval(r.config.class);
                    Ext.apply(r.config.config, {
                        formConfig: { tbar: false },
                        baseCls: 'x-plain'
                    });
                    var dialog = new Vps.Auto.Form.Window({
                        autoForm: new c(r.config.config),
                        width: 660,
                        height: 300
                    });
                    dialog.on('datachange', function(r) {
                        this.relayCmd('createlink', r.href);
                    }, this);
                    dialog.showEdit();
                },
                scope: this
            });
        }
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

    cleanHtml : function(html){
        html = Vps.Form.HtmlEditor.superclass.cleanHtml.call(this, html);
        html = html.replace(/\sclass="(?:Mso.+)"/g, ''); //Word-Scheiß
        return html;
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
