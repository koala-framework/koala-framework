Ext.namespace('Kwc.Directories.Category.Directory');
Kwc.Directories.Category.Directory.Plugin = function(config) {
    config.text = trlKwf('Categories');
    Ext.apply(this, config);
};
Ext.extend(Kwc.Directories.Category.Directory.Plugin, Kwc.Directories.Plugin.GridWindow,
{
});
