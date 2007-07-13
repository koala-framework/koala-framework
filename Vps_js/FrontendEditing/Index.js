Ext.namespace('Vps.FrontendEditing');
Vps.FrontendEditing.Index = function(renderTo, config)
{
    this.init();    
}

Ext.extend(Vps.FrontendEditing.Index, Ext.util.Observable,
{
    init : function()
    {
        var elements = Ext.select('.component');
        elements.each(this.enableEditing, this);
        var element = elements.first();
    },
    
    enableEditing : function(el) {
        parts = el.dom.className.split(' ');
        if (parts[1]) {
            cls = parts[1].replace(/_/g, '.') + 'Fe';
            obj = eval(cls);
            if (obj) {
                button = new Ext.Button (el.createChild(), {
                    text: 'Bearbeiten',
                    handler: this.showEditing,
                    params: { container: el, obj: obj },
                    scope: this
                })
            }
        }
    },
    
    showEditing : function(r, o)
    {
        el = r.params.container;
        Ext.DomHelper.overwrite(el, '');
        config = {
            controllerUrl: '/component/edit/' + el.dom.id.substr(10) + '/',
            caller : this
        }
        component = new r.params.obj(el, config);
    },
    
    showContent : function(o, e)
    {
        Ext.Ajax.request({
            url: '/component/jsonShow/' + o.params.container.dom.id.substr(10) + '/',
            success: function (o, e) {
                r = Ext.decode(o.responseText);
                Ext.DomHelper.overwrite(e.params.container, r.content);
                this.init();
            },
            params: { container: o.params.container },
            scope: this
        });
    }
    
})
