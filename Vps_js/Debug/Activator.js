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
        document.body.appendChild(a);
    }
});
Vps.Debug.showActivator = true;
