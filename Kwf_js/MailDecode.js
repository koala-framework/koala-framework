Kwf.onElementReady('a', function decodeMailHref(el)
{
    var atDecoding = '(kwfat)';
    var dotDecoding = '(kwfdot)';

    el = el.dom;
    if (el.href && el.href.match(/^mailto:/)) {
        el.href = el.href.replace(atDecoding, '@');
        el.href = el.href.replace(dotDecoding, '.');
    }
}, { defer: true });
Kwf.onElementReady('span.kwfEncodedMail', function decodeMail(el)
{
    var atDecoding = '(kwfat)';
    var dotDecoding = '(kwfdot)';

    el = el.dom;
    var txt = el.innerHTML;
    txt = txt.replace(atDecoding, '@');
    el.innerHTML = txt.replace(dotDecoding, '.');
});
