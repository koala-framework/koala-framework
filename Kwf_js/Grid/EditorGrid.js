//Zusätzliches AfterEditComplete event, das auch gefeuert wird, wenn edit
//abgebrochen wurde bzw. der wert nicht geändert wurde
//(das normale afteredit wird nur gefeuert wenn wert geändert wurde)
//
//wird benötigt um den save-button im grid wida zu disablen wenn nix geändert wurde

Ext2.grid.EditorGridPanel.baseOnEditComplete = Ext2.grid.EditorGridPanel.prototype.onEditComplete;
Ext2.grid.EditorGridPanel.prototype.onEditComplete = function(ed, value, startValue){
    Ext2.grid.EditorGridPanel.baseOnEditComplete.apply(this, arguments);
    this.fireEvent("aftereditcomplete", ed, value, startValue);
};

Ext2.grid.EditorGridPanel.baseOnRender = Ext2.grid.EditorGridPanel.prototype.onRender;
Ext2.grid.EditorGridPanel.prototype.onRender = function(ct, position){
    Ext2.grid.EditorGridPanel.baseOnRender.apply(this, arguments);

    if (this.filtersInSeparateTbar) {
        var tb2 = new Ext2.Toolbar(this.getTopToolbar().el);
        if (typeof this.filtersInSeparateTbar != 'boolean') {
            this.filters.applyToTbar(tb2, false, this.filtersInSeparateTbar);
        } else {
            this.filters.applyToTbar(tb2);
        }
    }

};
