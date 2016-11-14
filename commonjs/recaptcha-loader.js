var isRequested = false;
var isLoaded = false;
var callbacks = [];

module.exports = function(callback) {
    if (!isLoaded) {
        callbacks.push(callback);
    } else {
        callbacks.call();
    }

    if (!isRequested) {
        var cb = 'kwfUp-recaptchaCallback'.replace('-', '_');
        window[cb] = function() {
            isLoaded = true;
            callbacks.forEach(function(callback) {
                callback.call();
            });
        };
        (function(cb){
            var el = document.createElement('script');
            el.src = 'https://www.google.com/recaptcha/api.js?onload=' + cb + '&render=explicit';
            el.type = 'text/javascript';
            el.async = true;
            var targetEl = document.getElementsByTagName('script')[0];
            targetEl.parentNode.insertBefore(el, targetEl);
        })(cb);
        isRequested = true;
    }
};
