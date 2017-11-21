require('./box.scss');
var $ = require('jquery');

var BenchmarkBox = {};
module.exports = BenchmarkBox;

var benchmarkEnabled = false;
try {
    benchmarkEnabled = typeof Kwf !='undefined' && Kwf.Debug && Kwf.Debug.benchmark;
} catch(e) {}
if (!benchmarkEnabled) {
    benchmarkEnabled = location.search.match(/[\?&]KWF_BENCHMARK/);
}
if (!benchmarkEnabled) {

    //noop implementation if not enabled
    BenchmarkBox.now = function() { return 0; };
    BenchmarkBox.count = function() { };
    BenchmarkBox.time = function() { };
    BenchmarkBox.subTime = function() { };
    BenchmarkBox.create = function() { };

} else {

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
        BenchmarkBox.now = function() {
            return window.performance.now();
        };
    } else {
        var dateNow = Date.now;
        if (!dateNow) dateNow = function() { return new Date().getTime()};
        var nowOffset = dateNow();
        if (window.performance && window.performance.timing && window.performance.timing.navigationStart){
            nowOffset = window.performance.timing.navigationStart
        }
        BenchmarkBox.now = function now(){
            return dateNow() - nowOffset;
        };
    }
    BenchmarkBox._counters = {};
    BenchmarkBox.count = function(name) {
        if (!BenchmarkBox._counters[name]) BenchmarkBox._counters[name] = 0;
        BenchmarkBox._counters[name]++;
    };
    BenchmarkBox._timers = {};
    BenchmarkBox.time = function(name, duration) {
        if (!BenchmarkBox._timers[name]) BenchmarkBox._timers[name] = 0;
        BenchmarkBox._timers[name] += duration;
        BenchmarkBox.count(name);
    };
    BenchmarkBox._subTimers = {};
    BenchmarkBox.subTime = function(name, subName, duration) {
        if (!BenchmarkBox._subTimers[name]) {
            BenchmarkBox._subTimers[name] = {};
        }
        if (!BenchmarkBox._subTimers[name][subName]) {
            BenchmarkBox._subTimers[name][subName] = {
                count: 0,
                duration: 0
            };
        }
        BenchmarkBox._subTimers[name][subName].count++;
        BenchmarkBox._subTimers[name][subName].duration += duration;
    };
    BenchmarkBox.initBox = function(el) {
        if (el.jquery) el = el.get(0);
        if (el.initDone) return;
        el.initDone = true;
        var container = $('.kwfUp-benchmarkContainer');
        if (!container.length) {
            container = $('<div class="kwfUp-benchmarkContainer"></div>');
            $('body').append($(container));
        }
        container.append($(el));

        var benchmarkType = $(el).data('benchmarkType');
        if (getCookie('kwfUp-benchmarkBox-'+benchmarkType)=='1') {
            $(el).addClass('visible');
        }
        var showLink = $('<a href="#" class="showContent">['+benchmarkType+']</a>');
        $(showLink).prependTo($(el));

        showLink.on('click', function(ev) {
            ev.preventDefault();
            var el = $(this);
            if (!el.parent().hasClass('visible')) {
                el.parent().addClass('visible');
                setCookie('kwfUp-benchmarkBox-'+benchmarkType, '1');
            } else {
                el.parent().removeClass('visible');
                setCookie('kwfUp-benchmarkBox-'+benchmarkType, '0');
            }
        });
    };
    BenchmarkBox.create = function(options) {
        if (!benchmarkEnabled) return;
        var html = '';
        if (BenchmarkBox._timers.time) {
            html += (Math.round(BenchmarkBox._timers.time*100)/100) + 'ms<br />';
        }
        for (var i in BenchmarkBox._counters) {
            if (i == 'time') continue;
            var c = BenchmarkBox._counters[i];
            var t = BenchmarkBox._timers[i];
            if (t) {
                c += ' ('+(Math.round(t*100)/100) + 'ms)';
            }
            html += i+': '+c+'<br />';
        }
        for (var name in BenchmarkBox._subTimers) {
            var st = BenchmarkBox._subTimers[name];
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
            $.each(subArray, function() {
                html += '&nbsp;&nbsp;'+this.name+' '+this.count+' ('+(Math.round(this.duration*100)/100)+'ms)<br />';
            });
        }
        BenchmarkBox._counters = {};
        BenchmarkBox._timers = {};
        BenchmarkBox._subTimers = {};
        html = '<div class="kwfUp-benchmarkBoxContent">'+html+'</div>';
        html = '<div class="kwfUp-benchmarkBox" data-benchmark-type="'+options.type+'">'+html+'</div>';
        var el = $(html);
        $('body').append(el);
        BenchmarkBox.initBox(el);
    };
    $(function() {
        setTimeout(function() {
            $('body').find('.kwfUp-benchmarkBox').each(function(i, el) {
                BenchmarkBox.initBox($(el));
            });
        }, 10);
    });
}
