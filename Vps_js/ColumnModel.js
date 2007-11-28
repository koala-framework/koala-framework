//hilfs-funktionen um columns einfacher holen zu könn
Ext.grid.ColumnModel.prototype.getColumnsByDataIndex = function(dataIndex)
    return this.getColumnsBy(function(c)
        if (c.dataIndex == dataIndex) return tru
    }

Ext.grid.ColumnModel.prototype.getColumnByDataIndex = function(dataIndex)
    var r = this.getColumnsByDataIndex(dataIndex
    if (r.length == 0) return nul
    return r[0

