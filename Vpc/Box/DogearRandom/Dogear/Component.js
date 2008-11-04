
// functions müssen so heißen, werden vom flash aufgerufen
// limitiert zu max einem dogear, mehr macht aber sowieso nicht sinn...
function dogearEnlarge() {
    var dogearSmall = document.getElementById('dogearSmall');
    var dogearBig = document.getElementById('dogearBig');
    dogearSmall.style.top = '-500px';
    dogearBig.style.top = '0';
//     dogearBig.childNodes[0].showLarge();
}

function dogearShrink(){
    var dogearSmall = document.getElementById('dogearSmall');
    var dogearBig = document.getElementById('dogearBig');
    dogearSmall.childNodes[0].showLoop();
    dogearSmall.style.top = '0';
    dogearBig.style.top = '-700px';
}