Ext.namespace('Vps.GoogleMap');
Vps.GoogleMap.isLoaded = false;
Vps.GoogleMap.isCallbackCalled = false;
Vps.GoogleMap.callbacks = [];

Vps.GoogleMap.load = function(callback, scope)
{
    if (Vps.GoogleMap.isCallbackCalled) {
        callback.call(scope || window);
        return;
    }
    if (Vps.GoogleMap.isLoaded) return;

    Vps.GoogleMap.isLoaded = true;
    Vps.GoogleMap.callbacks.push({
        callback: callback,
        scope: scope
    });

    var url = 'http://maps.google.com/maps?file=api&v=2.x&key={$googleMapsApiKey}&c&async=2';
    url += '&callback=Vps.GoogleMap._loaded';
    var s = document.createElement('script');
    s.setAttribute('type', 'text/javascript');
    s.setAttribute('src', url);
    document.getElementsByTagName("head")[0].appendChild(s);
};

Vps.GoogleMap._loaded = function()
{
    Vps.GoogleMap.isCallbackCalled = true;
    Vps.GoogleMap.callbacks.forEach(function(i) {
        i.callback.call(i.scope || window);
    });
};
