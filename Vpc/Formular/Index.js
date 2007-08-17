Ext.namespace('Vpc.Formular');
Vpc.Formular.Index = function(renderTo, config)
{
    this.events = {};
    this.layout = new Ext.BorderLayout(
        renderTo,
        {
            center: {
                initialSize: 400,
                titlebar: true,
                collapsible: true,
                minSize: 200,
                maxSize: 600
            },
            east: {
                split:true,
                initialSize: 300,
                titlebar: true,
                collapsible: true,
                minSize: 300,
                maxSize: 300,
                collapsed: true
            }
        }
    );
    this.grid = new Vpc.Paragraphs.Index(this.layout.el.createChild(), config);
    this.layout.add("center", new Ext.GridPanel(this.grid.grid.grid, {autoCreate: true, title: 'Formular Elemente'}));
    this.layout.add("east", new Ext.ContentPanel("generalProperties", {autoCreate: true, title: 'Einstellungen'}));
    this.grid.on('editcomponent', this.edit1, this);
    this.grid.grid.grid.on('rowclick', this.onRowClicked, this);
};


Ext.extend(Vpc.Formular.Index, Ext.util.Observable,
{
    edit1 : function(o) {
        var controllerUrl = o.controllerUrl;
        this.layout.remove("east", "generalProperties");
        if (!this.layout.getRegion('east').collapsed) {
            this.layout.getRegion("east").el.mask('loading...');
        }
        Ext.Ajax.request({
            url: controllerUrl + 'jsonIndex/',
            success: function(r) {
                this.layout.getRegion("east").expand();
                this.layout.getRegion("east").el.unmask();
                response = Ext.decode(r.responseText);
                cls = eval(response['class']);
                this.layout.add("east", new Ext.ContentPanel("generalProperties", {autoCreate: true, title: 'Einstellungen'}));
                component = new cls('generalProperties', Ext.applyIf(response.config, {controllerUrl: controllerUrl, fitToFrame:true}));
            },
            failure: function(r) {
                this.layout.getRegion("east").el.unmask();
            },
            scope: this
        });
    },

    onRowClicked : function(o) {
        if (!this.layout.getRegion('east').collapsed) {
            this.grid.edit();
        } else {
            this.layout.remove("east", "generalProperties");
        }
    }

})




