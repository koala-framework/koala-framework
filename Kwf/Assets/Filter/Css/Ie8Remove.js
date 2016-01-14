var postcss = require('postcss');

module.exports = postcss.plugin('ie8-remove', function (opts) {
    opts = opts || {};

    var mode = opts.mode;

    return function (css, result) {
        css.walkAtRules(function (atrule) {
            if (atrule.name == 'ie8') {
                atrule.remove();
            }
        });
    };
});
