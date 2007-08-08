Ext.namespace('Vpc.Formular');
Vpc.Formular.Index = function(renderTo, config)
{
    this.layout = new Ext.BorderLayout(
        renderTo,
        {
            center: {
                initialSize: 700,
                titlebar: true,
                collapsible: true,
                minSize: 200,
                maxSize: 600
            },
            east: {
                split:true,
                initialSize: 500,
                titlebar: true,
                collapsible: true,
                minSize: 200,
                maxSize: 600
            }
        }
    );
    this.grid = new Vpc.Paragraphs.Index(this.layout.el.createChild(), config);
    this.layout.add("center", new Ext.GridPanel(this.grid.grid.grid, {autoCreate: true, title: 'Formular Elemente'}));
    this.layout.add("east", new Ext.ContentPanel("generalProperties", {autoCreate: true, title: 'Einstellungen'}));
    this.grid.on('editcomponent', this.edit, this);
};


Ext.extend(Vpc.Formular.Index, Ext.util.Observable,
{
    edit : function(o) {
        var controllerUrl = o.controllerUrl;
        Ext.Ajax.request({
            url: controllerUrl + 'jsonIndex/',
            success: function(r) {
                response = Ext.decode(r.responseText);
                cls = eval(response['class']);
                this.layout.remove("east", "generalProperties");
                this.layout.add("east", new Ext.ContentPanel("generalProperties", {autoCreate: true, title: 'Einstellungen'}));
                component = new cls('generalProperties', Ext.applyIf(response.config, {controllerUrl: controllerUrl, fitToFrame:true}));
            },
            scope: this
        });
    }

})




