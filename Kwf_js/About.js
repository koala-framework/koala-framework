Kwf.About = Ext.extend(Ext.Window, {
    initComponent: function() {
        this.title = 'About';
        this.width = 350;
        this.height = 200;
        this.resizable = false;
        this.layout = 'fit';
        this.modal = true;
        this.items = [new Ext.Panel({
            cls: 'kwf-about',
            autoLoad: '/kwf/user/about/content'
        })];
        Kwf.About.superclass.initComponent.call(this);
    }
});
