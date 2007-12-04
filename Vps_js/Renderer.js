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

Ext.util.Format.euroMoney = function(v)
{
    if (v == 0) return "";
    v = v.toString().replace(",", ".");
    v = (Math.round((v-0)*100))/100;
    v = (v == Math.floor(v)) ? v + ".00" : ((v*10 == Math.floor(v*10)) ? v + "0" : v);
    v = v.toString().replace(".", ",");
    return v + " €";
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

Ext.util.Format.localizedDate = Ext.util.Format.dateRenderer('d.m.Y');
