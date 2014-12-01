Kwf.Form.TreeSelect = Ext2.extend(Kwf.Form.AbstractSelect,
{
    // mandatory parameters
    // controllerUrl (for the tree)

    // optional parameters
    // windowWidth, windowHeight
    // displayField

    _getWindowItem: function()
    {
        if (!this._windowItem) {
            this._windowItem = new Kwf.Auto.TreePanel({
                controllerUrl: this.controllerUrl,
                listeners: {
                    click: function(node) {
                        if (!this.displayField && node) {
                            var nodeTexts = [];
                            nodeTexts.push(node.text);
                            var nd = node.parentNode;
                            while (nd) {
                                nodeTexts.push(nd.text);
                                nd = nd.parentNode;
                            }
                            var nodeText = '';
                            for (var i = 0; i < nodeTexts.length - (typeof this.cutNodes != 'undefined' ? this.cutNodes : 1); i++) {
                                nodeText = nodeTexts[i] + ' » ' + nodeText;
                            }
                            if (nodeText) nodeText = nodeText.substr(0, nodeText.length-3);
                        }

                        this._selectWin.value = {
                            id: node.id,
                            name: this.displayField ? node.attributes.data[this.displayField] : nodeText
                        };
                    },
                    scope: this
                }
            });
        }
        return this._windowItem;
    }
});
Ext2.reg('treeselect', Kwf.Form.TreeSelect);
