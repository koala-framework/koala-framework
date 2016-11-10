var libraryLoaded = false;
var callbacks = [];

module.exports = function(callback) {
    if (!libraryLoaded) {
        callbacks.push(callback);
    } else {
        callback.call();
    }
};

var cb = 'kwfUp-recaptchaCallback'.replace('-', '_');
window[cb] = function() {
    callbacks.forEach(function(callback) {
        callback.call();
    });
    libraryLoaded = true;
};
(function(cb){
    a='https://www.google.com/recaptcha/api.js?onload=' + cb + '&render=explicit';
    b=document;c='script';d=b.createElement(c);d.src=a;d.type='text/java'+c;d.async=true;
    a=b.getElementsByTagName(c)[0];a.parentNode.insertBefore(d,a);
})(cb);
