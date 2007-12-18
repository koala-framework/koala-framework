Ext.namespace('Vps.Renderer');

Ext.util.Format.boolean = function(v, p, record) {
    p.css += ' x-grid3-check-col-td'; 
    return '<div class="x-grid3-check-col'+(v?'-on':'')+'">&#160;</div>';
};
Ext.util.Format.booleanTickCross = function(v, p, record) {
    p.css += ' x-grid3-check-col-td'; 
    return '<div class="x-grid3-check-col vps-check-tick-cross-col'+(v?'-on':'')+'">&#160;</div>';
};

Ext.util.Format.password = function(value)
{
    return value||true ? '******' : '';
};

Ext.util.Format.euroMoney = function(v, p)
{
    if (p) p.css = 'vps-renderer-euro-money';
    if (v == 0) return "";
    v = v.toString().replace(",", ".");
    v = (Math.round((v-0)*100))/100;
    v = (v == Math.floor(v)) ? v + ".00" : ((v*10 == Math.floor(v*10)) ? v + "0" : v);
    v = v.toString().replace(".", ",");
    return v + " â‚¬";
};

Ext.util.Format.percent = function(v)
{
    return v + "%";
};

Ext.util.Format.showField = function(fieldName) {
    return function(value, p, record) {
        return record.data[fieldName];
    };
};

Ext.util.Format.nl2Br = function(v) {
    return v.replace(/\n/g, "<br />");
};

Ext.util.Format.component = function(v) {
    return '<iframe height="100" width="100%" frameborder="0" style="border: 1px solid darkgrey" src="' + v + '"></iframe>';
};

//date-funktion überschreiben, damit Y-m-d als eingabeformat verwendet werden kann
Ext.util.Format.date = function(v, format) {
    if(!v){
        return '';
    }
    if(!(v instanceof Date)){
        v = new Date(Date.parseDate(v, 'Y-m-d'));
    }
    return v.dateFormat(format || 'Y-m-d');
};

Ext.util.Format.localizedDate = Ext.util.Format.dateRenderer('Y-m-d');
Ext.util.Format.germanDate = Ext.util.Format.dateRenderer('d.m.Y');
Ext.util.Format.germanDay = function(value, p) {
    p.css += 'vps-renderer-bright';
    return Ext.util.Format.date(value, 'd.m.');    
}

Ext.util.Format.cellButton = function(value, p, record, rowIndex, colIndex, store, column) {
    p.css += 'vps-cell-button';
    if (column && column.buttonIcon) {
        p.attr += 'style="background-image:url('+column.buttonIcon+');" ';
    }
    if (column && column.tooltip) {
        p.attr += ' ext:qtip="'+column.tooltip+'"';
    }
    return '';
};
