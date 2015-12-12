module.exports = function() {
    var url = '/kwf/util/kwc/render';
    if (typeof Kwf != 'undefined' && Kwf.Debug && Kwf.Debug.rootFilename) url = Kwf.Debug.rootFilename + url;
    if (location.search.match(/[\?&]kwcPreview/)) url += '?kwcPreview';
    return url;
};
