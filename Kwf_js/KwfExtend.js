if(!Kwf) Kwf = {};

//very, very simple class system, based on Ext2
//used when no ext2 is available

Kwf.namespace = function() {
    var a=arguments, o=null, i, j, d, rt;
    for (i=0; i<a.length; ++i) {
        d=a[i].split(".");
        rt = d[0];
        eval('if (typeof ' + rt + ' == "undefined"){' + rt + ' = {};} o = ' + rt + ';');
        for (j=1; j<d.length; ++j) {
            o[d[j]]=o[d[j]] || {};
            o=o[d[j]];
        }
    }
}

//based on Ext2.extend without Ext2 dependency
Kwf.extend = function(){
    // inline overrides
    var io = function(o){
        for(var m in o){
            this[m] = o[m];
        }
    };
    var oc = Object.prototype.constructor;

    return function(sb, sp, overrides) {
        if (typeof sp == 'object') {
            overrides = sp;
            sp = sb;
            sb = overrides.constructor != oc ? overrides.constructor : function(){sp.apply(this, arguments);};
        }
        var F = function(){}, sbp, spp = sp.prototype;
        F.prototype = spp;
        sbp = sb.prototype = new F();
        sbp.constructor=sb;
        sb.superclass=spp;
        if (spp.constructor == oc) {
            spp.constructor=sp;
        }
        sb.override = function(o) {
            Kwf.override(sb, o);
        };
        sbp.override = io;
        Kwf.override(sb, overrides);
        sb.extend = function(o){Kwf.extend(sb, o);};
        return sb;
    };
}();

Kwf.override = function(origclass, overrides) {
    if (overrides) {
        var p = origclass.prototype;
        for(var method in overrides){
            p[method] = overrides[method];
        }
    }
};
