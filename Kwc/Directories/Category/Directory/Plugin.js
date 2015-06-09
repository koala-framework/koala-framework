Ext2.namespace('Kwc.Directories.Category.Directory');
Kwc.Directories.Category.Directory.Plugin = function(config) {
    config.text = trlKwf('Categories');
    Ext2.apply(this, config);
};
Ext2.extend(Kwc.Directories.Category.Directory.Plugin, Kwc.Directories.Plugin.GridWindow,
{
});
