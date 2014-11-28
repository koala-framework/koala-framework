(function() {
    Ext.ns('Kwf.Utils.BenchmarkBox');

    var benchmarkEnabled = Kwf.Debug.benchmark;
    if (!benchmarkEnabled) {
        benchmarkEnabled = location.search.match(/[\?&]KWF_BENCHMARK/);
    }
    if (!benchmarkEnabled) {
        //noop implementation if not enabled
        Kwf.Utils.BenchmarkBox.now = function() { return 0; };
        Kwf.Utils.BenchmarkBox.count = function() { };
        Kwf.Utils.BenchmarkBox.time = function() { };
        Kwf.Utils.BenchmarkBox.subTime = function() { };
        Kwf.Utils.BenchmarkBox.create = function() { };
        return;
    }

    var setCookie = function(name, value){
        var argv = arguments,
            argc = arguments.length,
            expires = (argc > 2) ? argv[2] : null,
            path = (argc > 3) ? argv[3] : '/',
            domain = (argc > 4) ? argv[4] : null,
            secure = (argc > 5) ? argv[5] : false;

        document.cookie = name + "=" + escape(value) + ((expires === null) ? "" : ("; expires=" + expires.toGMTString())) + ((path === null) ? "" : ("; path=" + path)) + ((domain === null) ? "" : ("; domain=" + domain)) + ((secure === true) ? "; secure" : "");
    };
    var getCookieVal = function(offset) {
        var endstr = document.cookie.indexOf(";", offset);
        if(endstr == -1){
            endstr = document.cookie.length;
        }
        return unescape(document.cookie.substring(offset, endstr));
    };
    var getCookie = function(name) {
        var arg = name + "=",
            alen = arg.length,
            clen = document.cookie.length,
            i = 0,
            j = 0;

        while(i < clen){
            j = i + alen;
            if(document.cookie.substring(i, j) == arg){
                return getCookieVal(j);
            }
            i = document.cookie.indexOf(" ", i) + 1;
            if(i === 0){
                break;
            }
        }
        return null;
    };

    if (window.performance && window.performance.now) {
        Kwf.Utils.BenchmarkBox.now = function() {
            return window.performance.now();
        };
    } else {
        var dateNow = Date.now;
        if (!dateNow) dateNow = function() { return new Date().getTime()};
        var nowOffset = dateNow();
        if (window.performance && window.performance.timing && window.performance.timing.navigationStart){
            nowOffset = window.performance.timing.navigationStart
        }
        Kwf.Utils.BenchmarkBox.now = function now(){
            return dateNow() - nowOffset;
        };
    }
    Kwf.Utils.BenchmarkBox._counters = {};
    Kwf.Utils.BenchmarkBox.count = function(name) {
        if (!Kwf.Utils.BenchmarkBox._counters[name]) Kwf.Utils.BenchmarkBox._counters[name] = 0;
        Kwf.Utils.BenchmarkBox._counters[name]++;
    };
    Kwf.Utils.BenchmarkBox._timers = {};
    Kwf.Utils.BenchmarkBox.time = function(name, duration) {
        if (!Kwf.Utils.BenchmarkBox._timers[name]) Kwf.Utils.BenchmarkBox._timers[name] = 0;
        Kwf.Utils.BenchmarkBox._timers[name] += duration;
        Kwf.Utils.BenchmarkBox.count(name);
    };
    Kwf.Utils.BenchmarkBox._subTimers = {};
    Kwf.Utils.BenchmarkBox.subTime = function(name, subName, duration) {
        if (!Kwf.Utils.BenchmarkBox._subTimers[name]) {
            Kwf.Utils.BenchmarkBox._subTimers[name] = {};
        }
        if (!Kwf.Utils.BenchmarkBox._subTimers[name][subName]) {
            Kwf.Utils.BenchmarkBox._subTimers[name][subName] = {
                count: 0,
                duration: 0
            };
        }
        Kwf.Utils.BenchmarkBox._subTimers[name][subName].count++;
        Kwf.Utils.BenchmarkBox._subTimers[name][subName].duration += duration;
    };
    Kwf.Utils.BenchmarkBox.initBox = function(el) {
        if (el.dom.initDone) return;
        el.dom.initDone = true;
        var container = Ext.getBody().child('.benchmarkContainer');
        if (!container) {
            container = Ext.getBody().createChild({
                cls: 'benchmarkContainer'
            });
        }
        container.appendChild(el);

        var benchmarkType = el.dom.getAttribute('data-benchmark-type');
        if (getCookie('benchmarkBox-'+benchmarkType)=='1') {
            el.addClass('visible');
        }
        var showLink = el.insertFirst({
            tag: 'a',
            href: '#',
            cls: 'showContent',
            html: '['+benchmarkType+']'
        });
        showLink.on('click', function(ev) {
            ev.stopEvent();
            var el = Ext.get(this);
            if (!el.hasClass('visible')) {
                el.addClass('visible');
                setCookie('benchmarkBox-'+benchmarkType, '1');
            } else {
                el.removeClass('visible');
                setCookie('benchmarkBox-'+benchmarkType, '0');
            }
        }, el.dom);
    };
    Kwf.Utils.BenchmarkBox.create = function(options) {
        if (!benchmarkEnabled) return;
        var html = '';
        if (Kwf.Utils.BenchmarkBox._timers.time) {
            html += (Math.round(Kwf.Utils.BenchmarkBox._timers.time*100)/100) + 'ms<br />';
        }
        for (var i in Kwf.Utils.BenchmarkBox._counters) {
            if (i == 'time') continue;
            var c = Kwf.Utils.BenchmarkBox._counters[i];
            var t = Kwf.Utils.BenchmarkBox._timers[i];
            if (t) {
                c += ' ('+(Math.round(t*100)/100) + 'ms)';
            }
            html += i+': '+c+'<br />';
        }
        for (var name in Kwf.Utils.BenchmarkBox._subTimers) {
            var st = Kwf.Utils.BenchmarkBox._subTimers[name];
            var subArray = [];
            for (var subName in st) {
                subArray.push({
                    count: st[subName].count,
                    duration: st[subName].duration,
                    name: subName
                });
            }
            html += name+'<br />';
            subArray.sort(function(i, j) {
                return j.duration-i.duration;
            });
            subArray = subArray.slice(0, 5); //only top 5
            subArray.each(function(i) {
                html += '&nbsp;&nbsp;'+i.name+' '+i.count+' ('+(Math.round(i.duration*100)/100)+'ms)<br />';
            });
        }
        Kwf.Utils.BenchmarkBox._counters = {};
        Kwf.Utils.BenchmarkBox._timers = {};
        Kwf.Utils.BenchmarkBox._subTimers = {};
        html = '<div class="benchmarkBoxContent">'+html+'</div>';
        var el = Ext.getBody().createChild({
            cls: 'benchmarkBox',
            'data-benchmark-type': options.type,
            html: html
        });
        Kwf.Utils.BenchmarkBox.initBox(el);
    };
    Ext.onReady(function() {
        Ext.select('.benchmarkBox').each(function(el) {
            Kwf.Utils.BenchmarkBox.initBox(el);
        });
    }, this, { delay: 110 });
})();
