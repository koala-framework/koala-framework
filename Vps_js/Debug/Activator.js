Ext.onReady(function() {
    if (!Vps.isApp) {
        var a = document.createElement('a');
        a.style.backgroundImage = 'url(/assets/silkicons/bug.png)';
        a.style.backgroundRepeat = 'no-repeat';
        a.style.backgroundPosition = 'center';
        a.style.border = '1px solid gray';
        a.style.position = 'absolute';
        a.style.right = '0';
        a.style.top = '0';
        a.style.width = '20px';
        a.style.height = '20px';
        a.style.zIndex = '200';
        a.title = 'Activate Debugging';
        a.href = '/vps/debug/activate?url=' + location.href;
        a.className = 'printHidden';
        document.body.appendChild(a);
    }
});

//for Vps.Menu.Index
Vps.Debug.showActivator = true;

//if we have the activator loaded we always want to show debug messages
//DebugData-Helper doesn't output anything so we set it here
Vps.Debug.displayErrors = true;

Ext.Container.prototype.logXTypesTree = function() {
    var level = 0;
    function foo(c) {
        if(c.items){
            var cs = c.items.items;
            level++;
            for(var i = 0, len = cs.length; i < len; i++){
                console.log(new Array(level).join(' ')+cs[i].getXTypes());
                foo(cs[i]);
            }
            level--;
        }
    }
    foo(this);
};
