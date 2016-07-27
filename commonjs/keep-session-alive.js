var $ = require('jQuery');

var sendKeepAlive = function () {
    $.ajax({
        url: '/kwf/user/login/json-keep-alive',
        dataType: 'json'
    });
    setTimeout(sendKeepAlive, 1000 * 60 * 5);
};

var keepAliveActivated = false;
var activateKeepAlive = function () {
    if (keepAliveActivated) return;
    keepAliveActivated = true;
    setTimeout(sendKeepAlive, 1000 * 60 * 5);
};
module.exports = activateKeepAlive;
