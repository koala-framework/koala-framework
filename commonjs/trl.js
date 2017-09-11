function getTrlData(source, i)
{
    var ret = null;
    if (!window['kwfUp-kwfTrlData']) return ret;
    window['kwfUp-kwfTrlData'].forEach(function(d) {
        if (d.source == source && d.data[i]) {
            ret = d.data[i];
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


var trl = function(source, i, values) {
    var ret = getTrlData(source, i) || i;
    return replaceValues(ret, values);
};
var trlc = function(source, context, i, values) {
    var ret = getTrlData(source, context+'__'+i) || i;
    return replaceValues(ret, values);
};
var trlp = function(source, sg, pl, values) {
    var ret = getTrlData(source, sg + '--' + pl) || [sg, pl];
    var cnt = values;
    if (cnt instanceof Array) cnt = cnt[0]
    if (cnt == 1) {
        ret = ret[0];
    } else {
        ret = ret[1];
    }
    return replaceValues(ret, values);
};
var trlcp = function(source, context, sg, pl, values) {
    var ret = getTrlData(source, context + '__' + sg + '--' + pl) || [sg, pl];
    var cnt = values;
    if (cnt instanceof Array) cnt = cnt[0]
    if (cnt == 1) {
        ret = ret[0];
    } else {
        ret = ret[1];
    }
    return replaceValues(ret, values);
};

var exports = {
    trl: function(i, values) {
        return trl('web', i, values);
    },
    trlc: function(context, i, values) {
        return trlc('web', context, i, values);
    },
    trlp: function(sg, pl, values) {
        return trlp('web', sg, pl, values);
    },
    trlcp: function(context, sg, pl, values) {
        return trlcp('web', context, sg, pl, values);
    },
    trlKwf: function(i, values) {
        return trl('kwf', i, values);
    },
    trlcKwf: function(context, i, values) {
        return trlc('kwf', context, i, values);
    },
    trlpKwf: function(sg, pl, values) {
        return trlp('kwf', sg, pl, values);
    },
    trlcpKwf: function(context, sg, pl, values) {
        return trlcp('kwf', context, sg, pl, values);
    }
};

module.exports = exports;
