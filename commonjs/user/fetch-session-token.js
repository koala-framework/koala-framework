'use strict';

var $ = require('jQuery');

function fetchSessionToken(cb, scope) {
    var dfd = $.Deferred();

    if (window.Kwf && window.Kwf.sessionToken) {
        dfd.resolve(window.Kwf.sessionToken);
        if (cb) cb.call(scope || window, window.Kwf.sessionToken);
    } else {
        $.ajax({
            method: "POST",
            url: "/kwf/user/login/json-get-session-token",
            dataType: "json"
        }).done(function(data) {
            if (!window.Kwf) window.Kwf = {};
            window.Kwf.sessionToken = data.sessionToken;
            dfd.resolve(data.sessionToken);
            if (cb) cb.call(scope || window, data.sessionToken);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            dfd.reject(jqXHR, textStatus, errorThrown);
        });
    }
    return dfd.promise();
};

module.exports = fetchSessionToken;
