Kwf.namespace(
'Kwc',
'Kwf.Component',
'Kwf.User.Login',
'Kwf.Auto',
'Kwf.Form',
'Kwf.Binding',
'Kwc.Advanced',
'Kwf.Debug',
'Kwf.Switch',
'Kwf.Basic.LinkTag.Extern',
'Kwf.Layout',
'Kwf.Utils'
);

//http://extjs.com/forum/showthread.php?t=26644
Kwf.clone = function(o) {
    if('object' !== typeof o || o === null) {
        return o;
    }
    var c = 'function' === typeof o.pop ? [] : {};
    var p, v;
    for(p in o) {
        if(o.hasOwnProperty(p)) {
            v = o[p];
            if('object' === typeof v && v !== null) {
                c[p] = Kwf.clone(v);
            }
            else {
                c[p] = v;
            }
        }
    }
    return c;
};

//log das auch ohne irgendwelche abh�nigkeiten funktioniert (zB im Selenium)
Kwf.log = function(msg) {
    if (!Kwf.debugDiv) {
        Kwf.debugDiv = document.createElement('div');
        document.body.appendChild(Kwf.debugDiv);
        Kwf.debugDiv.style.position = 'absolute';
        Kwf.debugDiv.style.zIndex = '300';
        Kwf.debugDiv.style.top = 0;
        Kwf.debugDiv.style.right = 0;
        Kwf.debugDiv.style.backgroundColor = 'white';
        Kwf.debugDiv.style.fontSize = '10px';
    }
    Kwf.debugDiv.innerHTML += msg+'<br />';
};

Kwf._componentEventHandlers = {};
/**
 * Fires a component event, used in frontend.
 *
 * @param string event name
 * @param parameters[...] pass to event handler
 */
Kwf.fireComponentEvent = function(evName) {
    if (Kwf._componentEventHandlers[evName]) {
        var args = [];
        for (var i=1; i<arguments.length; i++) { //remove first
            args.push(arguments[i]);
        }
        Kwf._componentEventHandlers[evName].forEach(function(i) {
            i.cb.apply(i.scope || window, args);
        }, this);
    }
};

/**
 * Adds event listener to a component event, used in frontend.
 *
 * @param string event name
 * @param callback function
 * @param scope
 */
Kwf.onComponentEvent = function(evName, cb, scope) {
    if (!Kwf._componentEventHandlers[evName]) Kwf._componentEventHandlers[evName] = [];
    Kwf._componentEventHandlers[evName].push({
        cb: cb,
        scope: scope
    });
};

Kwf.getKwcRenderUrl = function() {
    var url = '/kwf/util/kwc/render';
    if (Kwf.Debug.rootFilename) url = Kwf.Debug.rootFilename + url;
    if (location.search.match(/[\?&]kwcPreview/)) url += '?kwcPreview';
    return url;
}
