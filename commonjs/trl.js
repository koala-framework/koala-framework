function trl (text, values){
  if (values == null){
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

function trlp (single, plural, values){
  if (values == null){
           return '';
    } else {
      if (typeof(values) == 'string' || typeof(values) == 'number') {
        var temp = values;
        values = new Array();
        values.push(temp);
      }

      if (values[0] == 1){
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
       }
}

module.exports = {
    trl: trl,
    trlc: trl,
    trlp: trlp,
    trlcp: trlp,
    trlKwf: trl,
    trlcKwf: trl,
    trlpKwf: trlp,
    trlcpKwf: trlp
};
