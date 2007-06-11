/*
 * @enhancements
 * Add method to Grid to restore state.
 */
Ext.apply(Ext.grid.Grid.prototype, {
  restoreState: function (provider) {
    if (!provider) {
      provider = Ext.state.Manager;
    }
    var sm = new Ext.grid.GridStateManager();
    sm.init(this, provider);    
  }
});

/*
 * Class for saving grid state. Modeled on LayoutStateManager
 */
Ext.grid.GridStateManager = function(){
     // default empty state
     this.state = {
        hidden: { },
        locked: { },
        widths: { }
    };
};

Ext.grid.GridStateManager.prototype = {
    init : function(grid, provider){
        this.provider = provider;
        this.grid = grid;
        var state = provider.get(this.getStateKey());
        if(state) {
            state.hidden = state.hidden || { };
            for (var colId in state.hidden) {
              var colIndex = this.grid.getColumnModel().getIndexById(colId);
              if (colIndex && colIndex >= 0) {
                this.grid.getColumnModel().setHidden(colIndex, state.hidden[colIndex]);
              }
            }            
            state.locked = state.locked || { };
            for (var colId in state.locked) {
              var colIndex = this.grid.getColumnModel().getIndexById(colId);
              if (colIndex && colIndex >= 0) {
                this.grid.getColumnModel().setLocked(colIndex, state.locked[colIndex]);
              }
            }            
            state.widths = state.widths || { };
            for (var colId in state.widths) {
              var colIndex = this.grid.getColumnModel().getIndexById(colId);
              if (colIndex && colIndex >= 0) {
                this.grid.getColumnModel().setColumnWidth(colIndex, state.widths[colIndex]);
              }
            }            
            if (state.sortInfo) {
              this.grid.getDataSource().setDefaultSort(state.sortInfo.field, state.sortInfo.direction);
            }
            if (state.colIds) {
              for (var i = 0; i < state.colIds.length; i++) {
                var savedColId = state.colIds[i];
                var initialColIndex = this.grid.getColumnModel().getIndexById(savedColId);
                if (initialColIndex && initialColIndex > 0 && initialColIndex != i) {
                  this.grid.getColumnModel().moveColumn(initialColIndex, i);
                }               
              }
            }
            this.state = state; 
        }
        grid.getColumnModel().on("hiddenchange", this.onHiddenChange, this);
        // for some reason the widthchange event on ColumnModel always gives width = undefined
        // so have to list to the event on the grid directly
        grid.on("columnresize", this.onColumnResize, this);
        grid.getColumnModel().on("columnlockchange", this.onLockChange, this);
        grid.getColumnModel().on("columnmoved", this.onColumnMove, this);
        grid.getDataSource().on("datachanged", this.onSort, this);
    },
    
    getStateKey : function() {
      return this.grid.id + "-grid-state";    
    },
    
    storeState : function(){
        this.provider.set(this.getStateKey(), this.state);
    },
    
    onHiddenChange : function(cm, colIndex, hidden){
        var colId = cm.getColumnId(colIndex);
        this.state.hidden[colId] = hidden;
        this.storeState();
    },
    
    onColumnResize : function(colIndex, width){
        var colId = this.grid.getColumnModel().getColumnId(colIndex);
        this.state.widths[colId] = width;
        this.storeState();
    },
    
    onLockChange : function(cm, colIndex, lockState){
        var colId = cm.getColumnId(colIndex);
        this.state.locked[colId] = lockState;
        this.storeState();
    },
    
    onColumnMove : function(cm, oldIndex, newIndex){
        // we do this one by saving array of current order of column ids
        var colIds = [];
        for(var i = 0; i < cm.getColumnCount(); i++){
            colIds.push(cm.getColumnId(i));
        } 
        this.state.colIds = colIds;       
        this.storeState();
    },
    
    onSort : function(dataSource){
        if (dataSource.sortInfo) {
          this.state.sortInfo = { field: dataSource.sortInfo.field, direction: dataSource.sortInfo.direction }
          this.storeState();
        }
    }
};