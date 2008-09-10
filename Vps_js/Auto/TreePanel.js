Vps.Auto.TreePanel = Ext.extend(Vps.Auto.SyncTreePanel, {

    onSaved : function (response)
    {
        node = this.tree.getNodeById(response.id);
        if (node == undefined) {
            if (response.data.parent_id == null) { response.data.parent_id = 0; }
            parentNode = this.tree.getNodeById(response.data.parent_id);
            if (parentNode.isExpanded()) {
                response.uiProvider = eval(response.uiProvider);
                node = new Ext.tree.AsyncTreeNode(response);
                if (parentNode.firstChild) {
                    parentNode.insertBefore(node, parentNode.firstChild);
                } else {
                    parentNode.appendChild(node);
                }
                parentNode.expand();
                this.tree.getSelectionModel().select(this.tree.getNodeById(response.id));
            } else {
                parentNode.expand();
            }
        } else {
            node.setText(response.text);
            node.attributes.visible = response.visible;
            this.setVisible(node);
        }
    },

    onDeleted: function (response) {
        node = this.tree.getNodeById(response.id);
        if (node.nextSibling) {
            sibling = node.nextSibling;
        } else if (node.previousSibling) {
            sibling = node.previousSibling;
        } else if (node.parentNode) {
            sibling = node.parentNode;
        }
        this.tree.getSelectionModel().select(sibling);
        node.parentNode.removeChild(node);
    }

});

Ext.reg('vps.autotree', Vps.Auto.TreePanel);
