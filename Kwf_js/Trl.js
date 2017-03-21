var t = require('kwf/commonjs/trl');
var methods = ['trl', 'trlc', 'trlcp', 'trlp', 'trlKwf', 'trlcKwf', 'trlcpKwf', 'trlpKwf'];
methods.forEach(function(m) {
    window[m] = t[m];
});
