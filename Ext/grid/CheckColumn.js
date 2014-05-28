//based on ext/examples/grid/edit-grid.js
//WURDE ANGEPASST!
/*
 * Ext JS Library 2.0 Beta 2
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 *
 * http://extjs.com/license
 */

Ext2.grid.CheckColumn = function(config){
    Ext2.apply(this, config);
    if(!this.id){
        this.id = Ext2.id();
    }
    this.actualRenderer = this.renderer || Ext2.util.Format.boolean;
    this.renderer = this.addIdRenderer.createDelegate(this);
};

Ext2.grid.CheckColumn.prototype ={
    init : function(grid){
        this.grid = grid;
        this.grid.on('render', function(){
            var view = this.grid.getView();
            view.mainBody.on('mousedown', this.onMouseDown, this);
        }, this);
    },

    onMouseDown : function(e, t){
        //max drei ebenen nach oben gehen und schaun ob wir eine passende id finden
        for (var i = 0; i < 3; i++) {
            if(t.className && t.className.indexOf('x2-grid3-cc-'+this.id) != -1){
                e.stopEvent();
                var index = this.grid.getView().findRowIndex(t);
                var record = this.grid.store.getAt(index);
                record.set(this.dataIndex, !record.data[this.dataIndex]);
                return;
            }
            t = t.parentNode;
        }
    },

    //zusätzlich zum css vom renderer noch was einfügen
    addIdRenderer : function(v, p, record){
        var ret = this.actualRenderer.apply(this, arguments);
        p.css += 'x2-grid3-cc-'+this.id;
        return ret;
    }
};
