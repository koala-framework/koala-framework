Vps.Form.HtmlEditor = Ext.extend(Ext.form.HtmlEditor,
    formatBlocks :
        'h1': 'Heading 1
        'h2': 'Heading 2
        'h3': 'Heading 3
        'h4': 'Heading 4
        'h5': 'Heading 5
        'h6': 'Heading 6
        'p': 'Normal
        'address': 'Address
        'pre': 'Formatte
    

    initComponent : function()
        this.actions = {

        //todo: lazy-loading von windo
        if (this.linkComponentConfig)
            this.enableLinks = fals
            var cls = eval(this.linkComponentConfig['class']
            var panel = new cls(Ext.applyIf(this.linkComponentConfig.config,
                baseCls: 'x-plain
                formConfig:
                    tbar: fal
               
            })
            this.linkDialog = new Vps.Auto.Form.Window
                autoForm: pane
                width: 66
                height: 4
            }
       
        if (this.imageComponentConfig)
            var cls = eval(this.imageComponentConfig['class']
            var panel = new cls(Ext.applyIf(this.imageComponentConfig.config,
                baseCls: 'x-plain
                formConfig:
                    tbar: fal
               
            })
            this.imageDialog = new Vps.Auto.Form.Window
                autoForm: pane
                width: 45
                height: 4
            }
       
        if (this.downloadComponentConfig)
            var cls = eval(this.downloadComponentConfig['class']
            var panel = new cls(Ext.applyIf(this.downloadComponentConfig.config,
                baseCls: 'x-plain
                formConfig:
                    tbar: fal
               
            })
            this.downloadDialog = new Vps.Auto.Form.Window
                autoForm: pane
                width: 45
                height: 4
            }
       

        Vps.Form.HtmlEditor.superclass.initComponent.call(this
    
    getAction : function(typ
   
        if (this.actions[type]) return this.actions[type

        if (type == 'insertImage')
            this.actions[type] = new Ext.Action
                icon: '/assets/silkicons/image.png
                handler: this.createImag
                scope: thi
                tooltip:
                    cls: 'x-html-editor-tip
                    title: 'Image
                    text: 'Insert new image or edit selected image
                
                cls: 'x-btn-icon
                clickEvent: 'mousedown
                tabIndex: 
            }
        } else if (type == 'insertDownload')
            this.actions[type] = new Ext.Action
                icon: '/assets/silkicons/page_white.png
                handler: this.createDownloa
                scope: thi
                tooltip:
                    cls: 'x-html-editor-tip
                    title: 'Download
                    text: 'Create new Download for the selected text or edit selected Download
                
                cls: 'x-btn-icon
                clickEvent: 'mousedown
                tabIndex: 
            }
        } else if (type == 'insertLink')
            this.actions[type] = new Ext.Action
                handler: this.createLin
                scope: thi
                tooltip:
                    cls: 'x-html-editor-tip
                    title: 'Hyperlink
                    text: 'Create new Link for the selected text or edit selected Link
                
                cls: 'x-btn-icon x-edit-createlink
                clickEvent: 'mousedown
                tabIndex: 
            }
        } else
            throw 'unknown action-type: ' + typ
       
        return this.actions[type
    
    initEditor : function()
        Vps.Form.HtmlEditor.superclass.initEditor.call(this
        Ext.EventManager.on(this.doc, 'keypress', function(e)
            if(e.ctrlKey
                var c = e.getCharCode(), cm
                if(c > 0
                    c = String.fromCharCode(c
                    if (c == 'v')
                        //tidy on pas
                        Ext.getBody().mask('Cleaning...'
                        this.tidyHtml.defer(500, this
                   
               
           
        }, this
    
    createToolbar: function(editor
        Vps.Form.HtmlEditor.superclass.createToolbar.call(this, editor
        var tb = this.getToolbar(
        tb.insert(7, '-'
        if (this.linkDialog)
            tb.insert(8,  this.getAction('insertLink')
       
        if (this.imageDialog)
            tb.insert(9, this.getAction('insertImage')
       
        if (this.downloadDialog)
            tb.insert(10,  this.getAction('insertDownload')
       
        this.linkComponentConf
        tb.add('-'
        tb.add
            icon: '/assets/silkicons/text_letter_omega.png
            handler: this.insertCha
            scope: thi
            tooltip:
                cls: 'x-html-editor-tip
                title: 'Character
                text: 'Insert a custom character
            
            cls: 'x-btn-icon
            clickEvent: 'mousedown
            tabIndex: 
        }
        tb.add
            icon: '/assets/silkicons/paste_plain.png
            handler: this.insertPlainTex
            scope: thi
            tooltip:
                cls: 'x-html-editor-tip
                title: 'Insert Plain Text
                text: 'Insert text without formating
            
            cls: 'x-btn-icon
            clickEvent: 'mousedown
            tabIndex: 
        }
        tb.add
            icon: '/assets/silkicons/html_valid.png
            handler: this.tidyHtm
            scope: thi
            tooltip:
                cls: 'x-html-editor-tip
                title: 'Clean Html
                text: 'Clean up Html and remove formatings
            
            cls: 'x-btn-icon
            clickEvent: 'mousedown
            tabIndex: 
        }

        this.blockSelect = tb.el.createChild
            tag:'select
            cls:'x-font-select
            html: this.createBlockOptions
        }
        this.blockSelect.on('change', function(
            var v = this.blockSelect.dom.valu
            if (Ext.isIE)
                v = '<'+v+'>
           
            this.relayCmd('formatblock', v
            this.deferFocus(
        }, this
        tb.insert(0, this.blockSelect.dom
        tb.insert(1, '-'

    

    createBlockOptions : function(
        var buf = [
        for (var i in this.formatBlocks)
            fb = this.formatBlocks[i
            buf.pus
                '<option value="',i,'"', /*style="font-family:',ff,';"',
                    (i == 'p' ? ' selected="true">' : '>'
                    f
                '</option
            
       
        return buf.join(''
    
    updateToolbar: function(
        Vps.Form.HtmlEditor.superclass.updateToolbar.call(this
        if (Ext.isIE)
            var el = this.getFocusElement(
            while (el)
                for(var i in this.formatBlocks)
                    if (el.tagName && i == el.tagName.toLowerCase())
                        if(i != this.blockSelect.dom.value
                            this.blockSelect.dom.value = 
                       
                        retur
                   
               
                el = el.parentNod
           
        } else
            var name = (this.doc.queryCommandValue('FormatBlock')||'p').toLowerCase(
            if(name != this.blockSelect.dom.value
                this.blockSelect.dom.value = nam
           
       
        var a = this.getFocusElement('a'
        if (a && a.tagName && a.tagName.toLowerCase() == 'a')
            var expr = new RegExp(this.page_id+this.component_key+'-(l|d)([0-9]+)'
            var m = a.href.match(expr
            if (m)
                if (m[1] == 'l')
                    this.getAction('insertLink').enable(
                    this.getAction('insertDownload').disable(
                } else if (m[1] == 'd')
                    this.getAction('insertLink').disable(
                    this.getAction('insertDownload').enable(
               
            } else
                this.getAction('insertLink').disable(
                this.getAction('insertDownload').disable(
           
        } else
            if (Ext.isIE)
                var selection = this.doc.selectio
            } else
                var selection = this.doc.getSelection(
           
            if (selection == '')
                this.getAction('insertLink').disable(
                this.getAction('insertDownload').disable(
            } else
                this.getAction('insertLink').enable(
                this.getAction('insertDownload').enable(
           
       
    
    getDocMarkup : function(
        return '<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;}</style>
               '<link rel="stylesheet" type="text/css" href="/assets/AllFrontend.css" />
               '</head><body class="content"></body></html>
    
    setValue : function(v)
        if (v.page_id && v.component_key)
            this.page_id = v.page_i
            this.component_key = v.component_ke
       
        if (v.content) v = v.conten
        Vps.Form.HtmlEditor.superclass.setValue.call(this, v
    

    createImage: function()
        var img = this.getFocusElement('img'
        if (img && img.tagName && img.tagName.toLowerCase() == 'img')
            var expr = new RegExp('/media/[0-9]+/[^/]+/'+this.page_id+this.component_key+'-i([0-9]+)/'
            var m = img.src.match(expr
            if (m)
                var nr = parseInt(m[1]
           
            if (nr)
                this.imageDialog.showEdit
                    page_id: this.page_i
                    component_key: this.component_key+'-i'+
                }
                this.imageDialog.on('datachange', function(r)
                    img.src = r.imageUr
                    img.width = r.imageDimension.widt
                    img.height = r.imageDimension.heigh
                }, this, {single: true}
                retur
           
       
        Ext.Ajax.request
            params: {page_id: this.page_id, component_key: this.component_key
            url: this.controllerUrl+'/jsonAddImage
            success: function(response, options, r)
                this.imageDialog.showEdit
                    page_id: r.page_i
                    component_key: r.component_k
                }
                this.imageDialog.on('datachange', function(r)
                    var html = '<img src="'+r.imageUrl+'" 
                    html += 'width="'+r.imageDimension.width+'" 
                    html += 'height="'+r.imageDimension.height+'" /
                    this.insertAtCursor(html
                }, this, {single: true}
            
            scope: th
        }
    
    createLink: function()
        var a = this.getFocusElement('a'
        if (a && a.tagName && a.tagName.toLowerCase() == 'a')
            var expr = new RegExp(this.page_id+this.component_key+'-l([0-9]+)'
            var m = a.href.match(expr
            if (m)
                var nr = parseInt(m[1]
           
            if (nr)
                this.linkDialog.un('datachange', this._insertLink, this
                this.linkDialog.showEdit
                    page_id: this.page_i
                    component_key: this.component_key+'-l'+
                }
                retur
           
       
        Ext.Ajax.request
            params: {page_id: this.page_id, component_key: this.component_key
            url: this.controllerUrl+'/jsonAddLink
            success: function(response, options, r)
                this.linkDialog.un('datachange', this._insertLink, this
                this.linkDialog.showEdit
                    page_id: r.page_i
                    component_key: r.component_k
                }
                this.linkDialog.on('datachange', this._insertLink, this, { single: true }
            
            scope: th
        }
    

    _insertLink : function()
        var params = this.linkDialog.getAutoForm().getBaseParams(
        this.relayCmd('createlink', params.page_id+params.component_key
    

    createDownload: function()
        var a = this.getFocusElement('a'
        if (a && a.tagName && a.tagName.toLowerCase() == 'a')
            var expr = new RegExp(this.page_id+this.component_key+'-d([0-9]+)'
            var m = a.href.match(expr
            if (m)
                var nr = parseInt(m[1]
           
            if (nr)
                this.downloadDialog.un('datachange', this._insertDownloadLink, this
                this.downloadDialog.showEdit
                    page_id: this.page_i
                    component_key: this.component_key+'-d'+
                }
                retur
           
       
        Ext.Ajax.request
            params: {page_id: this.page_id, component_key: this.component_key
            url: this.controllerUrl+'/jsonAddDownload
            success: function(response, options, r)
                this.downloadDialog.un('datachange', this._insertDownloadLink, this
                this.downloadDialog.showEdit
                    page_id: r.page_i
                    component_key: r.component_k
                }
                this.downloadDialog.on('datachange', this._insertDownloadLink, this, { single: true }
            
            scope: th
        }
    

    _insertDownloadLink : function()
        var params = this.downloadDialog.getAutoForm().getBaseParams(
        this.relayCmd('createlink', params.page_id+params.component_key
    
  
    insertChar: function
   
        var win = Vps.Form.HtmlEditor.insertCharWindow; //statische var, nur ein window erstell
        if (!win)
            win = new Vps.Form.InsertCharWindow
                modal: tru
                title: 'Insert Custom Character
                width: 50
                closeAction: 'hide
                autoScroll: tr
            }
            win.on('insertchar', function(win, char)
                this.insertAtCursor(char
                win.hide(
            }, this
            Vps.Form.HtmlEditor.insertCharWindow = wi
       
        win.show(
    

    insertPlainText: function
   
        Ext.Msg.prompt('Insert Plain Text', '
            function(btn, text)
                if (btn == 'ok')
                    this.insertAtCursor(text
               
            }, this, true
    

    tidyHtml: function
   
        Ext.getBody().mask('Cleaning...'
        Ext.Ajax.request
            url: this.controllerUrl+'/jsonTidyHtml
            params:
                page_id: this.page_i
                component_key: this.component_ke
                html: this.getValue
            
            success: function(response, options, r)
                if (this.getValue() != r.html)
                    this.setValue(r.html
               
            
            callback: function()
                Ext.getBody().unmask(
            
            scope: th
        }
    

    //priva
	getFocusElement : function(ta

        if (Ext.isIE)
            var rng = this.doc.selection.createRange(
            var elm = rng.item ? rng.item(0) : rng.parentElement(
        } else
            this.win.focus(); //Von m
            var sel = this.win.getSelection(
            if (!sel) return nul
            if (sel.rangeCount < 0) return nul

            var rng = sel.getRangeAt(0
            if (!rng) return nul

            var elm = rng.commonAncestorContaine

            // Handle selection a image or other control like element such as ancho
            if (!rng.collapsed)
                // Is selection sma
                if (rng.startContainer == rng.endContainer)
                    if (rng.startOffset - rng.endOffset < 2)
                        if (rng.startContainer.hasChildNodes(
                            elm = rng.startContainer.childNodes[rng.startOffset
                   
               
           
       
        if (tag && elm)
            while (elm && elm.parentNode 
                    (!elm.tagName || elm.tagName.toLowerCase() != tag))
                elm = elm.parentNod
           
       
        return el
    

    //protect
    toggleSourceEdit : function(sourceEditMode)
        this.tidyHtml(
        Vps.Form.HtmlEditor.superclass.toggleSourceEdit.call(this, sourceEditMode
   
}
Ext.reg('htmleditor', Vps.Form.HtmlEditor
