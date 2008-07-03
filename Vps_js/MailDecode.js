Vps.onContentReady(function()
{
    var atDecoding = '(vpsat)';
    var dotDecoding = '(vpsdot)';

    var els = Ext.query('a');
    els.forEach(function(el) {
        if (el.href && el.href.match(/^mailto:/)) {
            el.href = el.href.replace(atDecoding, '@');
            el.href = el.href.replace(dotDecoding, '.');
        }
    });

    var els = Ext.query('span.vpsEncodedMail');
    els.forEach(function(el) {
        var txt = el.innerHTML;
        txt = txt.replace(atDecoding, '@');
        el.innerHTML = txt.replace(dotDecoding, '.');
    });
});
