Kwf.onJContentReady(function() {
    document.getElementById('result').innerHTML += '6 ';
});

Kwf.onJContentReady(function() {
    document.getElementById('result').innerHTML += '1 ';
}, this, {priority: 5});

Kwf.onContentReady(function() {
    document.getElementById('result').innerHTML += '2 ';
}, this, {priority: 4});

Kwf.onJElementReady('.onReadyPriorityTest', function() {
    document.getElementById('result').innerHTML += '3 ';
}, this, {priority: 3});

Kwf.onElementReady('.onReadyPriorityTest', function(el, options) {
    document.getElementById('result').innerHTML += '4 ';
}, this, {priority: 2});

Kwf.onElementReady('.onReadyPriorityTest', function(el, options) {
    document.getElementById('result').innerHTML += '5 ';
}, this, {priority: 1});

Kwf.onJContentReady(function() {
    document.getElementById('result').innerHTML += '7 ';
}, this, {priority: -1});
