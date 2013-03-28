Ext.ns('Kwf.Maintenance');
Kwf.Maintenance.Setup = Ext.extend(Ext.Panel, {
    border: false,
    initComponent: function() {
        this.layout = 'border';
        this.about = {
            cls: 'kwfSetupAbout',
            region: 'north',
            height: 150,
            html : '<div class="about">'+
                    '<img class="logo" src="http://www.koala-framework.org/assets/web/images/logo.png" >'+
                    '<h1>'+trlKwf('Setup')+'</h1>'+
                    '<h2>'+this.kwfVersion+'</h2>'+
                    '<h2>'+this.appVersion+'</h2>'+
                    '</div>'
        };
        this.steps = new Kwf.Maintenance.SetupSteps({
            width: 300,
            region: 'west'
        });

        //welcome
        this.stepWelcome = new Ext.Panel({
            width: 350,
            height: 200,
            border: false,
            cls: 'kwfSetupWelcome',
            html: '<h1>Welcome</h1>'+
                  '<p>to the installation of Koala Framework.</p>'+
                  '<p>This Setup Tool will guide you throu the installation process.</p>',
            buttons: [{
                text: 'Continue',
                handler: function() {
                    this.cards.getLayout().setActiveItem(this.stepRequirements);
                    this.stepRequirements.refresh();
                    this.steps.setCurrentStep('requirements');
                },
                scope: this
            }]
        });

        //requirements
        this.stepRequirements = new Kwf.Maintenance.SetupRequirements({
        });
        this.stepRequirements.on('continue', function() {
            this.cards.getLayout().setActiveItem(this.stepDatabase);
            this.steps.setCurrentStep('dbconfig');
        }, this);

        //dbconfig
        this.stepDatabase = new Ext.FormPanel({
            border: false,
            bodyStyle: "padding: 10px;",
            items: [{
                xtype: 'textfield',
                name: 'db_username',
                fieldLabel: 'Username',
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: 'db_password',
                fieldLabel: 'Password',
                type: 'password'
            }, {
                xtype: 'textfield',
                name: 'db_dbname',
                fieldLabel: 'Database',
                value: this.defaultDbNmae,
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: 'db_host',
                value: 'localhost',
                fieldLabel: 'Host',
                allowBlank: false
            }],
            buttons: [{
                text: 'Continue',
                handler: function() {
                    if (!this.stepDatabase.getForm().isValid()) return;
                    Ext.Ajax.request({
                        url: '/kwf/maintenance/setup/json-check-db',
                        params: this.stepDatabase.getForm().getValues(),
                        success: function() {
                            this.cards.getLayout().setActiveItem(this.stepConfig);
                            this.steps.setCurrentStep('config');
                        },
                        scope: this
                    });
                },
                scope: this
            }]
        });

        //config
        this.stepConfig = new Ext.FormPanel({
            border: false,
            bodyStyle: "padding: 10px;",
            items: [{
                xtype: 'checkbox',
                name: 'display_errors',
                fieldLabel: 'Display Errors'
            }],
            buttons: [{
                text: 'Continue',
                handler: function() {
                    if (!this.stepConfig.getForm().isValid()) return;
                    this._startInstallation();
                },
                scope: this
            }]
        });

        //install
        this.stepInstall = new Ext.Panel({
            html: '... installing ...'
        });

        //finished
        this.stepFinished = new Ext.Panel({
            html: '... finished ...'
        });

        this.cards = new Ext.Panel({
            region: 'center',
            layout: 'card',
            activeItem: 0,
            items: [
                this.stepWelcome,
                this.stepRequirements,
                this.stepDatabase,
                this.stepConfig,
                this.stepInstall,
                this.stepFinished
            ]
        });
        this.items = [
            this.about,
            {
                region: 'center',
                layout: 'border',
                border: false,
                items: [this.cards, this.steps]
            }
        ];

        Kwf.Maintenance.Setup.superclass.initComponent.call(this);
    },
    
    _startInstallation: function() {
        this.cards.getLayout().setActiveItem(this.stepInstall);
        this.steps.setCurrentStep('install');
        var params = {};
        Ext.apply(params, this.stepDatabase.getForm().getValues());
        Ext.apply(params, this.stepConfig.getForm().getValues());
        Ext.Ajax.request({
            url: '/kwf/maintenance/setup/json-install',
            params: params,
            success: function() {
                this.cards.getLayout().setActiveItem(this.stepFinished);
                this.steps.setCurrentStep('finished');
            },
            scope: this
        });
    }
});
Ext.reg('kwf.maintenance.setup', Kwf.Maintenance.Setup);
