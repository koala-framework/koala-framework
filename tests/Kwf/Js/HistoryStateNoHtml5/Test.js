var onReady = require('kwf/commonjs/on-ready');
var historyState = require('kwf/commonjs/history-state');

onReady.onContentReady(function() {
    Ext2.get('testBtn1').on('click', function() {
        historyState.currentState.result = 'sub';
        Ext2.get('result').update(historyState.currentState.result);
        //console.log('bnt1', historyState.currentState);
        historyState.pushState('asdf', KWF_BASE_URL+'/kwf/test/kwf_js_history-state-no-html5_test/sub');
    }, this);
    Ext2.get('testBtn2').on('click', function() {
        historyState.currentState.result = 'index';
        Ext2.get('result').update(historyState.currentState.result);
        //console.log('bnt2', historyState.currentState);
        historyState.pushState('asdf', KWF_BASE_URL+'/kwf/test/kwf_js_history-state-no-html5_test');
    }, this);

    historyState.currentState.result = Ext2.get('result').dom.innerHTML;
    historyState.updateState();
    //console.log('init', historyState.currentState);

    historyState.on('popstate', function() {
        //console.log('popstate', historyState.currentState);
        Ext2.get('result').update(historyState.currentState.result);
    }, this);
});
