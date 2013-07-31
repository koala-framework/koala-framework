Ext.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.StartNewsletterPanel = Ext.extend(Kwf.Binding.AbstractPanel, {

    initComponent: function()
    {
        Ext.applyIf(this, {
            title: trlKwf('Mailing'),
            layout: 'column',
            border: false,
            height: 100,
            defaults: {
                border: false,
                height: 100
            }
        });

        this.newsletterButtonsContainer = new Ext.Element(document.createElement('div'));
        this.newsletterButtonsContainer.addClass('kwcNewsletterButtons');

        this.newsletterPauseButtonContainer = this.newsletterButtonsContainer.createChild({
            tag: 'div',
            cls: 'kwcNewsletterButton pause',
            style: {
                display: 'none'
            }
        });
        this.newsletterPauseButtonContainer.enableDisplayMode('block');
        this.newsletterPauseButton = this.newsletterPauseButtonContainer.createChild({
            tag: 'button',
            cls: 'kwcNewsletterButtonPause',
            html: trlKwf('Pause')
        });
        this.newsletterPauseButton.on('click', function(ev) {
            this.setStatus('pause');
        }, this);

        this.newsletterStartButtonContainer = this.newsletterButtonsContainer.createChild({
            tag: 'div',
            cls: 'kwcNewsletterButton start'
        });
        this.newsletterStartButtonContainer.enableDisplayMode('block');
        this.newsletterStartButton = this.newsletterStartButtonContainer.createChild({
            tag: 'button',
            cls: 'kwcNewsletterButtonStart',
            html: trlKwf('Start newsletter')
        });
        this.newsletterStartButton.on('click', function(ev) {
            this.mailingFormWindow = new Kwf.Auto.Form.Window({
                controllerUrl: this.formControllerUrl,
                title: trlKwf('Newsletter sending settings')
            }, this);
            this.mailingFormWindow.on('datachange', function() {
                this.load();
            }, this);
            this.mailingFormWindow.applyBaseParams(this.getBaseParams());
            this.mailingFormWindow.showEdit(this.baseParams.newsletterId);
        }, this);

        this.mailingSettingsPanel = new Ext.Panel({
            columnWidth: .3,
            items: [this.newsletterButtonsContainer]
        });

        this.newsletterProgressContainer = new Ext.Element(document.createElement('div'));
        this.newsletterProgressContainer.addClass('kwcNewsletterProgress');

        this.newsletterProgressText = this.newsletterProgressContainer.createChild({
            tag: 'div',
            cls: 'kwcNewsletterProgressText'
        });
        this.newsletterInfoMailsSent = this.newsletterProgressText.createChild({
            tag: 'span',
            cls: 'kwcNewsletterInfoMailsSent'
        });
        this.newsletterInfoTime = this.newsletterProgressText.createChild({
            tag: 'span',
            cls: 'kwcNewsletterInfoTime'
        });
        this.newsletterInfoSpeed = this.newsletterProgressText.createChild({
            tag: 'span',
            cls: 'kwcNewsletterInfoSpeed'
        });

        this.progress = new Ext.ProgressBar({
            renderTo: this.newsletterProgressContainer
        });
        this.progress.setSize = Ext.ProgressBar.superclass.setSize;
        this.progress.onResize =  function(w, h) {
            var inner = Ext.get(this.el.child('.x-progress-inner')),
                bar = inner.child('.x-progress-bar'),
                pt = inner.child('.x-progress-text'),
                ptb = inner.child('.x-progress-text-back');
            Ext.ProgressBar.superclass.onResize.apply(this, arguments);
            inner.setHeight(h);
            bar.setHeight(h);
            this.textEl.setHeight('auto');
            pt.setHeight('auto');
            ptb.setHeight('auto');
            pt.child('div').setWidth('auto');
            ptb.child('div').setWidth('auto');
            this.syncProgressBar();
        };
        this.progress.setSize('90%', 16);

        this.mailingSendingPanel = new Ext.Panel({
            columnWidth: .7,
            items: [this.newsletterProgressContainer]
        });

        this.items = [this.mailingSendingPanel, this.mailingSettingsPanel];
        Kwc.Newsletter.Detail.StartNewsletterPanel.superclass.initComponent.call(this);
    },

    load: function()
    {
        Ext.Ajax.request({
            url: this.controllerUrl + '/json-status',
            params : this.getBaseParams(),
            success: function(response, options, r) {
                var info = r.info;
                this.checkButtons(info);
                if (info.state == 'sending') info.state = 'start';
                if (this.setProgress(info)) {
                    this.startTimer();
                }
            },
            scope: this
        });
    },

    setProgress: function(info)
    {
        var progress = 0;
        if (info.total > 0) progress = info.sent / info.total;
        this.progress.updateProgress(progress, Math.round(progress * 100) + '%');
        this.newsletterInfoMailsSent.update(info.text);
        if (info.state == 'start' || info.state == 'sending') {
            this.newsletterInfoSpeed.update(trlKwf('Newsletter speed: <b>{0}</b>', [info.speed]));
            this.newsletterInfoTime.update(trlKwf('Remaining time: <b>{0}</b>', [info.remainingTime]));
        }
        return !(info.state == 'finished');
    },

    setStatus: function(status)
    {
        Ext.Ajax.request({
            url: this.controllerUrl + '/json-change-status',
            params : Ext.apply(Kwf.clone(this.getBaseParams()), {
                status: status
            }),
            success: function(response, options, r) {
                if (status == 'pause') this.newsletterStartButton.update(trlKwf('Start newsletter'));
                if (r.info.state == 'sending') r.info.state = 'start';
                if (this.setProgress(r.info)) this.startTimer();
                this.load();
            },
            scope: this
        });
    },

    startTimer: function()
    {
        var self = this;
        function updateTimer() {
            self.hTimer = window.setTimeout(updateTimer, 30000);
            self.tick();
        }
        this.hTimer = window.setTimeout(updateTimer, 5000);
    },

    stopTimer: function()
    {
        if (this.hTimer != null) window.clearTimeout(this.hTimer);
        this.hTimer = null;
    },

    tick: function()
    {
        if (this.timerBusy) return;
        this.timerBusy = true;
        Ext.Ajax.request({
            url: this.controllerUrl + '/json-status',
            params : this.getBaseParams(),
            success: function(response, options, r) {
                this.checkButtons(r.info);
                if (!this.setProgress(r.info))
                    this.stopTimer();
                this.timerBusy = false;
            },
            failure: function(response) {
                this.timerBusy = false;
            },
            scope: this
        });
    },

    checkButtons: function(info)
    {
        if (info.state == 'start' || info.state == 'startLater') {
            this.newsletterStartButtonContainer.hide();
            this.newsletterPauseButtonContainer.show();
            this.newsletterStartButton.update(trlKwf('Waiting for start...'));
        } else if (info.state == 'pause') {
            this.newsletterStartButtonContainer.show();
            this.newsletterPauseButtonContainer.hide();
        } else  if (info.state == 'sending') {
            this.newsletterStartButtonContainer.hide();
            this.newsletterPauseButtonContainer.show();
            this.newsletterStartButton.update(trlKwf('Edit newsletter'));
        } else if (info.state == 'finished') {
            this.newsletterStartButtonContainer.show();
            this.newsletterPauseButtonContainer.hide();
            this.newsletterStartButton.update(trlKwf('Newsletter finished'));
            this.newsletterStartButton.dom.disabled = 'disabled';
        }
    }
});
Ext.reg('kwc.newsletter.startNewsletter', Kwc.Newsletter.Detail.StartNewsletterPanel);
