function getTrlData(i)
{
    var ret = null;
    if (!window.kwfTrlData) return ret;
    window.kwfTrlData.forEach(function(d) {
        if (d[i]) {
            ret = d[i];
            return false;
        }
    });
    return ret;
}
function replaceValues(text, values)
{
    if (!values) return text;
    if (typeof(values) == 'string' || typeof(values) == 'number') {
        values = [values];
    }
    var cnt = 0;
    values.forEach(function(value) {
        text = text.replace(new RegExp('\\{('+cnt+')\\}', 'g'), value);
        cnt++;
    });
    return text;
}
var exports = {
    trl: function(i, values) {
        var ret = getTrlData(i) || i;
        return replaceValues(ret, values);
    },
    trlc: function(context, i, values) {
        var ret = getTrlData(context+'__'+i) || i;
        return replaceValues(ret, values);
    },
    trlp: function(sg, pl, values) {
        var ret = getTrlData(sg + '--' + pl) || [sg, pl];
        var cnt = values;
        if (cnt instanceof Array) cnt = cnt[0]
        if (cnt == 1) {
            ret = ret[0];
        } else {
            ret = ret[1];
        }
        return replaceValues(ret, values);
    },
    trlcp: function(context, sg, pl, values) {
        var ret = getTrlData(context + '__' + sg + '--' + pl) || [sg, pl];
        var cnt = values;
        if (cnt instanceof Array) cnt = cnt[0]
        if (cnt == 1) {
            ret = ret[0];
        } else {
            ret = ret[1];
        }
        return replaceValues(ret, values);
    }
};
exports.trlKwf = exports.trl;
exports.trlcKwf = exports.trlc;
exports.trlpKwf = exports.trlp;
exports.trlcpKwf = exports.trlcp;

module.exports = exports;
