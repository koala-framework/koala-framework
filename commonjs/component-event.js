
var componentEventHandlers = {};

module.exports = {

/**
 * Fires a component event, used in frontend.
 *
 * @param string event name
 * @param parameters[...] pass to event handler
 */
trigger: function(evName) {
    if (componentEventHandlers[evName]) {
        var args = [];
        for (var i=1; i<arguments.length; i++) { //remove first
            args.push(arguments[i]);
        }
        componentEventHandlers[evName].forEach(function(i) {
            i.cb.apply(i.scope || window, args);
        }, this);
    }
},

/**
 * Adds event listener to a component event, used in frontend.
 *
 * @param string event name
 * @param callback function
 * @param scope
 */
on: function(evName, cb, scope) {
    if (!componentEventHandlers[evName]) componentEventHandlers[evName] = [];
    componentEventHandlers[evName].push({
        cb: cb,
        scope: scope
    });
}

};
