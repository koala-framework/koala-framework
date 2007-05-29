/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.tree.TreeEditor = function(tree, config){
    config = config || {};
    // config can either be a prebuilt field, or a field config
    var field = config.events ? config : new Ext.form.TextField(config);
    Ext.tree.TreeEditor.superclass.constructor.call(this, field);

    this.tree = tree;

    tree.on('beforeclick', this.beforeNodeClick, this);
    tree.el.on('mousedown', this.hide, this);
    this.on('complete', this.updateNode, this);
    this.on('beforestartedit', this.fitToTree, this);
    this.on('startedit', this.bindScroll, this, {delay:10});
    this.on('specialkey', this.onSpecialKey, this);
};

Ext.extend(Ext.tree.TreeEditor, Ext.Editor, {
    alignment: "l-l",
    autoSize: false,
    hideEl : false,
    cls: "x-small-editor x-tree-editor",
    shim:false,
    shadow:"frame",
    maxWidth: 250,

    fitToTree : function(ed, el){
        var td = this.tree.el.dom, nd = el.dom;
        if(td.scrollLeft >  nd.offsetLeft){ // ensure the node left point is visible
            td.scrollLeft = nd.offsetLeft;
        }
        var w = Math.min(
                this.maxWidth,
                (td.clientWidth > 20 ? td.clientWidth : td.offsetWidth) - Math.max(0, nd.offsetLeft-td.scrollLeft) - /*cushion*/5);
        this.setSize(w, '');
    },

    triggerEdit : function(node){
        this.completeEdit();
        this.editNode = node;
        this.startEdit(node.ui.textNode, node.text);
    },

    bindScroll : function(){
        this.tree.el.on('scroll', this.cancelEdit, this);
    },

    beforeNodeClick : function(node){
        if(this.tree.getSelectionModel().isSelected(node)){
            this.triggerEdit(node);
            return false;
        }
    },

    updateNode : function(ed, value){
        this.tree.el.un('scroll', this.cancelEdit, this);
        this.editNode.setText(value);
    },

    onSpecialKey : function(field, e){
        var k = e.getKey();
        if(k == e.ESC){
            this.cancelEdit();
            e.stopEvent();
        }else if(k == e.ENTER && !e.hasModifier()){
            this.completeEdit();
            e.stopEvent();
        }
    }
});