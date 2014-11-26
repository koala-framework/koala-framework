(function(){

var T = Ext2.Toolbar;

Ext2.override(T, {
    insertItem: function(index, item) {
        var td = document.createElement("td");
        this.tr.insertBefore(td, this.tr.childNodes[index]);
        this.initMenuTracking(item);
        item.render(td);
        this.items.insert(index, item);
        return item;
    },
    insert : function(index){
        var a = arguments, l = a.length;
        for(var i = 1; i < l; i++){
            var idx = index+i-1;
            var el = a[i];
            if(el.isFormField){ // some kind of form field
                return this.insertField(idx, el);
            }else if(el.render){ // some kind of Toolbar.Item
                return this.insertItem(idx, el);
            }else if(typeof el == "string"){ // string
                if(el == "separator" || el == "-"){
                    return this.insertSeparator(idx);
                }else if(el == " "){
                    return this.insertSpacer(idx);
                }else if(el == "->"){
                    return this.insertFill(idx);
                }else{
                    return this.insertText(idx, el);
                }
            }else if(el.tagName){ // element
                return this.insertElement(idx, el);
            }else if(typeof el == "object"){ // must be button config?
                if(el.xtype){
                    return this.insertField(idx, Ext2.ComponentMgr.create(el, 'button'));
                }else{
                    return this.insertButton(idx, el);
                }
            }
        }
    },
    insertText : function(index, text){
        return this.insertItem(index, new T.TextItem(text));
    },
    insertElement : function(index, el){
        return this.insertItem(index, new T.Item(el));
    },
    insertFill : function(index){
        return this.insertItem(index, T.Fill());
    },
    insertSeparator : function(index){
        return this.insertItem(index, new T.Separator());
    },
    insertSpacer : function(index){
        return this.insertItem(index, new T.Spacer());
    },
    insertDom : function(index, config){
        var td = document.createElement("td");
        this.tr.insertBefore(td, this.tr.childNodes[index]);
        Ext2.DomHelper.overwrite(td, config);
        var ti = new T.Item(td.firstChild);
        ti.render(td);
        this.items.add(ti);
        return ti;
    },
    insertField : function(index, field){
        var td = document.createElement("td");
        this.tr.insertBefore(td, this.tr.childNodes[index]);
        field.render(td);
        var ti = new T.Item(td.firstChild);
        ti.render(td);
        this.items.add(ti);
        return ti;
    }
});

})();
