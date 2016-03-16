if (typeof Kwf == 'undefined') Kwf = {};
Kwf.loadDeferred = function(fn) {
    if (!Kwf._loadDeferred) Kwf._loadDeferred = [];
    if (Kwf._loadDeferred === 'done') {
        fn();
    } else {
        Kwf._loadDeferred.push(fn);
    }
};

document.write('<!--[if lt IE 9]><script type="text/javascript" src="/assets/html5shiv/dist/html5shiv.min.js"><' + '/script><![endif]-->');
