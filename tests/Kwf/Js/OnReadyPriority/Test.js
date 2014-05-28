Kwf.onJContentReady(function() {
    document.getElementById('result').innerHTML += '6 ';
    Ext2.get('add').on('click', function() {
        document.getElementById('result').innerHTML = '';
        var childTemplate = new Ext2.Template(
            '<div class="onReadyPriorityTestDynamicElement"></div>'
        );
        childTemplate.append(Ext2.get('dynamic'));

        var invisibleBlockChildTemplate = new Ext2.Template(
            '<div id="block" class="onReadyPriorityTestDynamicInvisibleBlockElement" style="display:none"></div>'
        );
        invisibleBlockChildTemplate.append(Ext2.get('dynamic'));

        var invisibleChildTemplate = new Ext2.Template(
            '<div id="visible" class="onReadyPriorityTestDynamicInvisibleElement" style="visibility:hidden"></div>'
        );
        invisibleChildTemplate.append(Ext2.get('dynamic'));

        Kwf.callOnContentReady(document.body, { newRender: true });
    });

    Ext2.get('changeVisibility').on('click', function() {
        document.getElementById('result').innerHTML = '';
        Ext2.get('block')
            .setVisible(true);
        Ext2.get('visible')
            .setVisible(true);

        Kwf.callOnContentReady(document.body, { newRender: true });
    });
});

Kwf.onJContentReady(function() {
    document.getElementById('result').innerHTML += '1 ';
}, {priority: 5});

Kwf.onContentReady(function() {
    document.getElementById('result').innerHTML += '2 ';
}, {priority: 4});

Kwf.onJElementReady('.onReadyPriorityTest', function(el) {
    if (el instanceof jQuery) {
        document.getElementById('result').innerHTML += '3 ';
    }
}, {priority: 3});

Kwf.onElementReady('.onReadyPriorityTest', function(el, options) {
    if (el instanceof Ext2.Element) {
        document.getElementById('result').innerHTML += '4 ';
    }
}, {priority: 2});

Kwf.onElementReady('.onReadyPriorityTest', function(el, options) {
    if (el instanceof Ext2.Element) {
        document.getElementById('result').innerHTML += '5 ';
    }
}, {priority: 1});

Kwf.onJContentReady(function() {
    document.getElementById('result').innerHTML += '7 ';
}, {priority: -1});

Kwf.onElementReady('.onReadyPriorityTestDynamicElement', function(el, options) {
    document.getElementById('result').innerHTML += '3 ';
}, {priority: 1});

Kwf.onElementReady('.onReadyPriorityTestDynamicInvisibleElement', function(el, options) {
    document.getElementById('result').innerHTML += '5 ';
}, {priority: 1, checkVisibility: true});

Kwf.onElementReady('.onReadyPriorityTestDynamicInvisibleBlockElement', function(el, options) {
    document.getElementById('result').innerHTML += '4 ';
}, {priority: 2, checkVisibility: true});
