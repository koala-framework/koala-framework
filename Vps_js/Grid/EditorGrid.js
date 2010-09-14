//Zusätzliches AfterEditComplete event, das auch gefeuert wird, wenn edit
//abgebrochen wurde bzw. der wert nicht geändert wurde
//(das normale afteredit wird nur gefeuert wenn wert geändert wurde)
//
//wird benötigt um den save-button im grid wida zu disablen wenn nix geändert wurde

Ext.grid.EditorGridPanel.baseOnEditComplete = Ext.grid.EditorGridPanel.prototype.onEditComplete;
Ext.grid.EditorGridPanel.prototype.onEditComplete = function(ed, value, startValue){
    Ext.grid.EditorGridPanel.baseOnEditComplete.apply(this, arguments);
    this.fireEvent("aftereditcomplete", ed, value, startValue);
};

Ext.grid.EditorGridPanel.baseOnRender = Ext.grid.EditorGridPanel.prototype.onRender;
Ext.grid.EditorGridPanel.prototype.onRender = function(ct, position){
    Ext.grid.EditorGridPanel.baseOnRender.apply(this, arguments);

    if (this.filtersInSeparateTbar) {
        var tb2 = new Ext.Toolbar(this.getTopToolbar().el);
        if (typeof this.filtersInSeparateTbar != 'boolean') {
            this.filters.applyToTbar(tb2, false, this.filtersInSeparateTbar);
        } else {
            this.filters.applyToTbar(tb2);
        }
    }

};
