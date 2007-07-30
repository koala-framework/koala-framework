Ext.namespace('Vpc.Formular');
Vpc.Formular.Index = function(renderTo, config)
{
   // var par = new Vpc.Paragraphs.Index(renderTo, config);
   //par.on('editcomponent', this.edit, this);


	this.layout = new Ext.BorderLayout(renderTo, {
						center: {
				            initialSize: 700,
				            titlebar: true,
				            collapsible: true,
				            minSize: 200,
				            maxSize: 600
				        },
						east: {
				            split:true,
				            initialSize: 400,
				            titlebar: true,
				            collapsible: true,
				            minSize: 200,
				            maxSize: 600
				        }
					});
	this.layout.add("center", new Ext.ContentPanel("form", {autoCreate: true, title: 'Formular Elemente'}));
	this.layout.add("east", new Ext.ContentPanel("generalProperties", {autoCreate: true, title: 'Einstellungen'}));
	var form = new Vpc.Paragraphs.Index('form', config);
	form.on('editcomponent', this.edit, this);


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
				this.layout.remove("east", "generalProperties");
				this.layout.add("east", new Ext.ContentPanel("generalProperties", {autoCreate: true, title: 'Einstellungen'}));
				component = new cls('generalProperties', Ext.applyIf(response.config, {controllerUrl: controllerUrl, fitToFrame:true}));
            },
            scope: this
        });
    }
})

/*
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
                    var dialog = new Ext.LayoutDialog('name', {
                        autoCreate: true,
						autoScroll: false,
                        width:300,
                        height:300,
                        shadow:true,
                        minWidth:300,
                        minHeight:300,
                        proxyDrag: true,
						syncHeightBeforeShow: true
                    });

					component = new cls(dialog.body, Ext.applyIf(response.config, {controllerUrl: controllerUrl, fitToFrame:true}));

					dialog.show();
                }

            },
            scope: this
        });
    }
}) */


