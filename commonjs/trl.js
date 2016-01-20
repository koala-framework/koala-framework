var prefix = 'kwfUp-';
if (prefix) prefix = prefix.substr(0, prefix.length-1);
var trlData = prefix ? window[prefix]._kwfTrlData : window._kwfTrlData;

function _kwfTrl(key, text, values)
{
    if (trlData[key]) text = trlData[key];

    if (values == null) {
        return text;
    } else {
        if (typeof(values) == 'string' || typeof(values) == 'number') {
            var temp = values;
            values = new Array();
            values.push(temp);
        }
        var cnt = 0;
        values.forEach(function(value) {
            text = text.replace(new RegExp('\\{('+cnt+')\\}', 'g'), value);
            cnt++;
        });
        return text;
    }
}

function _kwfTrlp(key, text, plural, values)
{
    var prefix = 'kwfUp-';
    if (prefix) prefix = prefix.substr(0, -1);
    var trlData = prefix ? window[prefix]._trlData : window._trlData;

    if (trlData[key]) text = trlData[key];
    if (trlData[key+'.plural']) plural = trlData[key+'.plural'];
    if (values == null) {
        return '';
    } else {
        if (typeof(values) == 'string' || typeof(values) == 'number') {
            var temp = values;
            values = new Array();
            values.push(temp);
        }

        if (values[0] == 1) {
            text = single;
        } else {
            text = plural;
        }
        var cnt = 0;
        values.forEach(function(value) {
            text = text.replace(new RegExp('\\{('+cnt+')\\}', 'g'), value);
            cnt++;
        });
        return text;
    }}


module.exports = {
    _kwfTrl: _kwfTrl,
    _kwfTrlp: _kwfTrlp
};
