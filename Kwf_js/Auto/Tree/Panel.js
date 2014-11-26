/*
 * Tree mit Loading-Mask
 */
// http://www.sencha.com/forum/showthread.php?86323-Tree-LoadMask-not-centering-properly&p=440009#post440009
Ext2.namespace('Kwf.Auto.Tree');
Kwf.Auto.Tree.TreeLoadMask = function(el, config){
    this.el = Ext2.get(el);
    Ext2.apply(this, config);
    
    //minimal delay so that the tree panel is layed out and the mask will be centered.
    this.loader.on('beforeload', this.onBeforeLoad, this, {delay:1});
    this.loader.on('load', this.onLoad, this);
    this.loader.on('loadexception', this.onLoad, this);
    this.removeMask = Ext2.value(this.removeMask, false);
    this.unmasked = false;
};

Ext2.extend(Kwf.Auto.Tree.TreeLoadMask, Ext2.LoadMask, {
    onLoad : function(){
        Kwf.Auto.Tree.TreeLoadMask.superclass.onLoad.call(this);
        // Nur beim ersten Request Mask anzeigen
        this.loader.un('beforeload', this.onBeforeLoad, this);
        this.loader.un('load', this.onLoad, this);
        this.loader.un('loadexception', this.onLoad, this);
        this.unmasked = true;
    },
    onBeforeLoad : function(){
        if (!this.unmasked) {
            Kwf.Auto.Tree.TreeLoadMask.superclass.onBeforeLoad.call(this);
        }
    }
    
});

Kwf.Auto.Tree.Panel = Ext2.extend(Ext2.tree.TreePanel, {
    initEvents : function(){
        Kwf.Auto.Tree.Panel.superclass.initEvents.call(this);
        this.initMask();
    },
    initMask : function() {
        this._mask = new Kwf.Auto.Tree.TreeLoadMask(
            this.bwrap,
            Ext2.apply({loader:this.loader}, { msg: trlKwf('Loading...') })
        );
    }
});
