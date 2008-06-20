function trl (text, values){
	if (values == null){
           return text;
    } else {
			var cnt = 0;
            values.each(function(value) {
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
			if (values[0] == 1){
				text = single;
			} else {
				text = plural;
			}
			var cnt = 0;
            values.each(function(value) {
				text = text.replace(new RegExp('\\{('+cnt+')\\}', 'g'), value);
				cnt++;
			});
           return text;
       }
}
