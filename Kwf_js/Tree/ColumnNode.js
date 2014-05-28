Kwf.Tree.ColumnNode = Ext2.extend(Kwf.Tree.Node, {
    focus: Ext2.emptyFn, // prevent odd scrolling behavior

    renderElements : function(n, a, targetNode, bulkRender){
        this.indentMarkup = n.parentNode ? n.parentNode.ui.getChildIndent() : '';

        var t = n.getOwnerTree();
        var cols = t.columns;
        var bw = t.borderWidth;
        var c = cols[0];

        var buf = [
             '<li class="x2-tree-node"><div ext:tree-node-id="',n.id,'" class="x2-tree-node-el x2-tree-node-leaf ', a.cls,'">',
                '<div class="x2-tree-col" style="width:',c.width-bw,'px;">',
                    '<span class="x2-tree-node-indent">',this.indentMarkup,"</span>",
                    '<img src="', this.emptyIcon, '" class="x2-tree-ec-icon x2-tree-elbow">',
                    '<img src="', a.icon || this.emptyIcon, '" class="x2-tree-node-icon',(a.icon ? " x2-tree-node-inline-icon" : ""),(a.iconCls ? " "+a.iconCls : ""),'" unselectable="on">',
                    '<a hidefocus="on" class="x2-tree-node-anchor" href="',a.href ? a.href : "#",'" tabIndex="1" ',
                    a.hrefTarget ? ' target="'+a.hrefTarget+'"' : "", '>',
                    '<span unselectable="on">', n.text || (c.renderer ? c.renderer(a[c.dataIndex], n, a) : a[c.dataIndex]),"</span></a>",
                "</div>"];
         for(var i = 1, len = cols.length; i < len; i++){
             c = cols[i];

             /*
			 buf.push('<div class="x2-tree-col ',(c.cls?c.cls:''),'" style="width:',c.width-bw,'px;">',
                        '<div class="x2-tree-col-buttons">',(c.renderer ? c.renderer(a[c.dataIndex], n, a) : a[c.dataIndex]),"</div>",
                      "</div>");
                      */
             buf.push('<div class="x2-tree-col-buttons ',(c.cls?c.cls:''),'" style="width:',c.width-bw,'px;">',
                        (c.renderer ? c.renderer(a[c.dataIndex], n, a) : a[c.dataIndex]),
                      "</div>");
         }
         buf.push(
            '<div class="x2-clear"></div></div>',
            '<ul class="x2-tree-node-ct" style="display:none;"></ul>',
            "</li>");

        if(bulkRender !== true && n.nextSibling && n.nextSibling.ui.getEl()){
            this.wrap = Ext2.DomHelper.insertHtml("beforeBegin",
                                n.nextSibling.ui.getEl(), buf.join(""));
        }else{
            this.wrap = Ext2.DomHelper.insertHtml("beforeEnd", targetNode, buf.join(""));
        }

        this.elNode = this.wrap.childNodes[0];
        this.ctNode = this.wrap.childNodes[1];
        var cs = this.elNode.firstChild.childNodes;
        this.indentNode = cs[0];
        this.ecNode = cs[1];
        this.iconNode = cs[2];
        this.anchor = cs[3];
        this.textNode = cs[3].firstChild;
    }
});