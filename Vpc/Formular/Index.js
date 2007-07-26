Ext.namespace('Vpc.Formular');
Vpc.Formular.Index = function(renderTo, config)
{
    var par = new Vpc.Paragraphs.Index(renderTo, config);
    par.on('editcomponent', this.edit, this);
    
};

Ext.extend(Vpc.Formular.Index, Ext.util.Observable,
{
    edit : function(o) {
        var controllerUrl = '/component/edit/' + o.id + '/';
        Ext.Ajax.request({
            url: controllerUrl + 'jsonIndex/',
            success: function(r) {
                response = Ext.decode(r.responseText);
                cls = eval(response['class']);
                if (cls) {
                    var dialog = new Ext.BasicDialog('edit', {
                        autoCreate: true,
                        width:400,
                        height:400,
                        shadow:true,
                        minWidth:300,
                        minHeight:250,
                        proxyDrag: true
                    });
                    component = new cls(dialog.body, Ext.applyIf(response.config, {controllerUrl: controllerUrl}));
                    dialog.show();
                }
                
            },
            scope: this
        });
    }
})
