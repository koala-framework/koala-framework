var onReady = require('kwf/commonjs/on-ready-ext2');

onReady.onRender('.kwcClass', function(el) {
    el.child('.submitWrapper button').on('click', function() {
        var button = el.child('.submitWrapper .button');
        button.down('.saving').show();
        button.down('.submit').hide();
    }, this);
});
