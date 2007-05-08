/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.menu.DateMenu=function(_1){Ext.menu.DateMenu.superclass.constructor.call(this,_1);this.plain=true;var di=new Ext.menu.DateItem(_1);this.add(di);this.picker=di.picker;this.relayEvents(di,["select"]);};Ext.extend(Ext.menu.DateMenu,Ext.menu.Menu);
