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
				cnt++;
			});
           return text;
       }
}
/*
alert (trlVps('VPS_TEST'));
alert (trlcVps('jajaja', 'bambambam {0} {1}', [1, 'du']));
alert (trlpVps('ein Fehler gefunden', '{0} Fehler gefunden {1} {2}', [2, 'juhuu', 'hallo']));
alert (trlcpVps('meinContext', 'eine Taube sitzt am Dach', '{0} Tauben sitzen am Dach {1} {2}', [3, 'hallo', 'oh yeah']));
alert (trlVps('dingdong {0} {1}', ['zeas', 'du']) );*/
    /*function format (format){
        var args = Array.prototype.slice.call(arguments, 1);
        return format.replace(/\{(\d+)\}/g, function(m, i){
            return args[i];
        });
    }*/