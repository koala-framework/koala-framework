/*
 * Ext JS Library 1.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.LayoutManager=function(_1){Ext.LayoutManager.superclass.constructor.call(this);this.el=Ext.get(_1);if(this.el.dom==document.body&&Ext.isIE){document.body.scroll="no";}else{if(this.el.dom!=document.body&&this.el.getStyle("position")=="static"){this.el.position("relative");}}this.id=this.el.id;this.el.addClass("x-layout-container");this.monitorWindowResize=true;this.regions={};this.events={"layout":true,"regionresized":true,"regioncollapsed":true,"regionexpanded":true};this.updating=false;Ext.EventManager.onWindowResize(this.onWindowResize,this,true);};Ext.extend(Ext.LayoutManager,Ext.util.Observable,{isUpdating:function(){return this.updating;},beginUpdate:function(){this.updating=true;},endUpdate:function(_2){this.updating=false;if(!_2){this.layout();}},layout:function(){},onRegionResized:function(_3,_4){this.fireEvent("regionresized",_3,_4);this.layout();},onRegionCollapsed:function(_5){this.fireEvent("regioncollapsed",_5);},onRegionExpanded:function(_6){this.fireEvent("regionexpanded",_6);},getViewSize:function(){var _7;if(this.el.dom!=document.body){_7=this.el.getSize();}else{_7={width:Ext.lib.Dom.getViewWidth(),height:Ext.lib.Dom.getViewHeight()};}_7.width-=this.el.getBorderWidth("lr")-this.el.getPadding("lr");_7.height-=this.el.getBorderWidth("tb")-this.el.getPadding("tb");return _7;},getEl:function(){return this.el;},getRegion:function(_8){return this.regions[_8.toLowerCase()];},onWindowResize:function(){if(this.monitorWindowResize){this.layout();}}});
