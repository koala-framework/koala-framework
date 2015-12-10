var postcss = require('postcss');

module.exports = postcss.plugin('unique-prefix', function (opts) {
    opts = opts || {};

    var replacements = opts.replacements;

    return function (css, result) {
        css.walkRules(/kwfUp-/, function (rule) {
            for (var i in replacements) {
                rule.selector = rule.selector.replace(new RegExp(i, 'g'), replacements[i]);
            }
        });

    };
});
