/* Based on ext-lang-en.js from ext4 with trlKwf() calls added where appropriate */
Ext4.onReady(function() {

    if (Ext4.data && Ext4.data.Types) {
        Ext4.data.Types.stripRe = /[\$,%]/g;
    }

    if (Ext4.Date) {
        Ext4.Date.monthNames = [
            trlKwf("January"),
            trlKwf("February"),
            trlKwf("March"),
            trlKwf("April"),
            trlKwf("May"),
            trlKwf("June"),
            trlKwf("July"),
            trlKwf("August"),
            trlKwf("September"),
            trlKwf("October"),
            trlKwf("November"),
            trlKwf("December")
        ];


        Ext4.Date.getShortMonthName = function(month) {
            return Ext4.Date.monthNames[month].substring(0, 3);
        };

        Ext4.Date.monthNumbers = {
            Jan: 0,
            Feb: 1,
            Mar: 2,
            Apr: 3,
            May: 4,
            Jun: 5,
            Jul: 6,
            Aug: 7,
            Sep: 8,
            Oct: 9,
            Nov: 10,
            Dec: 11
        };

        Ext4.Date.getMonthNumber = function(name) {
            return Ext4.Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
        };

        Ext4.Date.dayNames = [
            trlKwf("Sunday"),
            trlKwf("Monday"),
            trlKwf("Tuesday"),
            trlKwf("Wednesday"),
            trlKwf("Thursday"),
            trlKwf("Friday"),
            trlKwf("Saturday")
        ];

        Ext4.Date.getShortDayName = function(day) {
            return Ext4.Date.dayNames[day].substring(0, 3);
        };

        Ext4.Date.parseCodes.S.s = "(?:st|nd|rd|th)";
    }

    if (Ext4.util && Ext4.util.Format) {
        Ext4.apply(Ext4.util.Format, {
            thousandSeparator: '', //shitty trl parser doesn't eat that: trlcKwf('thousands separator', ","),
            decimalSeparator: trlcKwf('decimal separator', "."),
            currencySign: '€', //currencySign in trl, really?
            dateFormat: trlKwf('Y-m-d')
        });
    }
});

Ext4.define("Ext.locale.en.view.View", {
    override: "Ext.view.View",
    emptyText: ""
});

Ext4.define("Ext.locale.en.grid.plugin.DragDrop", {
    override: "Ext.grid.plugin.DragDrop",
    dragText: trlKwf("{0} selected row{1}")
});

// changing the msg text below will affect the LoadMask
Ext4.define("Ext.locale.en.view.AbstractView", {
    override: "Ext.view.AbstractView",
    loadingText: trlKwf("Loading...")
});

Ext4.define("Ext.locale.en.picker.Date", {
    override: "Ext.picker.Date",
    todayText: trlKwf("Today"),
    minText: trlKwf("This date is before the minimum date"),
    maxText: trlKwf("This date is after the maximum date"),
    disabledDaysText: "",
    disabledDatesText: "",
    nextText: trlKwf('Next Month (Control+Right)'),
    prevText: trlKwf('Previous Month (Control+Left)'),
    monthYearText: trlKwf('Choose a month (Control+Up/Down to move years)'),
    todayTip: trlKwf("{0} (Spacebar)"),
    format: trlKwf("m/d/y"),
    startDay: parseInt(trlcKwf('start day of week', '0'))
});

Ext4.define("Ext.locale.en.picker.Month", {
    override: "Ext.picker.Month",
    okText: "&#160;"+trlKwf('OK')+"&#160;",
    cancelText: trlKwf("Cancel")
});

Ext4.define("Ext.locale.en.toolbar.Paging", {
    override: "Ext.PagingToolbar",
    beforePageText: trlKwf("Page"),
    afterPageText: trlKwf("of {0}"),
    firstText: trlKwf("First Page"),
    prevText: trlKwf("Previous Page"),
    nextText: trlKwf("Next Page"),
    lastText: trlKwf("Last Page"),
    refreshText: trlKwf("Refresh"),
    displayMsg: trlKwf("Displaying {0} - {1} of {2}"),
    emptyMsg: trlKwf('No data to display')
});

Ext4.define("Ext.locale.en.form.Basic", {
    override: "Ext.form.Basic",
    waitTitle: trlKwf("Please Wait...")
});

Ext4.define("Ext.locale.en.form.field.Base", {
    override: "Ext.form.field.Base",
    invalidText: trlKwf("The value in this field is invalid")
});

Ext4.define("Ext.locale.en.form.field.Text", {
    override: "Ext.form.field.Text",
    minLengthText: trlKwf("The minimum length for this field is {0}"),
    maxLengthText: trlKwf("The maximum length for this field is {0}"),
    blankText: trlKwf("This field is required"),
    regexText: "",
    emptyText: null
});

Ext4.define("Ext.locale.en.form.field.Number", {
    override: "Ext.form.field.Number",
    decimalSeparator: trlcKwf('decimal separator', "."),
    decimalPrecision: 2,
    minText: trlKwf("The minimum value for this field is {0}"),
    maxText: trlKwf("The maximum value for this field is {0}"),
    nanText: trlKwf("{0} is not a valid number")
});

Ext4.define("Ext.locale.en.form.field.Date", {
    override: "Ext.form.field.Date",
    disabledDaysText: trlKwf("Disabled"),
    disabledDatesText: trlKwf("Disabled"),
    minText: trlKwf("The date in this field must be after {0}"),
    maxText: trlKwf("The date in this field must be before {0}"),
    invalidText: trlKwf("{0} is not a valid date - it must be in the format {1}"),
    format: trlKwf("Y-m-d"),
    altFormats: "m/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d",
    startDay: parseInt(trlcKwf('start day of week', '0'))
});

