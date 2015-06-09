Ext2.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.StartNewsletterPanel = Ext2.extend(Kwf.Binding.AbstractPanel, {

    initComponent: function()
    {
        Ext2.applyIf(this, {
            title: trlKwf('Mailing'),
            layout: 'column',
            border: false,
            height: 130,
            defaults: {
                border: false,
                height: 130
            }
        });

        this.newsletterButtonsContainer = new Ext2.Element(document.createElement('div'));
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
            Ext2.Ajax.request({
                url: this.controllerUrl + '/json-change-status',
                params : Ext2.apply(Kwf.clone(this.getBaseParams()), {
                    status: 'pause'
                }),
                success: function(response, options, r) {
                    this._updateButtons(r.info);
                    this._updateProgress(r.info);
                },
                scope: this
            });
        }, this);

        this.newsletterStartButtonContainer = this.newsletterButtonsContainer.createChild({
            tag: 'div',
            cls: 'kwcNewsletterButton start'
        });
        this.newsletterStartButtonContainer.enableDisplayMode('block');
        this.newsletterStartButton = this.newsletterStartButtonContainer.createChild({
            tag: 'button',
            cls: 'kwcNewsletterButtonStart',
            html: trlKwf('Send Newsletter')
        });
        this.newsletterStartButton.on('click', function(ev) {
            this.mailingFormWindow = new Kwf.Auto.Form.Window({
                controllerUrl: this.formControllerUrl,
                editTitle: trlKwf('Send Newsletter'),
                saveText: trlKwf('Start')
            }, this);
            this.mailingFormWindow.on('datachange', function() {
                this.load();
            }, this);
            this.mailingFormWindow.applyBaseParams(this.getBaseParams());
            this.mailingFormWindow.showEdit(this.baseParams.newsletterId);
        }, this);

        this.mailingSettingsPanel = new Ext2.Panel({
            columnWidth: .3,
            items: [this.newsletterButtonsContainer]
        });

        this.newsletterProgressContainer = new Ext2.Element(document.createElement('div'));
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

        this.progress = new Ext2.ProgressBar({
            renderTo: this.newsletterProgressContainer
        });
        this.progress.setSize = Ext2.ProgressBar.superclass.setSize;
        this.progress.onResize =  function(w, h) {
            var inner = Ext2.get(this.el.child('.x2-progress-inner')),
                bar = inner.child('.x2-progress-bar'),
                pt = inner.child('.x2-progress-text'),
                ptb = inner.child('.x2-progress-text-back');
            Ext2.ProgressBar.superclass.onResize.apply(this, arguments);
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

        this.mailingSendingPanel = new Ext2.Panel({
            columnWidth: .7,
            items: [this.newsletterProgressContainer]
        });

        this.items = [this.mailingSendingPanel, this.mailingSettingsPanel];
        Kwc.Newsletter.Detail.StartNewsletterPanel.superclass.initComponent.call(this);
    },

    load: function()
    {
        this._updateStatus({
            mask: this.el,
            ignoreVisible: true
        });
    },

    _updateProgress: function(info)
    {
        var progress = 0;
        if (info.total > 0) progress = info.sent / info.total;
        this.progress.updateProgress(progress, Math.round(progress * 100) + '%');
        this.newsletterInfoMailsSent.update(info.text);
        if (info.state == 'start' || info.state == 'sending') {
            this.newsletterInfoSpeed.update(trlKwf('current speed: <b>{0}</b>', [info.speed]));
            this.newsletterInfoTime.update(trlKwf('Remaining time: <b>{0}</b>', [info.remainingTime]));
        }
    },

    _deferedUpdateStatus: function()
    {
        if (!this._deferedUpdateStatusRunning) {
            this._deferedUpdateStatusRunning = true;
            (function() {
                this._deferedUpdateStatusRunning = false;
                this._updateStatus();
            }).defer(5000, this);
        }
    },

    _updateStatus: function(options)
    {
        if (!this.el.isVisible(true) && (!options || !options.ignoreVisible)) {
            this._deferedUpdateStatus();
            return;
        }
        Ext2.Ajax.request({
            mask: (options && options.mask) ? options.mask : false,
            url: this.controllerUrl + '/json-status',
            params : this.getBaseParams(),
            success: function(response, options, r) {
                this._updateButtons(r.info);
                this._updateProgress(r.info);
            },
            callback: function() {
                this._deferedUpdateStatus();
            },
            scope: this
        });
    },

    _updateButtons: function(info)
    {
        if (parseInt(info.queued) == 0 && parseInt(info.sent) == 0) {
            this.newsletterStartButton.dom.disabled = true;
        } else {
            this.newsletterStartButton.dom.disabled = false;
            if (info.state == 'start' || info.state == 'startLater' || info.state == 'sending') {
                this.newsletterStartButtonContainer.hide();
                this.newsletterPauseButtonContainer.show();
            } else if (info.state == 'pause') {
                this.newsletterStartButtonContainer.show();
                this.newsletterPauseButtonContainer.hide();
                this.newsletterStartButton.update(trlKwf('Resume Sending'));
            } else if (info.state == 'finished') {
                this.newsletterStartButtonContainer.show();
                this.newsletterPauseButtonContainer.hide();
                this.newsletterStartButton.update(trlKwf('Sending finished'));
                this.newsletterStartButton.dom.disabled = true;
            } else {
                this.newsletterStartButtonContainer.show();
                this.newsletterPauseButtonContainer.hide();
                this.newsletterStartButton.update(trlKwf('Send Newsletter'));
            }
        }
    }
});
Ext2.reg('kwc.newsletter.startNewsletter', Kwc.Newsletter.Detail.StartNewsletterPanel);
