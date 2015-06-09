//workaround für Permission denied to access property 'dom' from non-chrome context
//siehe http://www.extjs.com/forum/showthread.php?t=74765
//fixed in Ext 3
Ext2.Element.prototype.contains = function(el) {
    try {
        return !el ? false : Ext2.lib.Dom.isAncestor(this.dom, el.dom ? el.dom : el);
    } catch(e) {
        return false;
    }
};
