Ext.namespace('Vps.Component.FrontendEditing');
Vps.Component.FrontendEditing.Index = function(renderTo, config)
{
    this.init();    
};

Ext.extend(Vps.Component.FrontendEditing.Index, Ext.util.Observable,
{
    init : function()
    {
        var elements = Ext.select('.component');
        elements.each(this.enableEditing, this);
    },
    
    enableEditing : function(el) {
        parts = el.dom.className.split(' ');
        if (parts[1]) {
            cls = parts[1].replace(/_/g, '.') + 'Fe';
            obj = eval(cls);
            if (obj != undefined) {
                button = new Ext.Button (el.createChild(), {
                    text: 'Bearbeiten',
                    handler: this.showEditing,
                    params: { container: el, obj: obj },
                    scope: this
                });
            }
        }
    },
    
    showEditing : function(r, o)
    {
        el = r.params.container;
        parts = el.dom.className.split(' ');
        cls = parts[1];
        id = el.dom.id.substr(10);
        Ext.DomHelper.overwrite(el, '');
        config = {
            controllerUrl: '/admin/component/edit/' + cls + '/' + id,
            caller : this
        };
        component = new r.params.obj(el, config);
    },
    
    showContent : function(o, e)
    {
        el = o.params.container.dom;
        parts = el.className.split(' ');
        cls = parts[1];
        Ext.Ajax.request({
            url: '/admin/component/json-show/' + cls + '/' + el.id.substr(10),
            success: function (o, e) {
                r = Ext.decode(o.responseText);
                Ext.DomHelper.overwrite(e.params.container, r.content);
                this.init();
            },
            params: { container: o.params.container },
            scope: this
        });
    }
    
});