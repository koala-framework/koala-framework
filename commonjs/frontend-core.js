var $ = require('jquery');

//this sets the public_path like it is set for the first found script tag
//required when using cdn
var uniquePrefix = 'kwfUp-';
if (uniquePrefix.length) uniquePrefix = uniquePrefix.substr(0, uniquePrefix.length-1);
var script = $('script[data-kwf-unique-prefix="'+uniquePrefix+'"]'); //look up by unique prefix, required when using e18
if (script.length) {
    var m = script[0].src.match(/^(.*\/assets\/build\/)/);
    if (m) {
        __webpack_public_path__ = m[1];
    }
}
