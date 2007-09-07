Ext.namespace('Vps.Renderer');

Vps.Renderer.Date = Ext.util.Format.dateRenderer('d.m.Y');

Vps.Renderer.Boolean = function(value)
{
    return value ? 'Ja' : 'Nein';
};

Vps.Renderer.Password = function(value)
{
    return value||true ? '******' : '';
};

Vps.Renderer.MoneyEuro = function(v)
{
    if (v == 0) return "";
    v = v.toString().replace(",", ".");
    v = (Math.round((v-0)*100))/100;
    v = (v == Math.floor(v)) ? v + ".00" : ((v*10 == Math.floor(v*10)) ? v + "0" : v);
    v = v.toString().replace(".", ",");
    return v + " €";
};

Vps.Renderer.ShowField = function(fieldName) {
    return function(value, p, record) {
        return record.data[fieldName];
    };
};

Vps.Renderer.Nl2Br = function(v) {
    return v.replace(/\n/g, "<br />");
};

Vps.Renderer.Component = function(v) {
    return '<iframe height="100" width="100%" frameborder="0" style="border: 1px solid darkgrey" src="' + v + '"></iframe>';
};

