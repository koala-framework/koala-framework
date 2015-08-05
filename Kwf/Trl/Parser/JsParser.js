var esprima = require('esprima');
var fs = require('fs');
var pathModule = require('path');
process.stdin.setEncoding('utf8');

var ERROR_WRONG_NR_OF_ARGUMENTS = 'wrongNrOfArguments';
var ERROR_WRONG_ARGUMENT_TYPE = 'wrongArgumentType';

var translations = [];

var recursiveCheckForTrl = function(node, translations, contentLines) {
    var key, child, calledFunction;
    if (node.type == 'CallExpression') {
        calledFunction = node.callee.name;

        // Get original source code via start and end
        var lines = [];
        for (var i = node.loc.start.line-1; i < node.loc.end.line; i++) {
            var startIndex = 0;
            if (i == node.loc.start.line-1) {
                startIndex = node.loc.start.column;
            }
            var endIndex = contentLines[i].length;
            if (i == node.loc.end.line-1) {
                endIndex = node.loc.end.column;
            }
            lines.push(contentLines[i].slice(startIndex, endIndex));
        }
        var rawCode = lines.join("\n");

        var translation = {
            linenr: node.loc.start.line,
            before: rawCode,
            source: '',
            type: null,
            error_short: null,
            text: null,
            plural: null,
            context: null
        };
        if (calledFunction == 'trlKwf' || calledFunction == 'trl') {
            if (node.arguments.length != 1 && node.arguments.length != 2) { // singular[, variables]
                translation.error_short = ERROR_WRONG_NR_OF_ARGUMENTS
            } else if (node.arguments[0].type != 'Literal') {
                translation.error_short = ERROR_WRONG_ARGUMENT_TYPE;
            } else {
                translation.type = 'trl';
                translation.text = node.arguments[0].value;
                translation.source = node.callee.name.indexOf('Kwf') > -1 ? 'kwf' : 'web';
            }
        } else if (calledFunction == 'trlpKwf' || calledFunction == 'trlp') {
            if (node.arguments.length != 3) { // singular, plural, variables
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
            if (node.arguments.length != 2 && node.arguments.length != 3) { // context, singular[, variables]
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
            if (node.arguments.length != 4) { // context, singular, plural, variables
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
                recursiveCheckForTrl(child, translations, contentLines);
            }
        }
    }
}

var parseContent = function(content, translations, contentLines) {
    recursiveCheckForTrl(esprima.parse(content, {loc: true}), translations, contentLines);
};

var content = '';
process.stdin.resume();
process.stdin.on('data', function (buf) { content += buf.toString(); });
process.stdin.on('end', function() {
    var contentLines = content.split("\n");
    parseContent(content, translations, contentLines);
    console.log(JSON.stringify(translations));
    process.exit(0);
});
