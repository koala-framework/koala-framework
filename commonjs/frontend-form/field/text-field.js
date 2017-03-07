var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');

var TextField = kwfExtend(Field, {
});

fieldRegistry.register('kwfFormFieldTextField', TextField);
module.exports = TextField;
