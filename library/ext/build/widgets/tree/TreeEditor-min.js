/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.tree.TreeEditor=function(_1,_2){_2=_2||{};var _3=_2.events?_2:new Ext.form.TextField(_2);Ext.tree.TreeEditor.superclass.constructor.call(this,_3);this.tree=_1;_1.on("beforeclick",this.beforeNodeClick,this);_1.el.on("mousedown",this.hide,this);this.on("complete",this.updateNode,this);this.on("beforestartedit",this.fitToTree,this);this.on("startedit",this.bindScroll,this,{delay:10});this.on("specialkey",this.onSpecialKey,this);};Ext.extend(Ext.tree.TreeEditor,Ext.Editor,{alignment:"l-l",autoSize:false,hideEl:false,cls:"x-small-editor x-tree-editor",shim:false,shadow:"frame",maxWidth:250,fitToTree:function(ed,el){var td=this.tree.el.dom,nd=el.dom;if(td.scrollLeft>nd.offsetLeft){td.scrollLeft=nd.offsetLeft;}var w=Math.min(this.maxWidth,(td.clientWidth>20?td.clientWidth:td.offsetWidth)-Math.max(0,nd.offsetLeft-td.scrollLeft)-5);this.setSize(w,"");},triggerEdit:function(_9){this.completeEdit();this.editNode=_9;this.startEdit(_9.ui.textNode,_9.text);},bindScroll:function(){this.tree.el.on("scroll",this.cancelEdit,this);},beforeNodeClick:function(_a){if(this.tree.getSelectionModel().isSelected(_a)){this.triggerEdit(_a);return false;}},updateNode:function(ed,_c){this.tree.el.un("scroll",this.cancelEdit,this);this.editNode.setText(_c);},onSpecialKey:function(_d,e){var k=e.getKey();if(k==e.ESC){this.cancelEdit();e.stopEvent();}else{if(k==e.ENTER&&!e.hasModifier()){this.completeEdit();e.stopEvent();}}}});
