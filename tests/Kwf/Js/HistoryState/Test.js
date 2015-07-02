var onReady = require('kwf/on-ready');

onReady.onContentReady(function() {
    Ext2.get('testBtn1').on('click', function() {
        Kwf.Utils.HistoryState.currentState.result = 'sub';
        Ext2.get('result').update(Kwf.Utils.HistoryState.currentState.result);
        Kwf.Utils.HistoryState.pushState('asdf', '/kwf/test/kwf_js_history-state_test/sub');
    }, this);
    Ext2.get('testBtn2').on('click', function() {
        Kwf.Utils.HistoryState.currentState.result = 'index';
        Ext2.get('result').update(Kwf.Utils.HistoryState.currentState.result);
        Kwf.Utils.HistoryState.pushState('asdf', '/kwf/test/kwf_js_history-state_test');
    }, this);


    Kwf.Utils.HistoryState.currentState.result = Ext2.get('result').dom.innerHTML;
    Kwf.Utils.HistoryState.updateState();

    Kwf.Utils.HistoryState.on('popstate', function() {
        Ext2.get('result').update(Kwf.Utils.HistoryState.currentState.result);
    }, this);
});
