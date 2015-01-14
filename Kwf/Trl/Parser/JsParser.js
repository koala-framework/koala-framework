var esprima = require('esprima');
var fs = require('fs');
var pathModule = require('path');


var args = process.argv.slice(2);
var content = args[0];

var ERROR_WRONG_NR_OF_ARGUMENTS = 'wrongNrOfArguments';
var ERROR_WRONG_ARGUMENT_TYPE = 'wrongArgumentType';

var translations = [];

var recursiveCheckForTrl = function(node, translations) {
    var key, child, calledFunction;
    if (node.type == 'CallExpression') {
        calledFunction = node.callee.name;
        var translation = {
            linenr: node.loc.start.line,
            source: '',
            type: null,
            error_short: null,
            text: null,
            plural: null,
            context: null
        };
        if (calledFunction == 'trlKwf' || calledFunction == 'trl') {
            if (node.arguments.length != 1 && node.arguments.length != 2) {
                translation.error_short = ERROR_WRONG_NR_OF_ARGUMENTS
            } else if (node.arguments[0].type != 'Literal') {
                translation.error_short = ERROR_WRONG_ARGUMENT_TYPE;
            } else {
                translation.type = 'trl';
                translation.text = node.arguments[0].value;
                translation.source = node.callee.name.indexOf('Kwf') > -1 ? 'kwf' : 'web';
            }
        } else if (calledFunction == 'trlpKwf' || calledFunction == 'trlp') {
            if (node.arguments.length != 3 && node.arguments.length != 4) {
                translation.error_short = ERROR_WRONG_NR_OF_ARGUMENTS
            } else if (node.arguments[0].type != 'Literal' || node.arguments[1].type != 'Literal') {
                translation.error_short = ERROR_WRONG_ARGUMENT_TYPE;
            } else {
                translation.type = 'trlp';
                translation.text = node.arguments[0].value;
                translation.plural = node.arguments[1].value;
                translation.source = node.callee.name.indexOf('Kwf') > -1 ? 'kwf' : 'web';
            }
        } else if (calledFunction == 'trlcKwf' || calledFunction == 'trlc') {
            if (node.arguments.length != 2 && arguments.length != 3) {
                translation.error_short = ERROR_WRONG_NR_OF_ARGUMENTS
            } else if ((node.arguments[0].type != 'Literal' || node.arguments[1].type != 'Literal')) {
                translation.error_short = ERROR_WRONG_ARGUMENT_TYPE;
            } else {
                translation.type = 'trlc';
                translation.context = node.arguments[0].value;
                translation.text = node.arguments[1].value;
                translation.source = node.callee.name.indexOf('Kwf') > -1 ? 'kwf' : 'web';
            }
        } else if (calledFunction == 'trlcpKwf' || calledFunction == 'trlcp') {
            if (node.arguments.length != 4 && node.arguments.length != 5) {
                translation.error_short = ERROR_WRONG_NR_OF_ARGUMENTS
            } else if (node.arguments[0].type != 'Literal'
                || node.arguments[1].type != 'Literal'
                || node.arguments[2].type != 'Literal'
            ) {
                translation.error_short = ERROR_WRONG_ARGUMENT_TYPE;
            } else {
                translation.type = 'trlcp';
                translation.context = node.arguments[0].value;
                translation.text = node.arguments[1].value;
                translation.plural = node.arguments[2].value;
                translation.source = node.callee.name.indexOf('Kwf') > -1 ? 'kwf' : 'web';
            }
        }
        if (translation.type) {
            translations.push(translation);
        }
    }
    for (key in node) {
        if (node.hasOwnProperty(key)) {
            child = node[key];
            if (typeof child === 'object' && child !== null) {
                recursiveCheckForTrl(child, translations);
            }
        }
    }
}

var parseContent = function(content, translations) {
    recursiveCheckForTrl(esprima.parse(content, {loc: true}), translations);
};

parseContent(content, translations);

console.log(JSON.stringify(translations));
process.exit(0);
