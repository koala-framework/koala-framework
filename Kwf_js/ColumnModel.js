//hilfs-funktionen um columns einfacher holen zu können
Ext2.grid.ColumnModel.prototype.getColumnsByDataIndex = function(dataIndex) {
    return this.getColumnsBy(function(c) {
        if (c.dataIndex == dataIndex) return true;
    });
};
Ext2.grid.ColumnModel.prototype.getColumnByDataIndex = function(dataIndex) {
    var r = this.getColumnsByDataIndex(dataIndex);
    if (r.length == 0) return null;
    return r[0];
};
