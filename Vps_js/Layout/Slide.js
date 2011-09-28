//from http://extjs.com/forum/showthread.php?t=43120
Vps.Layout.Slide = Ext.extend(Ext.layout.CardLayout, {

    easing: 'none',
    duration: .5,
    opacity: 1,

    setActiveItem : function(itemInt){
        if (typeof(itemInt) == 'string') { itemInt = this.container.items.keys.indexOf(itemInt); }
        else if (typeof(itemInt) == 'object') { itemInt = this.container.items.items.indexOf(itemInt); }
        var item = this.container.getComponent(itemInt);
        if(this.activeItem != item){
            if(this.activeItem){
                if(item && (!item.rendered || !this.isValidParent(item, this.container))){
                    this.renderItem(item, itemInt, this.container.getLayoutTarget()); item.show();
                }
                var s = [this.container.body.getX() - this.container.body.getWidth(), this.container.body.getX() + this.container.body.getWidth()];
                this.activeItem.el.shift({ duration: this.duration, easing: this.easing, opacity: this.opacity, x:(this.activeItemNo < itemInt ? s[0] : s[1] )});
                item.el.setY(this.container.body.getY());
                item.el.setX((this.activeItemNo < itemInt ? s[1] : s[0] ));
                item.el.shift({ duration: this.duration, easing: this.easing, opacity: 1, x:this.container.body.getX()});
            }
            this.activeItemNo = itemInt;
            this.activeItem = item;
            this.layout();
        }
    }
});
Ext.Container.LAYOUTS['slide'] = Vps.Layout.Slide;
