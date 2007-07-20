Vps.Form.TextArea = function(config)
{
    Vps.Form.TextArea.superclass.constructor.call(this, config);
};

Ext.extend(Vps.Form.TextArea, Ext.form.TextArea,
{
    //um Enter in einem EditorGrid zu erlauben
    //kopiert von ext20 http://extjs.com/forum/showthread.php?p=45651
    //kann entfernt werden wenn wir auf ext20 updaten
    fireKey : function(e){
        if(e.isSpecialKey() && (e.getKey() != e.ENTER || e.hasModifier())){
            this.fireEvent("specialkey", this, e);
        }
    }
});
