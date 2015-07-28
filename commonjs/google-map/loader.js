Kwf.namespace('Kwf.GoogleMap');
var isLoaded = false;
var isCallbackCalled = false;
var callbacks = [];

module.exports = function(callback, scope)
{
    if (isCallbackCalled) {
        callback.call(scope || window);
        return;
    }
    callbacks.push({
        callback: callback,
        scope: scope
    });
    if (isLoaded) return;

    isLoaded = true;

    //try find the correct api key
    //Kwf.GoogleMap.apiKeys is set by Kwf_Assets_Dependency_Dynamic_GoogleMapsApiKeys
    //and contains possibly multiple api keys (to support multiple domains)
    var apiKeyIndex;

    var hostParts = location.host.split('.');
    if (hostParts.length <= 1) {
        apiKeyIndex = location.host;
    } else {
        apiKeyIndex = hostParts[hostParts.length-2]  // eg. 'koala-framework'
            +hostParts[hostParts.length-1]; // eg. 'org'
    }
    if (['orat', 'coat', 'gvat', 'couk'].indexOf(apiKeyIndex) != -1) {
        //one part more for those
        apiKeyIndex = hostParts[hostParts.length-3]+apiKeyIndex;
    }

    var key = '';
    if (apiKeyIndex in Kwf.GoogleMap.apiKeys) {
        key = Kwf.GoogleMap.apiKeys[apiKeyIndex];
    }
    var url = location.protocol+'/'+'/maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key='+key+'&c&libraries=places&async=2&language='+trlKwf('en');
    url += '&callback=Kwf.GoogleMap._loaded';
    var s = document.createElement('script');
    s.setAttribute('type', 'text/javascript');
    s.setAttribute('src', url);
    document.getElementsByTagName("head")[0].appendChild(s);
};

Kwf.GoogleMap._loaded = function()
{
    isCallbackCalled = true;
    callbacks.forEach(function(i) {
        i.callback.call(i.scope || window);
    });
};
