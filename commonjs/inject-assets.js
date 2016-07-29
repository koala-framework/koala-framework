var $ = require('jQuery');

function assetUrlEqual(a, b) {
    //remove v (version) paramete before comparing url
    a = a.replace(/\?.*$/, '');
    b = b.replace(/\?.*$/, '');
    return a == b;
}


function loadCSS(url, callback) {
    var se = document.createElement('link');
    se.rel = 'stylesheet';
    se.href = url;
    se.type = 'text/css';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(se, s);

    // link element doesn't have load event, use hackish img tag instead
    //(only one request will be made)
    var img = document.createElement('img');
    img.onerror = function() {
        if (callback) callback();
    }
    img.src = url;
}

function injectAssets(html, re, type, callbackOptions) {
    var m;
    while (m = re.exec(html)) {
        if (type == 'text/css') {
            var alreadyLoaded = false;
            $.each(document.getElementsByTagName('link'), function(k, i) {
                if (i.rel == 'stylesheet' && assetUrlEqual(i.href, location.protocol+'//'+location.host+m[1])) {
                    alreadyLoaded = true;
                }
            });
            if (alreadyLoaded) {
                continue;
            }
            callbackOptions.pending++;
            loadCSS(m[1], callbackOptions.loaded);
        } else {
            var alreadyLoaded = false;
            $.each(document.getElementsByTagName('script'), function(k, i) {
                if (assetUrlEqual(i.src, location.protocol+'//'+location.host+m[1])) {
                    alreadyLoaded = true;
                }
            });
            if (alreadyLoaded) {
                continue;
            }
            var se = document.createElement('script');
            se.src = m[1];
            se.type = type;
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(se, s);
        }
    }
}


//callback will called when all css files are loaded
module.exports = function(html, callback) {
    var callbackOptions = {
        pending: 0,
        loaded: function() {
            callbackOptions.pending--;
            if (callbackOptions.pending == 0) {
                if (callback) callback();
            }
        }
    };
    injectAssets(html, /<link rel="stylesheet" type="text\/css" href="([^"]+)" \/>(?!<!\[endif)/g, 'text/css', callbackOptions);
    injectAssets(html, /<script type="text\/javascript" src="([^"]+)"><\/script>(?!<!\[endif)/g, 'text/javascript', callbackOptions);
    injectAssets(html, /var se=document\.createElement\('script'\);se\.type='text\/javascript';se\.async=true;se\.src='([^']+)';/g, 'text/javascript', callbackOptions);

    var div = document.createElement('div');
    div.innerHTML = '<!--[if lte IE 8]><i></i><![endif]-->';
    if (div.getElementsByTagName('i').length > 0) {
        injectAssets(html, /<link rel="stylesheet" type="text\/css" href="([^"]+)" \/>(<!\[endif)/g, 'text/css', callbackOptions);
        injectAssets(html, /<script type="text\/javascript" src="([^"]+)"><\/script>(<!\[endif)/g, 'text/javascript', callbackOptions);
    }
    if (!callbackOptions.pending) {
        if (callback) callback();
    } else {
        //to avoid errors: if pending is not 0 within 500ms call callback
        setTimeout(function() {
            callbackOptions.pending = -1; //make sure it doesn't get 0 and callback is executed twice
            if (callback) callback();
        }, 500);
    }
};
