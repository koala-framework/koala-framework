Ext.namespace('Vpc.Formular.Option');
Vpc.Formular.Option.Index = function(renderTo, config)
{
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
    layout.add("center", new Ext.ContentPanel ("checkboxes1", {autoCreate: true, fitToFrame:true, title: 'Options'}));
    var cb = new Vps.Auto.Form("properties1", {controllerUrl: config.controllerUrl, fitToFrame:true});
    var cb = new Vps.Auto.Grid("checkboxes1", {controllerUrl: config.optionsControllerUrl, fitToFrame:true});
    layout.endUpdate();
};

Ext.extend(Vpc.Formular.Option.Index, Ext.util.Observable,{})