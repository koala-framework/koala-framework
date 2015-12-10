var postcss = require('postcss');

module.exports = postcss.plugin('unique-prefix', function (opts) {
    opts = opts || {};

    var prefix = opts.prefix || '';

    return function (css, result) {
        css.walkRules(/kwfUp-/, function (rule) {
            rule.selector = rule.selector.replace('kwfUp-', prefix);
        });

    };
});
