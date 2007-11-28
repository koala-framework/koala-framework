(function(

var T = Ext.Toolba

Ext.override(T,
    insertItem: function(index, item)
        var td = document.createElement("td"
        this.tr.insertBefore(td, this.tr.childNodes[index]
        this.initMenuTracking(item
        item.render(td
        this.items.insert(index, item
        return ite
    
    insert : function(index
        var a = arguments, l = a.lengt
        for(var i = 1; i < l; i++
            var idx = index+i-
            var el = a[i
            if(el.isFormField){ // some kind of form fie
                this.insertField(idx, el
            }else if(el.render){ // some kind of Toolbar.It
                this.insertItem(idx, el
            }else if(typeof el == "string"){ // stri
                if(el == "separator" || el == "-"
                    this.insertSeparator(idx
                }else if(el == " "
                    this.insertSpacer(idx
                }else if(el == "->"
                    this.insertFill(idx
                }els
                    this.insertText(idx, el
               
            }else if(el.tagName){ // eleme
                this.insertElement(idx, el
            }else if(typeof el == "object"){ // must be button confi
                if(el.xtype
                    this.insertField(idx, Ext.ComponentMgr.create(el, 'button')
                }els
                    this.insertButton(idx, el
               
           
       
    
    insertText : function(index, text
        return this.insertItem(index, new T.TextItem(text)
    
    insertElement : function(index, el
        return this.insertItem(index, new T.Item(el)
    
    insertFill : function(index
        return this.insertItem(index, T.Fill()
    
    insertSeparator : function(index
        return this.insertItem(index, new T.Separator()
    
    insertSpacer : function(index
        return this.insertItem(index, new T.Spacer()
    
    insertDom : function(index, config
        var td = document.createElement("td"
        this.tr.insertBefore(td, this.tr.childNodes[index]
        Ext.DomHelper.overwrite(td, config
        var ti = new T.Item(td.firstChild
        ti.render(td
        this.items.add(ti
        return t
    
    insertField : function(index, field
        var td = document.createElement("td"
        this.tr.insertBefore(td, this.tr.childNodes[index]
        field.render(td
        var ti = new T.Item(td.firstChild
        ti.render(td
        this.items.add(ti
        return t
   
}

})(
