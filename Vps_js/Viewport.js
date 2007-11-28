Vps.Viewport = Ext.extend(Ext.Viewport,
    initComponent: function
   
        Vps.menu = new Vps.Menu.Index
                    region: 'north
                    height: 
                
        this.items.push(Vps.menu
        this.layout = 'border
        Vps.Viewport.superclass.initComponent.call(this
   
}
