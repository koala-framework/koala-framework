Ext2.namespace('Kwf.Test');

Kwf.Test.ConnectionsError = Ext2.extend(Ext2.Panel, {
    html: 'test',
    id: 'blub',
    initComponent: function()
    {
        Kwf.Debug.displayErrors = false;
        this.buttons = [];
        this.buttons.push(
            new Ext2.Button({
            text:'testA',
            handler : function(){
                Ext2.Ajax.request({
                    timeout: 1000,
                    params: {test:1},
                    errorText: 'foo1',
                    url: KWF_BASE_URL+'/kwf/test/kwf_connection_test/json-timeout',
                    failure: function() {
                        this.el.insertHtml('beforeBegin', "<div id=\"abort\">abort</div>");
                    },
                    success: function() {
                        this.el.overwrite('<div id="testa">success</div>');
                    },
                    scope: this
               });
            },
            scope: this
        }));
        this.buttons.push(
            new Ext2.Button({
            text:'testC',
            handler : function(){
                Kwf.Debug.displayErrors = true;
                Ext2.Ajax.request({
                    timeout: 1000,
                    params: {test:1},
                    errorText: 'timeoutError',
                    url: KWF_BASE_URL+'/kwf/test/kwf_connection_test/json-timeout',
                    failure: function() {
                        this.el.insertHtml('beforeBegin', "<div id=\"aborttimeout\">aborttimeout</div>");
                    },
                    success: function() {
                        this.el.insertHtml('beforeBegin', "<div id=\"success\">success</div>");
                    },
                    scope: this
               });
                Ext2.Ajax.request({
                    timeout: 1000,
                    params: {test:2},
                    errorText: 'exceptionError',
                    url: KWF_BASE_URL+'/kwf/test/kwf_connection_test/json-exception',
                    failure: function() {
                        this.el.insertHtml('beforeBegin', "<div id=\"abortexception\">abortexception</div>");
                    },
                    success: function() {
                        this.el.insertHtml('beforeBegin', "<div id=\"success\">success</div>");
                    },
                    scope: this
               });
            },
            scope: this
        }));
        this.buttons.push(
            new Ext2.Button({
            text:'testD',
            handler : function(){
                Kwf.Debug.displayErrors = true;
                Ext2.Ajax.request({
                    params: {test:1},
                    url: KWF_BASE_URL+'/kwf/test/kwf_connection_test/json-real-exception',
                    failure: function() {
                        this.el.insertHtml('beforeBegin', "<div id=\"abort\">abort</div>");
                    },
                    success: function() {
                        this.el.insertHtml('beforeBegin', "<div id=\"success\">success</div>");
                    },
                    scope: this
               });
            },
            scope: this
        }));
        Kwf.Test.ConnectionsError.superclass.initComponent.call(this);
    }
});
