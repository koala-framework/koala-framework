Kwf.onContentReady(function() {
    Ext.get('testBtn1').on('click', function() {
        Kwf.Utils.HistoryState.currentState.result = 'sub';
        Ext.get('result').update(Kwf.Utils.HistoryState.currentState.result);
        Kwf.Utils.HistoryState.pushState('asdf', '/kwf/test/kwf_js_history-state_test/sub');
    }, this);
    Ext.get('testBtn2').on('click', function() {
        Kwf.Utils.HistoryState.currentState.result = 'index';
        Ext.get('result').update(Kwf.Utils.HistoryState.currentState.result);
        Kwf.Utils.HistoryState.pushState('asdf', '/kwf/test/kwf_js_history-state_test');
    }, this);


    Kwf.Utils.HistoryState.currentState.result = Ext.get('result').dom.innerHTML;
    Kwf.Utils.HistoryState.updateState();

    Kwf.Utils.HistoryState.on('popstate', function() {
        Ext.get('result').update(Kwf.Utils.HistoryState.currentState.result);
    }, this);
});
