Kwf.Utils.BackgroundProcess = {
    request: function(options) {
        if (!options.params) options.params = {};
        var progressNum = Math.floor(Math.random() * 1000000000) + 1;
        options.progressNum = progressNum;
        options.params.progressNum = progressNum;
        Ext.Ajax.request({
            url: options.url,
            params: options.params,
            bgOptions: options,
            success: function(response, o, r) {
                if (r.backgroundProcessRunning) {
                    o.bgOptions.pid = r.pid;
                    o.bgOptions.outputFile = r.outputFile;
                } else {
                    //no background process was started, request finished
                    if (o.bgOptions.progressBar) o.bgOptions.progressBar.destroy();
                    if (o.bgOptions.currentStatusRequest) {
                        Ext.Ajax.abort(o.bgOptions.currentStatusRequest);
                    }
                    if (o.bgOptions.success) o.bgOptions.success.call(o.bgOptions.scope || window, response, o, r);
                    o.bgOptions.finished = true;
                }
            },
            failure: function() {
                if (o.bgOptions.progressBar) o.bgOptions.progressBar.destroy();
                if (o.bgOptions.currentStatusRequest) {
                    Ext.Ajax.abort(o.bgOptions.currentStatusRequest);
                }
                if (o.bgOptions.failure) o.bgOptions.failure.call(o.bgOptions.scope || window);
                o.bgOptions.finished = true;
            },
            scope: this
        });

        if (options.progress) {
            this._showProgress(options);
        }
        this._doProgressStatusRequest.defer(500, this, [ options ]); //do that in any case, even if we don't show progress dialog
    },

    _showProgress: function(options)
    {
        options.progressBar = this._createProgressDialog({
            title: options.progressTitle || trlKwf('Progress')
        });
        options.progressBar.updateProgress(0, '0%', '');
    },

    _doProgressStatusRequest: function(options)
    {
        if (options.finished) return;
        var params = {
            progressNum: options.progressNum
        };
        if (options.pid) params.pid = options.pid;
        if (options.outputFile) params.outputFile = options.outputFile;
        options.currentStatusRequest = Ext.Ajax.request({
            url: '/kwf/json-progress-status',
            params: params,
            bgOptions: options,
            success: function(response, o, r) {
                if (r.bgFinished) {
                    o.bgOptions.progressBar.destroy();
                    if (r.bgError) Ext.Msg.alert(trlKwf('Error'), r.bgError);
                    if (r.bgResponse && r.bgResponse.success) {
                        //call success cb
                        if (o.bgOptions.success) options.success.call(o.bgOptions.scope || window, response, null, r.bgResponse);
                    } else {
                        //call failure cb
                        if (o.bgOptions.failure) o.bgOptions.failure.call(o.bgOptions.scope || window);
                    }
                    return;
                }

                if (typeof r.finished != 'undefined') {
                    if (r.finished) {
                        o.bgOptions.progressBar.updateProgress(
                            1, '100%', trlKwf('Finished')
                        );
                        return;
                    }

                    o.bgOptions.progressBar.updateProgress(
                        r.percent / 100,
                        Math.floor(r.percent)+'%',
                        r.text ? r.text : ''
                    );
                }

                this._doProgressStatusRequest.defer(500, this, [ o.bgOptions ]);
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
        var dlg = new Ext.Window(cfg);

        dlg.render(document.body);
        dlg.myEls = { };
        dlg.myEls.bodyEl = dlg.body.createChild({
            html:'<div class="kwf-progress-content"><span class="kwf-progress-text"></span><br /></div>'
        });
        dlg.myEls.bodyEl.addClass('kwf-progress-window');
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
    }
};
