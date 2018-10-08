/*
TODO port to jquery

Ext2.namespace('Kwc.PostsWritePreview');

Ext2.onReady(function()
{
    var previews = Ext2.query('div.kwcPostsWritePreview');
    Ext2.each(previews, function(preview)
    {
        var previewTarget = Ext2.query('div.previewBox', preview)[0];

        var sourceSelector = Ext2.query('input.sourceSelector', preview)[0].value;
        var previewSource = null;
        var rootNode = preview.parentNode;
        while (!previewSource) {
            previewSource = Ext2.query(sourceSelector, rootNode)[0];
            if (rootNode.tagName == 'BODY') break;
            rootNode = rootNode.parentNode;
        }

        if (previewSource) {
            previewTarget.innerHTML = Kwc.PostsWritePreview.replaceText(previewSource.value);
            Kwc.PostsWritePreview.scrollToBottom(previewTarget, 140);

            previewSource = Ext2.get(previewSource);

            previewSource.on('keyup', function(event, el) {
                previewTarget.innerHTML = Kwc.PostsWritePreview.replaceText(el.value);
                Kwc.PostsWritePreview.scrollToBottom(previewTarget, 140);
            }, previewSource, { buffer: 120 });
        }
    });
});

Kwc.PostsWritePreview.scrollToBottom = function(el, maxHeight) {
    var extEl = Ext2.get(el);
    if (extEl.getHeight() > maxHeight) {
        extEl.setHeight(maxHeight);
    }
    if (extEl.getHeight() >= maxHeight) {
        extEl.scroll("bottom", 20000);
    }
};

Kwc.PostsWritePreview.replaceText = function(v)
{
    // Kwf_Util_HtmlSpecialChars::filter
    v = v.replace(/&/g, '&amp;');
    v = v.replace(/"/g, '&quot;');
    v = v.replace(/</g, '&lt;');
    v = v.replace(/>/g, '&gt;');

    // smileys
    v = v.replace(/:-?\)/g, '<img src="/assets/silkicons/emoticon_smile.png" alt=":-)" />');
    v = v.replace(/:-?D/g, '<img src="/assets/silkicons/emoticon_grin.png" alt=":-D" />');
    v = v.replace(/:-?P/g, '<img src="/assets/silkicons/emoticon_tongue.png" alt=":-P" />');
    v = v.replace(/:-?\(/g, '<img src="/assets/silkicons/emoticon_unhappy.png" alt=":-(" />');
    v = v.replace(/;-?\)/g, '<img src="/assets/silkicons/emoticon_wink.png" alt=";-)" />');

    // zitate
    var qOpened = 0;
    var qClosed = 0;
    if (v.match(/\[quote\]/g)) qOpened += v.match(/\[quote\]/g).length;
    if (v.match(/\[quote=([^\]]*)\]/g)) qOpened += v.match(/\[quote=([^\]]*)\]/g).length;
    if (v.match(/\[\/quote\]/g)) qClosed += v.match(/\[\/quote\]/g).length;

    v = v.replace(/\[quote\]/g, '<fieldset class="quote"><legend>Zitat</legend>');
    v = v.replace(/\[quote=([^\]]*)\]/g, '<fieldset class="quote"><legend>Zitat von $1</legend>');
    v = v.replace(/\[\/quote\]/g, '</fieldset>');

    while (qOpened > qClosed) {
        v += '</fieldset>';
        qClosed += 1;
    }
    while (qClosed > qOpened) {
        v = '<fieldset class="quote"><legend>Zitat</legend>' + v;
        qOpened += 1;
    }

    // automatische verlinkung
    var offset = 0;
    var pattern = /((https?:\/\/www\.)|(https?:\/\/)|(www\.)){1,1}([A-Za-z0-9äöüÄÖÜ;\/?:@=&!*~#%\'+$.,_-]+)/;
    while (v.substr(offset).match(pattern)) {
        offset += v.substr(offset).search(pattern);
        var matches = v.substr(offset).match(pattern);

        if (!matches[1].match(/^http/)) matches[1] = 'http:/'+'/'+matches[1];

        var showUrl = matches[5];
        if (showUrl.length > 60) showUrl = showUrl.substr(0, 57) + '...';

        var rplc = "<a href=\"" + matches[1] + matches[5] + "\" "
            + "title=\"" + matches[1] + matches[5] + "\" "
            + "target=\"blank\">" + matches[1].replace(/https?:\/\/{1,1}/, '') + showUrl + "</a>";

        v = v.substr(0, offset) + rplc + v.substr(offset + matches[0].length);

        offset += rplc.length;
    }

    // zeilenumbrüche
    v = v.replace(/\n/g, '<br />');

    return v;
};

*/
