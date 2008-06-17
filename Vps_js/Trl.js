function trl (text, values){
	if (values == null){
           return text;
    } else {
			var cnt = 0;
            values.each(function(value) {
				if (cnt == 0) text = text.replace(/\{(0)\}/g, value);
				if (cnt == 1) text = text.replace(/\{(1)\}/g, value);
				if (cnt == 2) text = text.replace(/\{(2)\}/g, value);
				if (cnt == 3) text = text.replace(/\{(3)\}/g, value);
				if (cnt == 4) text = text.replace(/\{(4)\}/g, value);
				if (cnt == 5) text = text.replace(/\{(5)\}/g, value);
				if (cnt == 6) text = text.replace(/\{(6)\}/g, value);
				if (cnt == 7) text = text.replace(/\{(7)\}/g, value);
				if (cnt == 8) text = text.replace(/\{(8)\}/g, value);
				if (cnt == 9) text = text.replace(/\{(9)\}/g, value);
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
				if (cnt == 0) text = text.replace(/\{(0)\}/g, value);
				if (cnt == 1) text = text.replace(/\{(1)\}/g, value);
				if (cnt == 2) text = text.replace(/\{(2)\}/g, value);
				if (cnt == 3) text = text.replace(/\{(3)\}/g, value);
				if (cnt == 4) text = text.replace(/\{(4)\}/g, value);
				if (cnt == 5) text = text.replace(/\{(5)\}/g, value);
				if (cnt == 6) text = text.replace(/\{(6)\}/g, value);
				if (cnt == 7) text = text.replace(/\{(7)\}/g, value);
				if (cnt == 8) text = text.replace(/\{(8)\}/g, value);
				if (cnt == 9) text = text.replace(/\{(9)\}/g, value);
				cnt++;
			});
           return text;
       }
}
