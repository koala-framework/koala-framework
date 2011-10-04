//zusätzliche headerCss-einstellung für ColumnModel
Ext.grid.GridView.baseGetColumnStyle = Ext.grid.GridView.prototype.getColumnStyle;
Ext.grid.GridView.prototype.getColumnStyle = function(col, isHeader){
    var style = Ext.grid.GridView.baseGetColumnStyle.apply(this, arguments);
    if (isHeader && this.cm.config[col].headerCss) {
        style += this.cm.config[col].headerCss;
    }
    if (isHeader && this.cm.config[col].headerIcon) {
        style += String.format('background-image: url({0}); '+
                               'background-repeat: no-repeat; '+
                               'background-position: 5px 4px;',
                               this.cm.config[col].headerIcon);
    }
    return style;
};

//Workaround für Bug beschrieben hier:
//http://extjs.com/forum/showthread.php?p=205062#post205062
//!!!! entfernen wenn in ext behoben
Ext.grid.GridView.prototype.afterRender = function(){
    this.mainBody.dom.innerHTML = this.renderBody();
    this.processRows(0, true);

    if(this.deferEmptyText !== true){
        this.applyEmptyText();
    }
};
