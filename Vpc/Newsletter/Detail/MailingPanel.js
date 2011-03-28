Ext.ns('Vpc.Newsletter.Detail');
Vpc.Newsletter.Detail.MailingPanel = Ext.extend(Vps.Auto.GridPanel, {
    initComponent : function()
    {
        this.button = [];
        this.button['stop'] = new Ext.Toolbar.Button ({
            icon    : '/assets/silkicons/control_stop.png',
            cls     : 'x-btn-text-icon',
            text    : trlVps('Stop'),
            enableToggle: true,
            toggleGroup: 'control',
            pressed : false,
            disabled : true,
            toggleHandler: this.toggleButton,
            scope: this,
            name : 'stop'
        });
        this.button['pause'] = new Ext.Toolbar.Button ({
            icon    : '/assets/silkicons/control_pause.png',
            cls     : 'x-btn-text-icon',
            text    : trlVps('Pause'),
            enableToggle: true,
            toggleGroup: 'control',
            pressed : false,
            disabled : true,
            toggleHandler: this.toggleButton,
            scope: this,
            name : 'pause'
        });
        this.button['start'] = new Ext.Toolbar.Button ({
            icon    : '/assets/silkicons/control_play.png',
            cls     : 'x-btn-text-icon',
            text    : trlVps('Start'),
            enableToggle: true,
            toggleGroup: 'control',
            pressed : false,
            toggleHandler: this.toggleButton,
            scope: this,
            name : 'start'
        });
        var reload = new Ext.Toolbar.Button ({
            icon    : '/assets/silkicons/arrow_rotate_clockwise.png',
            cls     : 'x-btn-icon',
            handler: this.load,
            scope   : this
        });
        this.actions.deleteAll = new Ext.Action({
            text    : trlVps('Delete All'),
            icon    : '/assets/silkicons/bin_empty.png',
            cls     : 'x-btn-text-icon',
            handler : function(){
                Ext.Msg.confirm(
                    trlVps('Are you sure?'),
                    trlVps('Do you really want to delete all receivers with status "queued"?.'),
                    function(result) {
                        if (result == 'yes') {
                            Ext.Ajax.request({
                                url : this.controllerUrl + '/json-delete-all',
                                params: this.getBaseParams(),
                                success: function(response, options, r) {
                                    Ext.MessageBox.alert(trlVps('Status'), r.message);
                                    this.reload();
                                },
                                scope: this
                            });
                        }
                    },
                    this
                );
            },
            scope   :   this
        });

        this.progress = new Ext.ProgressBar();
        // http://www.extjs.com/forum/showthread.php?t=93086
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
            this.syncProgressBar();
        };
        this.progress.setSize(200, 16);
        
        this.startTimer = function() {
            var self = this;
            function updateTimer() {
                self.hTimer = window.setTimeout(updateTimer, 5000);
                self.tick();
            }
            this.hTimer = window.setTimeout(updateTimer, 5000);
        };
        
        this.stopTimer = function() {
            if (this.hTimer != null) window.clearTimeout(this.hTimer);
            this.hTimer = null;
        };
        
        this.tick = function() {
            if (this.timerBusy) return;
            this.timerBusy = true;
            Ext.Ajax.request({
                url: this.controllerUrl + '/json-status',
                params : this.getBaseParams(),
                success: function(response, options, r) {
                    if (!this.setProgress(r.info))
                        this.stopTimer();
                    this.timerBusy = false;
                },
                failure: function(response) {
                    this.timerBusy = false;
                },
                scope: this
            });
        };
        
        this.setProgress = function(info)
        {
            var progress = 0;
            if (info.total > 0) progress = info.sent / info.total;
            this.progress.updateProgress(progress, Math.round(progress * 100) + '%');
            this.status.el.innerHTML = info.text;
            return (info.state == 'start' || info.state == 'sending');
        };
        
        this.status = new Ext.Toolbar.TextItem({'text' : ' ' });

        this.tbar = [this.progress, {xtype: 'tbspacer'}, this.status];

        this.on('load', function(r, s, t) {
            this.askOnStart = false; // HACK, aber das ganze ist sowieso ein bisschen unübersichtlich...
            var info = r.reader.jsonData.info;
            if (info.state == 'finished') {
                this.button['pause'].disable();
                this.button['start'].disable();
                this.button['stop'].disable();
            } else if (info.state) {
                if (info.state == 'sending') info.state = 'start';
                this.button[info.state].toggle(true);
            }
            this.askOnStart = true;
            if (this.setProgress(info)) {
                this.startTimer();
            }
        }, this);

        this.on('loaded', function(r, s, t) {
            this.getGrid().topToolbar.add('|', this.button.stop, this.button.pause, this.button.start, '->', reload);
        }, this);

        Vpc.Newsletter.Detail.MailingPanel.superclass.initComponent.call(this);
    },

    toggleButton : function(button, pressed)
    {
        if (this.askOnStart && button.name == 'start') {
            Ext.Msg.confirm(
                trlVps('Start Newsletter sending'),
                trlVps('The newsletter will be sent in the background. You can leave this page or close the browser window. Start sending?'),
                function(result) {
                    if (result == 'no') {
                        this.button.start.toggle(false, true);
                        return;
                    } else {
                        this.askOnStart = false;
                        this.toggleButton(this.button.start, true);
                    }
                },
                this
            );
            return;
        }
        this.button['stop'].enable();
        if (button.name == 'stop') {
            this.button['pause'].disable();
            this.button['start'].disable();
        } else {
            this.button['pause'].enable();
            this.button['start'].enable();
        }
        if (!this.button.pause.pressed && !this.button.stop.pressed && !this.button.start.pressed) {
            button.toggle(true);
        }
        if (!pressed) return;
        if (this.pressedButton && this.pressedButton != 'stop' && button.name == 'stop') {
            Ext.Msg.confirm(
                trlVps('Are you sure?'),
                trlVps('If you press yes, the mailing will be stopped an can\'t be restarted again.'),
                function(result) {
                    if (result == 'no') {
                        this.button.pause.toggle(true);
                    } else {
                        this.toggleRequest(button);
                    }
                },
                this
            );
        } else {
            this.toggleRequest(button);
        }
    },
    
    toggleRequest : function(button)
    {
        if (this.pressedButton == button.name) return;
        Ext.Ajax.request({
            url: this.controllerUrl + '/json-change-status',
            params : Ext.apply(Vps.clone(this.getBaseParams()), {
                status: button.name
            }),
            success: function(response, options, r) {
                this.pressedButton = button.name;
            	var text = button.name == 'start' ? trlVps('Sending') : trlVps('Start');
                this.button.start.setText(text);
                if (r.info.state == 'sending') r.info.state = 'start';
                if (r.info.state != this.pressedButton) this.button[r.info.state].toggle(true);
                if (this.setProgress(r.info)) this.startTimer();
            },
            failure: function(response) {
                this.button[this.pressedButton].toggle(true);
            },
            scope: this
        });
    }
});
Ext.reg('vpc.newsletter.mailing', Vpc.Newsletter.Detail.MailingPanel);
