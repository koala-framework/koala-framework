var apiKeys = require('kwf-webpack/loader/google-maps-api-key!');
require('core-js/es6/symbol');
var t = require('kwf/commonjs/trl');
var loadedLibraries;

var callbackFunctionName = 'kwfUp-KwfGoogleMapLoaded'.replace('-', '_');
if (typeof window[callbackFunctionName + '_callbacks'] === "undefined") window[callbackFunctionName + '_callbacks'] = [];

if (typeof window[callbackFunctionName + '_isCallbackCalled'] === "undefined") window[callbackFunctionName + '_isCallbackCalled'] = false;
if (typeof window[callbackFunctionName + '_isLoaded'] === "undefined") window[callbackFunctionName + '_isLoaded'] = false;

module.exports = function(callback, options)
{
    if (!options) options = {};
    if (!options.libraries) {
        options.libraries = [];
    }

    var scope = window;
    if (options.scope) {
        scope = options.scope;
    } else {
        scope = options;
    }

    // Add places library by default
    if (options.libraries.indexOf('places') == -1) {
        options.libraries.push('places');
    }
    options.libraries = options.libraries.sort(); //for comparing JSON

    if (loadedLibraries && JSON.stringify(options.libraries) !== JSON.stringify(loadedLibraries)) {
        throw new Error('Google map was already loaded with different libraries');
    }

    if (window[callbackFunctionName + '_isCallbackCalled']) {
        callback.call(scope);
        return;
    }
    window[callbackFunctionName + '_callbacks'].push({
        callback: callback,
        scope: scope
    });
    if (window[callbackFunctionName + '_isLoaded']) return;

    window[callbackFunctionName + '_isLoaded'] = true;


    //try find the correct api key
    var key = '';
    if (typeof apiKeys == 'string') {
        //one api key can have multiple domains configured
        key = apiKeys;
    } else {
        //for legacy reasons support multiple domains with individual api keys
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
        if (apiKeyIndex in apiKeys) {
            key = apiKeys[apiKeyIndex];
        }
    }

    var url = location.protocol+'/'+'/maps.googleapis.com/maps/api/js?v=3.exp&key='+key+'&c&libraries='+options.libraries.join(',')+'&async=2&language='+ __trlKwf('en');
    url += '&callback=' + callbackFunctionName;
    var s = document.createElement('script');
    s.setAttribute('type', 'text/javascript');
    s.setAttribute('src', url);
    document.getElementsByTagName("head")[0].appendChild(s);

    loadedLibraries = options.libraries;
};

window[callbackFunctionName] = function()
{
    window[callbackFunctionName + '_isCallbackCalled'] = true;
    window[callbackFunctionName + '_callbacks'].forEach(function(i) {
        i.callback.call(i.scope || window);
    });
};
