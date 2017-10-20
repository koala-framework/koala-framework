var onReady = require('kwf/commonjs/on-ready');
var historyState = require('kwf/commonjs/history-state');

onReady.onContentReady(function() {
    Ext2.get('testBtn1').on('click', function() {
        historyState.currentState.result = 'sub';
        Ext2.get('result').update(historyState.currentState.result);
        historyState.pushState('asdf', KWF_BASE_URL+'/kwf/test/kwf_js_history-state_test/sub');
    }, this);
    Ext2.get('testBtn2').on('click', function() {
        historyState.currentState.result = 'index';
        Ext2.get('result').update(historyState.currentState.result);
        historyState.pushState('asdf', KWF_BASE_URL+'/kwf/test/kwf_js_history-state_test');
    }, this);


    historyState.currentState.result = Ext2.get('result').dom.innerHTML;
    historyState.updateState();

    historyState.on('popstate', function() {
        Ext2.get('result').update(historyState.currentState.result);
    }, this);
});
