Ext.namespace('Vpc.PostsWritePreview');

Ext.onReady(function()
{
    var previews = Ext.query('div.vpcPostsWritePreview');
    Ext.each(previews, function(preview)
    {
        var previewTarget = Ext.query('div.previewBox', preview)[0];
        var previewSourceSelector = Ext.query('input.previewSourceSelector', preview)[0].value;
        var previewSource = Ext.query(previewSourceSelector)[0];
        if (previewSource) {
            previewTarget.innerHTML = Vpc.PostsWritePreview.replaceText(previewSource.value);

            previewSource = Ext.get(previewSource);

            previewSource.on('keyup', function(event, el) {
                previewTarget.innerHTML = Vpc.PostsWritePreview.replaceText(el.value);
            });
        }
    });
});

Vpc.PostsWritePreview.replaceText = function(v)
{
    // htmlspecialchars
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
    var pattern = /((http:\/\/)|(www\.)|(http:\/\/www\.)){1,1}([a-z0-9äöü;\/?:@=&!*~#%\'+$.,_-]+)/;
    while (v.substr(offset).match(pattern)) {
        offset += v.substr(offset).search(pattern);
        var matches = v.substr(offset).match(pattern);
        if (typeof matches[3] == 'undefined') matches[3] = '';

        var showUrl = matches[5];
        if (showUrl.length > 60) showUrl = showUrl.substr(0, 57) + '...';

        var rplc = "<a href=\"http:/"+"/" + matches[3] + matches[5] + "\" "
            + "title=\"" + matches[3] + matches[5] + "\" "
            + "target=\"blank\">" + matches[3] + showUrl + "</a>";

        v = v.substr(0, offset) + rplc + v.substr(offset + matches[0].length);

        offset += rplc.length;
    }

    // zeilenumbrüche
    v = v.replace(/\n/g, '<br />');

    return v;
};

