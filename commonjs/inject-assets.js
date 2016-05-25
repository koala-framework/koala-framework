var $ = require('jQuery');

function injectAssets(html, re, type) {
    var m;
    while (m = re.exec(html)) {
        var se;
        if (type == 'text/css') {
            var alreadyLoaded = false;
            $.each(document.getElementsByTagName('link'), function(k, i) {
                if (i.rel == 'stylesheet' && i.href == location.protocol+'//'+location.host+m[1]) {
                    alreadyLoaded = true;
                }
            });
            if (alreadyLoaded) {
                continue;
            }
            se = document.createElement('link');
            se.rel = 'stylesheet';
            se.href = m[1];
        } else {
            var alreadyLoaded = false;
            $.each(document.getElementsByTagName('script'), function(k, i) {
                if (i.src == location.protocol+'//'+location.host+m[1]) {
                    alreadyLoaded = true;
                }
            });
            if (alreadyLoaded) {
                continue;
            }
            se = document.createElement('script');
            se.src = m[1];
        }
        se.type = type;
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(se, s);
    }
}

module.exports = function(html) {
    injectAssets(html, /<link rel="stylesheet" type="text\/css" href="([^"]+)" \/>(?!<!\[endif)/g, 'text/css');
    injectAssets(html, /<script type="text\/javascript" src="([^"]+)"><\/script>(?!<!\[endif)/g, 'text/javascript');
    injectAssets(html, /var se=document\.createElement\('script'\);se\.type='text\/javascript';se\.async=true;\s+se\.src='([^']+)';/g, 'text/javascript');

    var div = document.createElement('div');
    div.innerHTML = '<!--[if lte IE 8]><i></i><![endif]-->';
    if (div.getElementsByTagName('i').length > 0) {
        injectAssets(html, /<link rel="stylesheet" type="text\/css" href="([^"]+)" \/>(<!\[endif)/g, 'text/css');
        injectAssets(html, /<script type="text\/javascript" src="([^"]+)"><\/script>(<!\[endif)/g, 'text/javascript');
    }
};
