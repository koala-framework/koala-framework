var override = function(origclass, overrides) {
    if (overrides) {
        var p = origclass.prototype;
        for(var method in overrides){
            p[method] = overrides[method];
        }
    }
};

//based on Ext2.extend without Ext2 dependency
var extend = function(){
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
            override(sb, o);
        };
        sbp.override = io;
        override(sb, overrides);
        sb.extend = function(o){extend(sb, o);};
        return sb;
    };
}();

module.exports = extend;
