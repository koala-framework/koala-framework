Ext2.fly(window).on('beforeunload', function() {
    Kwf.Connection.isLeavingPage = true;
});
Kwf.Connection = Ext2.extend(Ext2.data.Connection, {
    _progressData    : { },

    /**
     * Options:
     * - mask (true für body, sonst element)
     * - maskText (default Loading...)
     * - progress
     * - progressTitle (default Progress)
     * - ignoreErrors (don't show error messages, but call failure callback)
     */
    request: function(options)
    {

        Kwf.requestSentSinceLastKeepAlive = true;
        Kwf.Connection.runningRequests++;

        if (options.mask) {
            if (options.mask instanceof Ext2.Element) {
                options.mask.mask(options.maskText || trlKwf('Loading...'));
            } else {
                if (Kwf.Connection.masks == 0) {
                    if (Ext2.get('loading')) {
                        Ext2.getBody().mask();
                    } else {
                        Ext2.getBody().mask(options.maskText || trlKwf('Loading...'));
                    }
                }
                Kwf.Connection.masks++;
            }
        }

        if (options.url.match(/[\/a-zA-Z0-9]*\/json[a-zA-Z0-9\-]+(\/|\?|)/)) {
            options.kwfCallback = {
                success: options.success,
                failure: options.failure,
                callback: options.callback,
                scope: options.scope
            };
            options.success = this.kwfJsonSuccess;
            options.failure = this.kwfJsonFailure;
            options.callback = this.kwfCallback;
            options.scope = this;
        } else {
            options.kwfCallback = {
                success: options.success,
                failure: options.failure,
                callback: options.callback,
                scope: options.scope
            };
            options.success = this.kwfNoJsonSuccess;
            options.failure = this.kwfNoJsonFailure;
            options.callback = this.kwfCallback;
            options.scope = this;
        }
        if (!options.params) options.params = {};
        options.params.applicationAssetsVersion = Kwf.application.assetsVersion;
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

        var ret = Kwf.Connection.superclass.request.call(this, options);

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
                title: options.progressTitle || trlKwf('Progress'),
                transId: this.transId,
                requestOptions: options,
                showCancel: options.showCancel
            }),
            breakStatusRequests: false
        };
        this._progressData[progressNum].progressBar.updateProgress(0, '0%', '');

        this._doProgressStatusRequest.defer(1500, this, [ progressNum ]);
    },

    _doProgressStatusRequest: function(progressNum)
    {
        this.request({
            url: '/kwf/json-progress-status',
            params: { progressNum: progressNum },
            ignoreErrors: true,
            success: function(response, options, r) {
                var progressNum = options.params.progressNum;
                if (!this._progressData[progressNum]) return;

                if (typeof r.finished != 'undefined') {
                    if (r.finished) {
                        this._progressData[progressNum].progressBar.updateProgress(
                            1, '100%', trlKwf('Finished')
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
        var progressBar = new Ext2.ProgressBar({
            text:'0%',
            animate: true
        });
        cfg = Ext2.applyIf(cfg, {
            title: trlKwf('Progress'),
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
        });
        var dlg = new Ext2.Window(cfg);

        if (typeof cfg.showCancel == 'undefined' || cfg.showCancel) {
            dlg.addButton(
                { text: trlKwf('Cancel') },
                (function(dialog) {
                    Ext2.Ajax.abort(dialog.transId);

                    var responseObject = Ext2.lib.Ajax.createExceptionObject(
                        dialog.transId, null, true
                    );
                    Ext2.callback(
                        dialog.requestOptions.kwfCallback.failure,
                        dialog.requestOptions.kwfCallback.scope,
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
            html:'<div class="kwf-progress-content"><span class="kwf-progress-text"></span><br /></div>'
        });
        dlg.myEls.bodyEl.addClass('kwf-progress-window');
        dlg.myEls.msgEl = Ext2.get(dlg.myEls.bodyEl.dom.childNodes[0].firstChild);

        dlg.progressBar = new Ext2.ProgressBar({
            renderTo:dlg.myEls.bodyEl,
            text: '0%',
            animate: true
        });
        dlg.myEls.bodyEl.createChild({cls:'x2-clear'});

        dlg.updateProgress = function(num, progressBarText, text)
        {
            this.progressBar.updateProgress(num, progressBarText, true);
            this.myEls.msgEl.update(text || '&#160;');
        };

        dlg.show();
        return dlg;
    },

    repeatRequest: function(options) {
        Kwf.Connection.runningRequests++;
        delete options.kwfIsSuccess;
        delete options.kwfLogin;

        Kwf.Connection.superclass.request.call(this, options);
        if (options.progress) {
            this._showProgress(options);
        }
    },
    kwfJsonSuccess: function(response, options)
    {
        options.kwfIsSuccess = false;
        options.kwfLogin = false;

        var errorMsg = false;

        var encParams;
        if (typeof options.params == "string") {
            encParams = options.params;
        } else {
            encParams = Ext2.urlEncode(options.params);
        }
        try {
            if (!response.responseText) {
                errorMsg = 'response is empty';
            } else {
                var r = Ext2.decode(response.responseText);
            }
        } catch(e) {
            errorMsg = e.toString()+': <br />'+response.responseText;
            var errorMsgTitle = 'Javascript Parse Exception';
        }
        if (errorMsg) {
            errorMsg = '<a href="'+options.url+'?'+encParams+'">request-url</a><br />' + errorMsg;
            var sendMail = !r || !r.exception;
            if (options.errorText) {
                errorText = options.errorText;
            } else {
                errorText = null;
            }
            if (options.ignoreErrors) {
                Ext2.callback(options.kwfCallback.failure, options.kwfCallback.scope, [response, options]);
            } else {
                Kwf.handleError({
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
                        Ext2.callback(this.options.kwfCallback.failure, this.options.kwfCallback.scope, [this.response, this.options]);
                    },
                    scope: { connection: this, options: options, response: response }
                });
            }
            return;
        }

        if (!r.success && !options.ignoreErrors) {
            if (r.error) {
                Ext2.Msg.alert(trlKwf('Error'), r.error);
            } else {
                Ext2.Msg.alert(trlKwf('Error'), trlKwf("A Server failure occured."));
            }
            Ext2.callback(options.kwfCallback.failure, options.kwfCallback.scope, [response, options]);
            return;
        }
        options.kwfIsSuccess = true;
        if (options.kwfCallback.success) {
            options.kwfCallback.success.call(options.kwfCallback.scope, response, options, r);
        }
    },
    kwfNoJsonSuccess: function(response, options)
    {
        options.kwfIsSuccess = true;
        if (options.kwfCallback.success) {
            options.kwfCallback.success.call(options.kwfCallback.scope, response, options);
        }
    },
    kwfNoJsonFailure: function(response, options)
    {
        options.kwfIsSuccess = false;
        if (options.kwfCallback.failure) {
            options.kwfCallback.failure.call(options.kwfCallback.scope, response, options);
        }
    },
    kwfJsonFailure: function(response, options)
    {
        if (Kwf.Connection.isLeavingPage) return; //when user leaves page all requests are stopped. Don't show errors in that case.

        options.kwfIsSuccess = false;

        var r = Ext2.decode(response.responseText);

        if (r && r.wrongversion && !options.ignoreErrors) {
            var dlg = new Ext2.Window({
                autoCreate : true,
                title: trlKwf('Error - wrong version'),
                resizable: false,
                modal: true,
                buttonAlign:"center",
                width:250,
                height:100,
                plain:true,
                closable: false,
                html: trlKwf('Because of an application update the application has to be reloaded.'),
                buttons: [{
                    text: trlKwf('OK'),
                    handler: function() {
                        location.reload();
                    },
                    scope: this
                }, {
                    text: trlKwf('Ignore'),
                    handler: function() {
                        Kwf.application.assetsVersion = r.assetsVersion;
                        options.params.applicationAssetsVersion = Kwf.application.assetsVersion;
                        this.repeatRequest(options);
                        dlg.hide();
                    },
                    scope: this
                }]
            });
            dlg.show();
            dlg.getEl().addClass('x2-window-dlg');
            return;
        }
        if (r && r.login && !options.ignoreErrors) {
            options.kwfLogin = true;
            if (!Kwf.Connection._loginDialog) {
                Kwf.Connection._afterLoginRequests = [];
                Kwf.Connection._loginDialog = new Kwf.User.Login.Dialog({
                    message: r.message,
                    success: function() {

                        Kwf.Connection._loginDialog.destroy();
                        delete Kwf.Connection._loginDialog;

                        //redo requests...
                        Kwf.Connection._afterLoginRequests.each(function(i) {
                            this.repeatRequest(i);
                        }, this);

                        Kwf.Connection._afterLoginRequests.length = 0;
                    },
                    scope: this
                });
                Ext2.getBody().unmask();
                Kwf.Connection._loginDialog.showLogin();
            }
            Kwf.Connection._afterLoginRequests.push(options); //redo after login
            return;
        }


        var errorText, errorMsg;
        var errorMsgTitle = trlKwf('Error');
        if (options.errorText) {
            errorText = options.errorText;
            errorMsg = options.errorText;
        } else if (r && r.exception) {
            errorMsg = '<pre>' + Ext2.util.Format.htmlEncode(r.exception.join('\n')) + '</pre>';
            errorMsgTitle = 'PHP Exception';
        } else if (r && r.error) {
            errorMsg = Ext2.util.Format.htmlEncode(r.error);
            errorMsgTitle = 'PHP Exception';
        } else {
            errorMsg = trlKwf("A connection problem occured.");
            errorText = null;
        }
        var retry = false;
        if (response.status == -1) {
            //request failed not because of error response (eg 500) but because of eg. timeout
            //allow the user to retry the request
            retry = true;
        } else {
            //request failed because of error response (eg 500), don't allow the user to
            //retry as it would result in the same error again
            //however if displayErrors is enabled (during development) retry is possible (this is handled in Kwf.handleError)
        }
        if (options.ignoreErrors) {
            Ext2.callback(options.kwfCallback.failure, options.kwfCallback.scope, [response, options]);
        } else {
            Kwf.handleError({
                url: options.url,
                message: errorMsg,
                title: errorMsgTitle,
                errorText: errorText,
                mail: false,
                checkRetry: retry,
                retry: function() {
                    this.repeatRequest(options);
                },
                abort: function() {
                    Ext2.callback(options.kwfCallback.failure, options.kwfCallback.scope, [response, options]);
                },
                scope: this
            });
        }
    },

    kwfCallback: function(options, success, response)
    {
        //wenn login-fenster angezeigt wird keinen callback aufrufen - weil der request
        //wird ja erneut gesendet und da dann der callback aufgerufen.
        if (options.kwfLogin) return;

        if (options.mask) {
            if (options.mask instanceof Ext2.Element) {
                options.mask.unmask();
            } else {
                Kwf.Connection.masks--;
                if (Kwf.Connection.masks == 0) {
                    Ext2.getBody().unmask();
                    if (Ext2.get('loading')) {
                        Ext2.get('loading').fadeOut({remove: true});
                    }
                }
            }
        }

        if (options.progress) {
            this._progressData[options.params.progressNum].progressBar.hide();
            delete this._progressData[options.params.progressNum];
        }

        if (success && !options.kwfIsSuccess) {
            success = false;
        }
        Ext2.callback(options.kwfCallback.callback, options.kwfCallback.scope, [options, success, response]);
        Kwf.Connection.runningRequests--;
    }
});
Kwf.Connection.masks = 0; //static var that hols number of masked requests
Kwf.Connection.runningRequests = 0;

Ext2.Ajax = new Kwf.Connection({
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
        return Ext2.lib.Ajax.serializeForm(form);
    }
});

