Kwf.Utils.Upload = {
    supportsHtml5Upload: function()
    {
        if (XMLHttpRequest) {
            var xhr = new XMLHttpRequest();
            if (xhr.upload) {
                return true;
            }
        }
        return false;
    },
    uploadFile: function(config)
    {
        var file = config.file;
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(e) {
            if (e.lengthComputable && config.progress) {
                config.progress.call(config.scope, e);
            }
        }, false);
        xhr.onreadystatechange = function(e) {
            if (xhr.readyState == 4) {
                var errorMsg = false;
                if (xhr.status != 200) {
                        errorMsg = xhr.responseText;
                } else if (!xhr.responseText) {
                    errorMsg = 'response is empty';
                } else {
                    try {
                        var r = Ext.decode(xhr.responseText);
                    } catch(e) {
                        errorMsg = e.toString()+': <br />'+xhr.responseText;
                    }
                    if (!errorMsg && r.exception) {
                        errorMsg = '<pre>'+r.exception+'</pre>';
                    }
                }
                if (errorMsg) {
                    var sendMail = !r || !r.exception;
                    Kwf.handleError({
                        url: '/kwf/media/upload/json-upload',
                        message: errorMsg,
                        title: trlKwf('Upload Error'),
                        mail: sendMail,
                        checkRetry: false
                    });
                    if (config.failure) {
                        config.failure.call(config.scope, r);
                    }
                    return;
                }

                if (!r.success) {
                    if (r.error) {
                        Ext.Msg.alert(trlKwf('Error'), r.error);
                    } else {
                        Ext.Msg.alert(trlKwf('Error'), trlKwf("A Server failure occured."));
                    }
                    if (config.failure) {
                        config.failure.call(config.scope, r);
                    }
                    return;
                }

                if (config.success) {
                    config.success.call(config.scope, r, config);
                }
            }
        }
        var url = '/kwf/media/upload/json-upload';
        if (Kwf.sessionToken) url += '?kwfSessionToken='+Kwf.sessionToken;
        xhr.open('POST', url);
        xhr.setRequestHeader('X-Upload-Name', file.name);
        xhr.setRequestHeader('X-Upload-Size', file.size);
        xhr.setRequestHeader('X-Upload-Type', file.type);
        xhr.setRequestHeader('X-Upload-MaxResolution', config.maxResolution);
        xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
        xhr.send(file);

        return xhr;
    }
};
