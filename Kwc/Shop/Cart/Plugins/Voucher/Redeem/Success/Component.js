var onReady = require('kwf/commonjs/on-ready-ext2');

onReady.onRender('.kwcClass', function(el) {
    //reload page to update cart
    //TODO this could be done better by using events and reloading the cart using ajax
    location.href = location.href;
});
