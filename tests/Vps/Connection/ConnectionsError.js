Ext.namespace('Vps.Test');

Vps.Test.ConnectionsError = Ext.extend(Ext.Panel, {
    html: 'test<div id="log" style="color:red"></log>',
    id: 'blub',
    initComponent: function()
    {
        Vps.Debug.displayErrors = false;
        this.buttons = [];
        this.buttons.push(
            new Ext.Button({
            text:'testA',
            handler : function(){
                Ext.Ajax.request({
                    timeout: 1000,
                    params: {test:1},
                    errorText: 'foo1',
                    url: '/vps/test/vps_connection_test/json-timeout',
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
            new Ext.Button({
            text:'testB',
            handler : function(){
                Ext.Ajax.request({
                    timeout: 1000,
                    params: {test:1},
                    url: '/vps/test/vps_connection_test/json-success',
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
        this.buttons.push(
            new Ext.Button({
            text:'testC',
            handler : function(){
                Vps.Debug.displayErrors = true;
                Ext.Ajax.request({
                    timeout: 1000,
                    params: {test:1},
                    errorText: 'timeoutError',
                    url: '/vps/test/vps_connection_test/json-timeout',
                    failure: function() {
                        this.el.insertHtml('beforeBegin', "<div id=\"aborttimeout\">aborttimeout</div>");
                    },
                    success: function() {
                        this.el.insertHtml('beforeBegin', "<div id=\"success\">success</div>");
                    },
                    scope: this
               });
                Ext.Ajax.request({
                    timeout: 1000,
                    params: {test:2},
                    errorText: 'exceptionError',
                    url: '/vps/test/vps_connection_test/json-exception',
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
            new Ext.Button({
            text:'testD',
            handler : function(){
                Vps.Debug.displayErrors = true;
                Ext.Ajax.request({
                    timeout: 1000,
                    params: {test:1},
                    url: '/vps/test/vps_connection_test/json-real-exception',
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
        Vps.Test.ConnectionsError.superclass.initComponent.call(this);
    }
});


 /* */