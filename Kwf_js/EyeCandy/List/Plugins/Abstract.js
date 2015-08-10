var kwfNs = require('kwf/namespace');
var kwfExtend = require('kwf/extend');
kwfNs("Kwf.EyeCandy.List.Plugins.StateChanger",
              "Kwf.EyeCandy.List.Plugins.StateListener",
              "Kwf.EyeCandy.List.Plugins.ActiveChanger",
              "Kwf.EyeCandy.List.Plugins.ActiveListener");

Kwf.EyeCandy.List.Plugins.Abstract = function(cfg) {
    Ext2.apply(this, cfg);
};
Kwf.EyeCandy.List.Plugins.Abstract.prototype = {
    //list
    init: function()
    {
    },
    render: function()
    {
    }
};
