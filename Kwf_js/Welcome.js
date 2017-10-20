Kwf.Welcome = Ext2.extend(Ext2.Panel,
{
    afterRender: function() {
        this.welcomePanel = new Ext2.Panel({
            cls: 'kwf-welcome',
            width: 304,
            autoLoad: KWF_BASE_URL+'/kwf/welcome/content',
            border: false,
            renderTo: this.getEl()
        });
        this.welcomePanel.getUpdater().on('update', function() {
            this.welcomePanel.getEl().center();
        }, this);
        Kwf.Welcome.superclass.afterRender.call(this);
    },
    onResize: function(w, h) {
        Kwf.Welcome.superclass.onResize.call(this, w, h);
        this.welcomePanel.getEl().center();
    }
});
window.Welcome = Kwf.Welcome;

