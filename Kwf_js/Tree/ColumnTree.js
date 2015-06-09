Ext2.namespace('Kwf.Tree');
Kwf.Tree.ColumnTree = Ext2.extend(Ext2.tree.TreePanel, {
    lines:false,
    borderWidth: Ext2.isBorderBox ? 0 : 2, // the combined left/right border for each cell
    cls:'x2-column-tree',
    
    onRender : function(){
        Kwf.Tree.ColumnTree.superclass.onRender.apply(this, arguments);
        this.headers = this.body.createChild(
            {cls:'x2-tree-headers'},this.innerCt.dom);

        var cols = this.columns, c;
        var totalWidth = 0;

        for(var i = 0, len = cols.length; i < len; i++){
             c = cols[i];
             totalWidth += c.width;
             this.headers.createChild({
                 cls:'x2-tree-hd ' + (c.cls?c.cls+'-hd':''),
                 cn: {
                     cls:'x2-tree-hd-text',
                     html: c.header
                 },
                 style:'width:'+(c.width-this.borderWidth)+'px;'
             });
        }
        this.headers.createChild({cls:'x2-clear'});
        // prevent floats from wrapping when clipped
        this.headers.setWidth(totalWidth);
        this.innerCt.setWidth(totalWidth);
    }
});