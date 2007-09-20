//from ext/examples/grid/edit-grid.js
Ext.namespace('Vps.Grid');

Vps.Grid.CheckColumn = function(config) {
    Ext.apply(this, config);
    if(!this.id){
        this.id = Ext.id();
    }
    this.renderer = this.renderer.createDelegate(this);
};

Vps.Grid.CheckColumn.prototype ={
    init : function(grid){
        this.grid = grid;
        var onRender = function() {
            var view = this.grid.getView();
            view.mainBody.on('mousedown', this.onMouseDown, this);
        };

        //kann auf zwei arten verwendet werden:
        // - als plugin wie beim editor gird example von ext
        // - oder wenn grid schon gerendered wurde und init händisch aufgerufen wird
        if (this.grid.rendered) {
            onRender.call(this);
        } else {
            this.grid.on('render', onRender, this);
        }
    },

    onMouseDown : function(e, t){
        if(t.className && t.className.indexOf('x-grid3-cc-'+this.id) != -1){
            e.stopEvent();
            var index = this.grid.getView().findRowIndex(t);
            var record = this.grid.store.getAt(index);
            record.set(this.dataIndex, !record.data[this.dataIndex]);
        }
    },

    renderer : function(v, p, record) {
        p.css += ' x-grid3-check-col-td'; 
        return '<div class="x-grid3-check-col'+(v?'-on':'')+' x-grid3-cc-'+this.id+'">&#160;</div>';
    }
};
