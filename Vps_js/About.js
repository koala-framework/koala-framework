Vps.About = Ext.extend(Ext.Window, {
    initComponent: function() {
        this.title = 'About';
        this.width = 350;
        this.height = 200;
        this.resizable = false;
        this.layout = 'fit';
        this.modal = true;
        this.items = [new Ext.Panel({
            cls: 'vps-about',
            autoLoad: '/vps/user/about/content'
        })];
        Vps.About.superclass.initComponent.call(this);
    }
});
