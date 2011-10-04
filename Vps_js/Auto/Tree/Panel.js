/*
 * Tree mit Loading-Mask
 */
// http://www.sencha.com/forum/showthread.php?86323-Tree-LoadMask-not-centering-properly&p=440009#post440009
Ext.namespace('Vps.Auto.Tree');
Vps.Auto.Tree.TreeLoadMask = function(el, config){
    this.el = Ext.get(el);
    Ext.apply(this, config);
    
    //minimal delay so that the tree panel is layed out and the mask will be centered.
    this.loader.on('beforeload', this.onBeforeLoad, this, {delay:1});
    this.loader.on('load', this.onLoad, this);
    this.loader.on('loadexception', this.onLoad, this);
    this.removeMask = Ext.value(this.removeMask, false);
};

Ext.extend(Vps.Auto.Tree.TreeLoadMask, Ext.LoadMask, {
    onLoad : function(){
		Vps.Auto.Tree.TreeLoadMask.superclass.onLoad.call(this);
		// Nur beim ersten Request Mask anzeigen
        this.loader.un('beforeload', this.onBeforeLoad, this);
        this.loader.un('load', this.onLoad, this);
        this.loader.un('loadexception', this.onLoad, this);
	}
});

Vps.Auto.Tree.Panel = Ext.extend(Ext.tree.TreePanel, {
    initEvents : function(){
        Vps.Auto.Tree.Panel.superclass.initEvents.call(this);
        this.initMask();
    },
    initMask : function() {
        this._mask = new Vps.Auto.Tree.TreeLoadMask(
        	this.bwrap,
            Ext.apply({loader:this.loader}, { msg: trlVps('Loading...') })
        );
    }
});
