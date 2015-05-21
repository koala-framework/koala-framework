$(function() {
    $('#show').click(function() {
        $('.foo').show();
        Kwf.callOnContentReady(document.body, { action: 'show' });
    });
    $('#hide').click(function() {
        $('.foo').hide();
        Kwf.callOnContentReady(document.body, { action: 'hide' });
    });
});

Kwf.onJElementShow('.foo', function(el) {
    $('#log').append('show');
});
Kwf.onJElementHide('.foo', function(el) {
    $('#log').append('hide');
});
