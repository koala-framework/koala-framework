Vps.Welcome = Ext.extend(Ext.Panel,
{
    afterRender: function() {
        this.welcomePanel = new Ext.Panel({
            cls: 'vps-welcome',
            width: 304,
            autoLoad: '/vps/welcome/content',
            border: false,
            renderTo: this.getEl()
        });
        this.welcomePanel.getUpdater().on('update', function() {
            this.welcomePanel.getEl().center();
        }, this);
        Vps.Welcome.superclass.afterRender.call(this);
    },
    onResize: function(w, h) {
        Vps.Welcome.superclass.onResize.call(this, w, h);
        this.welcomePanel.getEl().center();
    }
});
var Welcome = Vps.Welcome;
