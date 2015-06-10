Kwf.onElementReady('.cssClass', function(el) {
    //reload page to update cart
    //TODO this could be done better by using events and reloading the cart using ajax
    location.href = location.href;
});
