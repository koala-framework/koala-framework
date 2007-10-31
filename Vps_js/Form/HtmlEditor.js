Vps.Form.HtmlEditor = Ext.extend(Ext.form.HtmlEditor, {
    initComponent : function() {
        
        Vps.Form.HtmlEditor.superclass.initComponent.call(this);
    },
    createToolbar: function(editor){
        Vps.Form.HtmlEditor.superclass.createToolbar.call(this, editor);
        var tb = this.getToolbar();
        tb.add('-');
        tb.add({
            icon: '/assets/vps/images/silkicons/image.png',
            handler: this.insertImage,
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
    },
//     getDocMarkup : function(){
//         return '<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;}</style></head><body></body></html>';
//     },
    insertImage: function() {
        var img = this.getFocusElement();
        if (img && img.tagName && img.tagName.toLowerCase() == 'img') {
            Ext.Ajax.request({
                params: { src: img.src },
                url: this.controllerUrl+'jsonEditImage/',
                success: function(response, options, r) {
                    var c = eval(r.config.class);
                    Ext.apply(r.config.config, {
                        formConfig: { tbar: false },
                        baseCls: 'x-plain'
                    });
                    var dialog = new Vps.Auto.Form.Window({
                        autoForm: new c(r.config.config),
                        width: 450,
                        height: 200
                    });
                    dialog.on('datachange', function(r) {
                        img.src = r.imageUrl;
                    }, this);
                    dialog.showEdit();
                },
                scope: this
            });
        } else {
            Ext.Ajax.request({
                params: { content: this.getValue() },
                url: this.controllerUrl+'jsonAddImage/',
                success: function(response, options, r) {
                    var c = eval(r.config.class);
                    Ext.apply(r.config.config, {
                        formConfig: { tbar: false },
                        baseCls: 'x-plain'
                    });
                    var dialog = new Vps.Auto.Form.Window({
                        autoForm: new c(r.config.config),
                        width: 450,
                        height: 200
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

            elm = rng.commonAncestorContainer;

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

//             return tinyMCE.getParentElement(elm);
        }
    }
});
Ext.reg('htmleditor', Vps.Form.HtmlEditor);
