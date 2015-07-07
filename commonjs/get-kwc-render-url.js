module.exports = function() {
    var url = '/kwf/util/kwc/render';
    if (Kwf.Debug.rootFilename) url = Kwf.Debug.rootFilename + url;
    if (location.search.match(/[\?&]kwcPreview/)) url += '?kwcPreview';
    return url;
};
