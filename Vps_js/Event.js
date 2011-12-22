Ext.namespace('Vps.Event');

/**
 * Ermöglicht Vps.Event.on(el, 'mouseEnter') oder auch 'mouseLeave'.
 * Problem: Wenn man mit mouseOver und mouseOut arbeitet bei verschachtelten dom-elementen
 * dann passiert es, dass die mouseOver und mouseOut events ein paar mal bekommt, wenn man
 * die verschachtelten nodes wechselt.
 *
 * MouseEnter und mouseLeave wird immer nur einmal gefeuert.
 */
Vps.Event.addListener = function(el, eventName, handler, scope, options) {
    var eventName = eventName.toLowerCase();
    if (!el.vpsEvent) el.vpsEvent = {};

    if (eventName == 'mouseenter' || eventName == 'mouseleave') {

        if (eventName == 'mouseenter') {
            var firstEvent = 'mouseover';
            var secondEvent = 'mouseout';
        } else if (eventName == 'mouseleave') {
            var firstEvent = 'mouseout';
            var secondEvent = 'mouseover';
        }

        Ext.EventManager.addListener(el, firstEvent, function() {
            el.vpsEvent[eventName] = true;
            (function(handler, scope, el) {
                if (el.vpsEvent[eventName] && !el.vpsEvent[eventName+'_current']) {
                    el.vpsEvent[eventName+'_current'] = true;
                    handler.call(scope);
                }
            }).defer(1, scope, [ handler, scope, el ]);
        }, scope, options);

        Ext.EventManager.addListener(el, secondEvent, function() {
            el.vpsEvent[eventName] = false;
            (function(el) {
                if (!el.vpsEvent[eventName]) {
                    el.vpsEvent[eventName+'_current'] = false;
                }
            }).defer(1, scope, [ el ]);
        }, scope, options);

    } else {
        throw "Vps.Event: eventName '"+eventName+"' is not yet implemented.";
    }
};

// shortcut
Vps.Event.on = Vps.Event.addListener;
