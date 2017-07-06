'use strict';

var $ = require('jQuery');
var sessionToken;

function fetchSessionToken(cb, scope) {
    var dfd = $.Deferred();

    if (sessionToken) {
        dfd.resolve(sessionToken);
        if (cb) cb.call(scope || window, sessionToken);
    } else {
        $.ajax({
            method: "POST",
            url: "/kwf/user/login/json-get-session-token",
            dataType: "json"
        }).done(function(data) {
            sessionToken = data.sessionToken;
            dfd.resolve(data.sessionToken);
            if (cb) cb.call(scope || window, data.sessionToken);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            dfd.reject(jqXHR, textStatus, errorThrown);
        });
    }
    return dfd.promise();
};

module.exports = fetchSessionToken;
