Ext2.override(Ext2.tree.TreeLoader, {
    processResponse : function(response, node, callback){
        var r = Ext2.decode(response.responseText);
        var o = r.nodes;
        for(var i = 0, len = o.length; i < len; i++){
            var n = this.createNode(o[i]);
            if(n){
                node.appendChild(n); 
            }
        }
        if(typeof callback == "function"){
            callback(this, node);
        }
    },

    getParams: function(node){
        return Ext2.applyIf({
            node: node.id
        }, this.baseParams);
    }
});
