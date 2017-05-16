var oneTransitionEnd = function (el, callback, scope) {
    var event = 'webkitTransitionEnd.kwfLightbox transitionend.kwfLightbox';
    el.on(event, function() {
        el.off(event);
        callback.call(scope, arguments);
    });
};
module.exports = oneTransitionEnd;
