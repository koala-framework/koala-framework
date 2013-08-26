Kwf.onJContentReady(function() {
    document.getElementById('result').innerHTML += '6 ';
    Ext.get('add').on('click', function() {
        document.getElementById('result').innerHTML = '';
        var childTemplate = new Ext.Template(
            '<div class="onReadyPriorityTestDynamicElement"></div>'
        );
        childTemplate.append(Ext.get('dynamic'));

        var invisibleBlockChildTemplate = new Ext.Template(
            '<div id="block" class="onReadyPriorityTestDynamicInvisibleBlockElement" style="display:none"></div>'
        );
        invisibleBlockChildTemplate.append(Ext.get('dynamic'));

        var invisibleChildTemplate = new Ext.Template(
            '<div id="visible" class="onReadyPriorityTestDynamicInvisibleElement" style="visibility:hidden"></div>'
        );
        invisibleChildTemplate.append(Ext.get('dynamic'));

        Kwf.callOnContentReady(document.body, { newRender: true });
    });

    Ext.get('changeVisibility').on('click', function() {
        document.getElementById('result').innerHTML = '';
        Ext.get('block')
            .setVisible(true);
        Ext.get('visible')
            .setVisible(true);

        Kwf.callOnContentReady(document.body, { newRender: true });
    });
});

Kwf.onJContentReady(function() {
    document.getElementById('result').innerHTML += '1 ';
}, this, {priority: 5});

Kwf.onContentReady(function() {
    document.getElementById('result').innerHTML += '2 ';
}, this, {priority: 4});

Kwf.onJElementReady('.onReadyPriorityTest', function(el) {
    if (el instanceof jQuery) {
        document.getElementById('result').innerHTML += '3 ';
    }
}, this, {priority: 3});

Kwf.onElementReady('.onReadyPriorityTest', function(el, options) {
    if (el instanceof Ext.Element) {
        document.getElementById('result').innerHTML += '4 ';
    }
}, this, {priority: 2});

Kwf.onElementReady('.onReadyPriorityTest', function(el, options) {
    if (el instanceof Ext.Element) {
        document.getElementById('result').innerHTML += '5 ';
    }
}, this, {priority: 1});

Kwf.onJContentReady(function() {
    document.getElementById('result').innerHTML += '7 ';
}, this, {priority: -1});

Kwf.onElementReady('.onReadyPriorityTestDynamicElement', function(el, options) {
    document.getElementById('result').innerHTML += '3 ';
}, this, {priority: 1});

Kwf.onElementReady('.onReadyPriorityTestDynamicInvisibleElement', function(el, options) {
    document.getElementById('result').innerHTML += '5 ';
}, this, {priority: 1, checkVisibility: true});

Kwf.onElementReady('.onReadyPriorityTestDynamicInvisibleBlockElement', function(el, options) {
    document.getElementById('result').innerHTML += '4 ';
}, this, {priority: 2, checkVisibility: true});
