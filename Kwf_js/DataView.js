Ext2.DataView.prototype.onAdd = function(ds, records, index)
{
    if(this.all.getCount() == 0){
        this.refresh();
        return;
    }
    var nodes = this.bufferRender(records, index), n, a = this.all.elements;
    if(index < this.all.getCount()){
        n = this.all.item(index).insertSibling(nodes, 'before', true);
        a.splice.apply(a, [index, 0].concat(nodes));
    }else{
        // BUGFIX
        for(var i=nodes.length-1; i>=0; i--) {
            n = this.all.last().insertSibling(nodes[i], 'after', true);
        }
        // /BUGFIX
        a.push.apply(a, nodes);
    }
    this.updateIndexes(index);
};
