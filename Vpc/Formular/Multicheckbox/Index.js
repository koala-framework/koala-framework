Ext.namespace('Vpc.Formular.Multicheckbox');
Vpc.Formular.Multicheckbox.Index = function(renderTo, config)
{
	/*var tabs = new Ext.TabPanel(renderTo);

	tabs.addTab("checkboxes", "Checkboxes");
	tabs.addTab("properties", "Eigenschaften");

	tabs.activate("options");

	var cb = new Vps.Auto.Grid("checkboxes", {controllerUrl: config.checkboxesControllerUrl});
	var cb = new Vps.Auto.Form("properties", {controllerUrl: config.controllerUrl});*/


	var layout = new Ext.BorderLayout(renderTo, {
        center: {
            tabPosition: 'top',
            closeOnTab: true,
            alwaysShowTabs : true,
            autoScroll: true
        }
	});

	layout.beginUpdate();
	layout.add("center", new Ext.ContentPanel("properties1", {autoCreate: true, title: 'Properties'}));
	layout.add("center", new Ext.ContentPanel ("checkboxes1", {autoCreate: true, fitToFrame:true, title: 'Checkboxes'}));
	var cb = new Vps.Auto.Form("properties1", {controllerUrl: config.controllerUrl, fitToFrame:true});
	var cb = new Vps.Auto.Grid("checkboxes1", {controllerUrl: config.checkboxesControllerUrl});
	layout.endUpdate();
};

Ext.extend(Vpc.Formular.Multicheckbox.Index, Ext.util.Observable,{})