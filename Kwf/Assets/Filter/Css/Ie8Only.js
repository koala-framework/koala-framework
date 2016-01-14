var postcss = require('postcss');

module.exports = postcss.plugin('ie8-only', function (opts) {
    opts = opts || {};

    var mode = opts.mode;

    return function (css, result) {
        css.walkRules(function (rule) {
            var p = rule.parent;
            var isIe8 = false;
            while (p) {
                if (p.type == 'atrule' && p.name == 'ie8') {
                    isIe8 = true;
                    break;
                }
                p = p.parent;
            }
            if (!isIe8) {
                rule.remove();
            }
        });

        css.walkAtRules(function (atrule) {
            if (atrule.name == 'ie8') {
                atrule.nodes.forEach(function(i) {
                    atrule.root().append(i);
                });
                atrule.remove();
            } else {
                atrule.remove();
            }
        });

    };
});
