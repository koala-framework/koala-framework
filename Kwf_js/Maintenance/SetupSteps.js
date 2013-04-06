Ext.ns('Kwf.Maintenance');
Kwf.Maintenance.SetupSteps = Ext.extend(Ext.Panel, {
    currentStep: 'welcome',
    border: false,
    cls: 'kwfSetupSteps',
    initComponent: function() {
        this.stepsTemplate = new Ext.XTemplate(
            '<ul>',
            '<tpl for="steps">',
                '<li class="<tpl if="step == parent.currentStep">current</tpl>"><span class="num">{num}.</span> {text}</li>',
            '</tpl>',
            '</ul>'
        );
        Kwf.Maintenance.SetupSteps.superclass.initComponent.call(this);
    },
    
    afterRender: function() {
        Kwf.Maintenance.SetupSteps.superclass.afterRender.apply(this, arguments);
        this._updateSteps();
    },

    setCurrentStep: function(step) {
        this.currentStep = step;
        this._updateSteps();
    },

    _updateSteps: function() {
        var data = {
            steps: [
                {
                    num: 1,
                    step: 'welcome',
                    text: 'Welcome'
                }, {
                    num: 2,
                    step: 'requirements',
                    text: 'Requirements'
                }, {
                    num: 3,
                    step: 'dbconfig',
                    text: 'Database Config'
                }, {
                    num: 4,
                    step: 'config',
                    text: 'Config'
                }, {
                    num: 5,
                    step: 'adminAccount',
                    text: 'Admin Account'
                }, {
                    num: 6,
                    step: 'install',
                    text: 'Installation'
                }, {
                    num: 7,
                    step: 'finished',
                    text: 'Finished'
                }
            ],
            currentStep: this.currentStep
        };
        this.stepsTemplate.overwrite(this.body, data);
    }
});
