Ext.namespace('Vps.Renderer');

Vps.Renderer.date = Ext.util.Format.dateRenderer('d.m.Y');

Vps.Renderer.boolean = function(value) {
    return value ? 'Ja' : 'Nein';
};

Vps.Renderer.password = function(value) {
    return value||true ? '******' : '';
};

Vps.Renderer.moneyEuro = function(v) {
    if (v == 0) return "";
    v = v.toString().replace(",", ".");
    v = (Math.round((v-0)*100))/100;
    v = (v == Math.floor(v)) ? v + ".00" : ((v*10 == Math.floor(v*10)) ? v + "0" : v);
    v = v.toString().replace(".", ",");
    return v + " €";
}
