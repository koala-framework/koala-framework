
// functions müssen so heißen, werden vom flash aufgerufen
// limitiert zu max einem dogear, mehr macht aber sowieso nicht sinn...
function dogearEnlarge() {
    var dogearSmall = document.getElementById('dogearSmall');
    var dogearBig = document.getElementById('dogearBig');
    dogearBig.style.top = '0';
    dogearSmall.style.top = '-500px';
//     dogearBig.childNodes[0].showLarge();
}

function dogearShrink(){
    var dogearSmall = document.getElementById('dogearSmall');
    var dogearBig = document.getElementById('dogearBig');
    dogearSmall.childNodes[0].showLoop();
    dogearSmall.style.top = '0';
    dogearBig.style.top = '-700px';
}


Ext.onReady(function() {
    Ext.EventManager.addListener(window, 'resize', function() {
        if (Ext.getBody().getWidth() >= 990) {
            document.getElementById('dogearSmall').style.display = 'block';
            document.getElementById('dogearBig').style.display = 'block';
        } else {
            document.getElementById('dogearSmall').style.display = 'none';
            document.getElementById('dogearBig').style.display = 'none';
        }
    });
});
