Vps.Connection = Ext.extend(Ext.data.Connection, {
    _progressData    : { },

    /**
     * Options:
     * - mask (true für body, sonst element)
     * - maskText (default Loading...)
     * - progress
     * - progressTitle (default Progress)
     */
    request: function(options)
    {

        Vps.requestSentSinceLastKeepAlive = true;
        Vps.Connection.runningRequests++;

        if (options.mask) {
            if (options.mask instanceof Ext.Element) {
                options.mask.mask(options.maskText || trlVps('Loading...'));
            } else {
                if (Vps.Connection.masks == 0) {
                    if (Ext.get('loading')) {
                        Ext.getBody().mask();
                    } else {
                        Ext.getBody().mask(options.maskText || trlVps('Loading...'));
                    }
                }
                Vps.Connection.masks++;
            }
        }

        if (options.url.match(/[\/a-zA-Z0-9]*\/json[a-zA-Z0-9\-]+(\/|\?|)/)) {
            options.vpsCallback = {
                success: options.success,
                failure: options.failure,
                callback: options.callback,
                scope: options.scope
            };
            options.success = this.vpsJsonSuccess;
            options.failure = this.vpsJsonFailure;
            options.callback = this.vpsCallback;
            options.scope = this;
        } else {
            options.vpsCallback = {
                success: options.success,
                failure: options.failure,
                callback: options.callback,
                scope: options.scope
            };
            options.success = this.vpsNoJsonSuccess;
            options.failure = this.vpsNoJsonFailure;
            options.callback = this.vpsCallback;
            options.scope = this;
        }
        if (!options.params) options.params = {};
        options.params.application_max_assets_mtime = Vps.application.maxAssetsMTime;
        if (!options.url.match(':\/\/')) {
            //absolute url incl. http:// erstellen
            //wird benötigt wenn fkt über mozrepl aufgerufen wird
            var u = location.protocol + '/'+'/' + location.host;
            if (options.url.substr(0, 1) == '/') {
                options.url = u + options.url;
            } else {
                options.url = u + '/' + options.url;
            }
        }

        if (options.progress) {
            var progressNum = Math.floor(Math.random() * 1000000000) + 1;
            options.params.progressNum = progressNum;
        }

        var ret = Vps.Connection.superclass.request.call(this, options);

        if (options.progress) {
            this._showProgress(options);
        }

        return ret;
    },

    _showProgress: function(options)
    {
        var progressNum = options.params.progressNum;

        this._progressData[progressNum] = {
            progressBar: this._createProgressDialog({
                title: options.progressTitle || trlVps('Progress'),
                transId: this.transId,
                requestOptions: options
            }),
            breakStatusRequests: false
        };
        this._progressData[progressNum].progressBar.updateProgress(0, '0%', '');

        this._doProgressStatusRequest.defer(1500, this, [ progressNum ]);
    },

    _doProgressStatusRequest: function(progressNum)
    {
        this.request({
            url: '/vps/json-progress-status',
            params: { progressNum: progressNum },
            success: function(response, options, r) {
                var progressNum = options.params.progressNum;
                if (!this._progressData[progressNum]) return;

                if (typeof r.finished != 'undefined') {
                    if (r.finished) {
                        this._progressData[progressNum].progressBar.updateProgress(
                            1, '100%', trlVps('Finished')
                        );
                        return;
                    }

                    this._progressData[progressNum].progressBar.updateProgress(
                        r.percent / 100,
                        Math.floor(r.percent)+'%',
                        r.text ? r.text : ''
                    );
                }

                if (!this._progressData[progressNum].breakStatusRequests) {
                    // recursing
                    this._doProgressStatusRequest.defer(500, this, [ progressNum ]);
                }
            },
            scope: this
        });
    },

    _createProgressDialog: function(cfg)
    {
        var progressBar = new Ext.ProgressBar({
            text:'0%',
            animate: true
        });
        cfg = Ext.applyIf(cfg, {
            title: trlVps('Progress'),
            autoCreate : true,
            resizable:false,
            constrain:true,
            constrainHeader:true,
            minimizable : false,
            maximizable : false,
            stateful: false,
            modal: true,
            shim:true,
            buttonAlign:"center",
            width:400,
            plain:true,
            footer:true,
            closable:false
        });;
        var dlg = new Ext.Window(cfg);

        if (typeof cfg.showCancel == 'undefined' || cfg.showCancel) {
            dlg.addButton(
                { text: trlVps('Cancel') },
                (function(dialog) {
                    Ext.Ajax.abort(dialog.transId);

                    var responseObject = Ext.lib.Ajax.createExceptionObject(
                        dialog.transId, null, true
                    );
                    Ext.callback(
                        dialog.requestOptions.vpsCallback.failure,
                        dialog.requestOptions.vpsCallback.scope,
                        [responseObject, dialog.requestOptions]
                    );
                    dialog.requestOptions.callback.call(
                        this,
                        dialog.requestOptions,
                        false,
                        responseObject
                    );
                }).createDelegate(this, [ dlg ])
            );
        }

        dlg.render(document.body);
        dlg.myEls = { };
        dlg.myEls.bodyEl = dlg.body.createChild({
            html:'<div class="vps-progress-content"><span class="vps-progress-text"></span><br /></div>'
        });
        dlg.myEls.bodyEl.addClass('vps-progress-window');
        dlg.myEls.msgEl = Ext.get(dlg.myEls.bodyEl.dom.childNodes[0].firstChild);

        dlg.progressBar = new Ext.ProgressBar({
            renderTo:dlg.myEls.bodyEl,
            text: '0%',
            animate: true
        });
        dlg.myEls.bodyEl.createChild({cls:'x-clear'});

        dlg.updateProgress = function(num, progressBarText, text)
        {
            this.progressBar.updateProgress(num, progressBarText, true);
            this.myEls.msgEl.update(text || '&#160;');
        };

        dlg.show();
        return dlg;
    },

    repeatRequest: function(options) {
        Vps.Connection.runningRequests++;
        delete options.vpsIsSuccess;
        Vps.Connection.superclass.request.call(this, options);
        if (options.progress) {
            this._showProgress(options);
        }
    },
    vpsJsonSuccess: function(response, options)
    {
        if (!options.ignoreErrors) {
            options.vpsIsSuccess = false;
            options.vpsLogin = false;

            var errorMsg = false;

            var encParams;
            if (typeof options.params == "string") {
                encParams = options.params;
            } else {
                encParams = Ext.urlEncode(options.params);
            }
            try {
                if (!response.responseText) {
                    errorMsg = 'response is empty';
                } else {
                    var r = Ext.decode(response.responseText);
                }
            } catch(e) {
                errorMsg = e.toString()+': <br />'+response.responseText;
                var errorMsgTitle = 'Javascript Parse Exception';
            }
            if (Vps.Debug.querylog && r && r.requestNum) {
                var rm = location.protocol + '/'+'/' + location.host;
                var url = options.url;
                if (url.substr(0, rm.length) == rm) {
                    url = url.substr(rm.length);
                }
                var data = [[new Date(), url, encParams, r.requestNum]];
                Vps.Debug.requestsStore.loadData(data, true);
            }
            if (!errorMsg && r.exception) {
                var p;
                if (typeof options.params == "string") {
                    p = options.params;
                } else {
                    p = Ext.urlEncode(options.params);
                }
                errorMsg = '<pre>'+r.exception+'</pre>';
                var errorMsgTitle = 'PHP Exception';
            }
            if (errorMsg && !options.ignoreErrors) {
                errorMsg = '<a href="'+options.url+'?'+encParams+'">request-url</a><br />' + errorMsg;
                var sendMail = !r || !r.exception;
                if (options.errorText) {
                    errorText = options.errorText;
                } else {
                    errorText = null;
                }
                Vps.handleError({
                    url: options.url,
                    message: errorMsg,
                    title: errorMsgTitle,
                    mail: sendMail,
                    errorText: errorText,
                    checkRetry: false,
                    retry: function() {
                        this.connection.repeatRequest(this.options);
                    },
                    abort: function() {
                        Ext.callback(this.options.vpsCallback.failure, this.options.vpsCallback.scope, [this.response, this.options]);
                    },
                    scope: { connection: this, options: options, response: response }
                });
                return;
            }

            if (!r.success && !options.ignoreErrors) {
                if (r.wrongversion) {
                    Ext.Msg.alert(trlVps('Error - wrong version'),
                    trlVps('Because of an application update the application has to be reloaded.'),
                    function(){
                        location.reload();
                    });
                    Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
                    return;
                }
                if (r.login) {
                    options.vpsLogin = true;
                    var dlg = new Vps.User.Login.Dialog({
                        message: r.message,
                        success: function() {
                            //redo action...
                            this.repeatRequest(options);
                        },
                        scope: this
                    });
                    Ext.getBody().unmask();
                    dlg.showLogin();
                    return;
                }
                if (r.error) {
                    Ext.Msg.alert(trlVps('Error'), r.error);
                } else {
                    Ext.Msg.alert(trlVps('Error'), trlVps("A Server failure occured."));
                }
                Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
                return;
            }
            options.vpsIsSuccess = true;
            if (options.vpsCallback.success) {
                options.vpsCallback.success.call(options.vpsCallback.scope, response, options, r);
            }
        };
    },
    vpsNoJsonSuccess: function(response, options)
    {
        options.vpsIsSuccess = true;
        if (options.vpsCallback.success) {
            options.vpsCallback.success.call(options.vpsCallback.scope, response, options);
        }
    },
    vpsNoJsonFailure: function(response, options)
    {
        options.vpsIsSuccess = false;
        if (options.vpsCallback.failure) {
            options.vpsCallback.failure.call(options.vpsCallback.scope, response, options);
        }
    },
    vpsJsonFailure: function(response, options)
    {
        if (!options.ignoreErrors) {
            options.vpsIsSuccess = false;

            errorMsgTitle = trlVps('Error');
            if (options.errorText) {
                errorText = options.errorText;
                errorMsg = options.errorText;
            } else {
               errorMsg = trlVps("A connection problem occured.");
               errorText = null;
            }
            if (!options.ignoreErrors) {
                Vps.handleError({
                    url: options.url,
                    message: errorMsg,
                    title: errorMsgTitle,
                    errorText: errorText,
                    mail: false,
                    checkRetry: true,
                    retry: function() {
                        this.repeatRequest(options);
                    },
                    abort: function() {
                        Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
                    },
                    scope: this
                });
                Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
            }
            return;
        }
    },

    vpsCallback: function(options, success, response)
    {
        //wenn login-fenster angezeigt wird keinen callback aufrufen - weil der request
        //wird ja erneut gesendet und da dann der callback aufgerufen.
        if (options.vpsLogin) return;

        if (options.mask) {
            if (options.mask instanceof Ext.Element) {
                options.mask.unmask();
            } else {
                Vps.Connection.masks--;
                if (Vps.Connection.masks == 0) {
                    Ext.getBody().unmask();
                    if (Ext.get('loading')) {
                        Ext.get('loading').fadeOut({remove: true});
                    }
                }
            }
        }

        if (options.progress) {
            this._progressData[options.params.progressNum].progressBar.hide();
            delete this._progressData[options.params.progressNum];
        }

        if(success && !options.vpsIsSuccess) {
            success = false;
        }
        Ext.callback(options.vpsCallback.callback, options.vpsCallback.scope, [options, success, response]);
        Vps.Connection.runningRequests--;
    }
});
Vps.Connection.masks = 0; //static var that hols number of masked requests
Vps.Connection.runningRequests = 0;

Ext.Ajax = new Vps.Connection({
    /**
     * The timeout in milliseconds to be used for requests. (defaults
     * to 30000)
     * @type Number
     * @property  timeout
     */
    autoAbort : false,

    /**
     * Serialize the passed form into a url encoded string
     * @return {String}
     */
    serializeForm : function(form){
        return Ext.lib.Ajax.serializeForm(form);
    }
});