Ext4.define("Ext.locale.en.form.field.ComboBox", {
    override: "Ext.form.field.ComboBox",
    valueNotFoundText: undefined
}, function() {
    Ext4.apply(Ext4.form.field.ComboBox.prototype.defaultListConfig, {
        loadingText: trlKwf("Loading...")
    });
});

Ext4.define("Ext.locale.en.form.field.VTypes", {
    override: "Ext.form.field.VTypes",
    emailText: trlKwf('This field should be an e-mail address in the format "user@example.com"'),
    urlText: trlKwf('This field should be a URL in the format "http:/' + '/www.example.com"'),
    alphaText: trlKwf('This field should only contain letters and _'),
    alphanumText: trlKwf('This field should only contain letters, numbers and _')
});

Ext4.define("Ext.locale.en.form.field.HtmlEditor", {
    override: "Ext.form.field.HtmlEditor",
    createLinkText: trlKwf('Please enter the URL for the link:')
}, function() {
    Ext4.apply(Ext4.form.field.HtmlEditor.prototype, {
        buttonTips: {
            bold: {
                title: trlKwf('Bold (Ctrl+B)'),
                text: trlKwf('Make the selected text bold.'),
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            italic: {
                title: trlKwf('Italic (Ctrl+I)'),
                text: trlKwf('Make the selected text italic.'),
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            underline: {
                title: trlKwf('Underline (Ctrl+U)'),
                text: trlKwf('Underline the selected text.'),
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            /*
            increasefontsize: {
                title: 'Grow Text'),
                text: 'Increase the font size.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            decreasefontsize: {
                title: 'Shrink Text',
                text: 'Decrease the font size.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            backcolor: {
                title: 'Text Highlight Color',
                text: 'Change the background color of the selected text.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            forecolor: {
                title: 'Font Color',
                text: 'Change the color of the selected text.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            justifyleft: {
                title: 'Align Text Left',
                text: 'Align text to the left.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            justifycenter: {
                title: 'Center Text',
                text: 'Center text in the editor.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            justifyright: {
                title: 'Align Text Right',
                text: 'Align text to the right.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            */
            insertunorderedlist: {
                title: trlKwf('Bullet List'),
                text: trlKwf('Start a bulleted list.'),
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            insertorderedlist: {
                title: trlKwf('Numbered List'),
                text: trlKwf('Start a numbered list.'),
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            createlink: {
                title: trlKwf('Hyperlink'),
                text: trlKwf('Make the selected text a hyperlink.'),
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            sourceedit: {
                title: trlKwf('Source Edit'),
                text: trlKwf('Switch to source editing mode.'),
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            }
        }
    });
});

Ext4.define("Ext.locale.en.grid.header.Container", {
    override: "Ext.grid.header.Container",
    sortAscText: trlKwf("Sort Ascending"),
    sortDescText: trlKwf("Sort Descending"),
    columnsText: trlKwf("Columns")
});

Ext4.define("Ext.locale.en.grid.GroupingFeature", {
    override: "Ext.grid.GroupingFeature",
    emptyGroupText: trlKwf('(None)'),
    groupByText: trlKwf('Group By This Field'),
    showGroupsText: trlKwf('Show in Groups')
});

Ext4.define("Ext.locale.en.grid.PropertyColumnModel", {
    override: "Ext.grid.PropertyColumnModel",
    nameText: trlKwf("Name"),
    valueText: trlKwf("Value"),
    dateFormat: trlKwf("Y-m-d"),
    trueText: trlKwf("true"),
    falseText: trlKwf("false")
});

Ext4.define("Ext.locale.en.grid.BooleanColumn", {
    override: "Ext.grid.BooleanColumn",
    trueText: trlKwf("true"),
    falseText: trlKwf("false"),
    undefinedText: '&#160;'
});

Ext4.define("Ext.locale.en.grid.NumberColumn", {
    override: "Ext.grid.NumberColumn",
    format: trlKwf('0,000.00')
});

Ext4.define("Ext.locale.en.grid.DateColumn", {
    override: "Ext.grid.DateColumn",
    format: trlKwf('Y-m-d')
});

Ext4.define("Ext.locale.en.form.field.Time", {
    override: "Ext.form.field.Time",
    minText: trlKwf("The time in this field must be equal to or after {0}"),
    maxText: trlKwf("The time in this field must be equal to or before {0}"),
    invalidText: trlKwf("{0} is not a valid time"),
    format: trlKwf("H:i"),
    altFormats: "g:ia|g:iA|g:i a|g:i A|h:i|g:i|H:i|ga|ha|gA|h a|g a|g A|gi|hi|gia|hia|g|H"
});

Ext4.define("Ext.locale.en.form.CheckboxGroup", {
    override: "Ext.form.CheckboxGroup",
    blankText: trlKwf("You must select at least one item in this group")
});

Ext4.define("Ext.locale.en.form.RadioGroup", {
    override: "Ext.form.RadioGroup",
    blankText: trlKwf("You must select one item in this group")
});

Ext4.define("Ext.locale.en.window.MessageBox", {
    override: "Ext.window.MessageBox",
    buttonText: {
        ok: trlKwf("OK"),
        cancel: trlKwf("Cancel"),
        yes: trlKwf("Yes"),
        no: trlKwf("No")
    }
});

// This is needed until we can refactor all of the locales into individual files
Ext4.define("Ext.locale.en.Component", {
    override: "Ext.Component"
});

