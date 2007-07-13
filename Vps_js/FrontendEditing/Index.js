Ext.namespace('Vps.FrontendEditing');
Vps.FrontendEditing.Index = function(renderTo, config)
{
    var elements = Ext.select('.component');
    elements.each(this.enableEditing, this);
    var element = elements.first();
    
}

Ext.extend(Vps.FrontendEditing.Index, Ext.util.Observable,
{
    enableEditing : function(el) {
        button = new Ext.Button (el.createChild(), {
            text: 'Bearbeiten',
            handler: this.edit,
            baseParams: { container: el },
            scope: this
        })
    },
    
    edit : function(o, e) {
        el = o.baseParams.container;
        id = el.dom.id;

        controllerUrl = '/component/edit/' + id + '/';
        Ext.Ajax.request({
            url: controllerUrl + 'jsonIndex/',
            success: this.showEditing,
            params: { container: el },
            scope: this
        });
    },
    
    showEditing : function(r, o)
    {
        el = o.params.container;
        Ext.DomHelper.overwrite(el, '');
        response = Ext.decode(r.responseText);
        cls = eval(response['class']);
        if (cls) {
            config = response.config.fe;
            if (config.wrap) {
                var content = new Ext.ContentPanel(el, {autoCreate: true});
                content.getEl().setWidth(config.width)
                content.getEl().setHeight(config.height)

                var toolbar = new Ext.Toolbar(content.el.createChild());
                toolbar.addButton({
                    text    : 'Zurück zur Ansicht',
                    icon    : '/assets/vps/images/silkicons/arrow_up.png',
                    cls     : 'x-btn-text-icon',
                    handler : this.showContent,
                    params: { container: el },
                    scope   : this
                });
                renderTo = content.el.createChild();
            } else {
                renderTo = el;
            }
            config = Ext.applyIf({ controllerUrl: controllerUrl }, response.config);
            component = new cls(renderTo, config);
        }
    },
    
    showContent : function(o, e)
    {
        Ext.Ajax.request({
            url: '/component/jsonShow/' + o.params.container.dom.id + '/',
            success: function (o, e) {
                r = Ext.decode(o.responseText);
                Ext.DomHelper.overwrite(e.params.container, r.content);
            },
            params: { container: o.params.container },
            scope: this
        });
    }
    
})
