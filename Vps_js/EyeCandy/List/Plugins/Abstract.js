Ext.namespace("Vps.EyeCandy.List.Plugins.StateChanger",
              "Vps.EyeCandy.List.Plugins.StateListener",
              "Vps.EyeCandy.List.Plugins.ActiveChanger",
              "Vps.EyeCandy.List.Plugins.ActiveListener");

Vps.EyeCandy.List.Plugins.Abstract = function(cfg) {
    Ext.apply(this, cfg);
};
Vps.EyeCandy.List.Plugins.Abstract.prototype = {
    //list
    init: function()
    {
    },
    render: function()
    {
    }
};
