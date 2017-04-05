var onReady = require('kwf/commonjs/on-ready-ext2');

onReady.onShow('.kwfEyeCandyList', function eyeCandyList(el) {
    var opts = Ext2.fly(el).down('.options', true);
    if (opts) {
        opts = Ext2.decode(opts.value);
        var cls = Kwf.EyeCandy.List;
        if (opts['class']) {
            cls = eval(opts['class']);
            delete opts['class'];
        }
        opts.el = el;
        el.list = new cls(opts);
    }
}, { defer: true });
