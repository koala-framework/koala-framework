var $ = require('jquery');
var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');
var onReady = require('kwf/commonjs/on-ready');
require('kwf/commonjs/frontend-form/field/multi-checkbox.css');

onReady.onRender('.kwfFormFieldMultiCheckbox', function multiCheckbox(mc) {
        var checkAll = mc.find('a.kwfMultiCheckboxCheckAll');
        var checkNone = mc.find('a.kwfMultiCheckboxCheckNone');

        if (checkAll) {
            checkAll.on('click', (function(ev) {
                ev.preventDefault();
                mc.find('input[type=checkbox]').prop('checked', true);
            }));
        }
        if (checkNone) {
            checkNone.on('click', (function(ev) {
                ev.preventDefault();
                mc.find('input[type=checkbox]').prop('checked', false);
            }));
        }
}, { defer: true });

var MultiCheckbox = kwfExtend(Field, {
    initField: function() {
    },
    getValue: function() { //has no value itself
        return null;
    },
    clearValue: function() {
    },
    setValue: function() {
    },
    getFieldName: function() {
        return this.el.find('.kwfFormFieldMultiCheckbox').get(0).getAttribute('data-fieldname');
    }
});

fieldRegistry.register('kwfFormFieldMultiCheckbox', MultiCheckbox);
module.exports = MultiCheckbox;

