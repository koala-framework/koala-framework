var oneTransitionEnd = function (el, callback, scope) {
    var transEndEventNames = {
        'WebkitTransition' : 'webkitTransitionEnd.kwfLightbox',
        'MozTransition'    : 'transitionend.kwfLightbox',
        'transition'       : 'transitionend.kwfLightbox'
    };
    var transitionType = Modernizr.prefixed('transition');
    if (!transitionType) return;

    var event = transEndEventNames[ transitionType ];
    if (transitionType != 'WebkitTransition') { // can be removed as soon as modernizr fixes https://github.com/Modernizr/Modernizr/issues/897
        event += ' ' +transEndEventNames['WebkitTransition'];
    }
    el.on(event, function() {
        el.off(event);
        callback.call(scope, arguments);
    });
};
module.exports = oneTransitionEnd;
