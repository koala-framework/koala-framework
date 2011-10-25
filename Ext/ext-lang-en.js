/**
 * List compiled by mystix on the extjs.com forums.
 * Thank you Mystix!
 *
 * English Translations
 */

if(Ext && Ext.UpdateManager && Ext.UpdateManager.defaults){
    Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">'+trlKwf('Loading...')+'</div>';
}
if(Ext && Ext.View){
  Ext.View.prototype.emptyText = "";
}

if(Ext && Ext.grid.Grid){
  Ext.grid.Grid.prototype.ddText = trlKwf("{0} selected row(s)");
}

if(Ext && Ext.TabPanelItem){
  Ext.TabPanelItem.prototype.closeText = trlKwf("Close this tab");
}

if(Ext && Ext.form.Field){
  Ext.form.Field.prototype.invalidText = trlKwf("The value in this field is invalid");
}

if(Ext && Ext.LoadMask){
  Ext.LoadMask.prototype.msg = trlKwf("Loading...");
}

Date.monthNames = [
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

Date.getShortMonthName = function(month) {
  return Date.monthNames[month].substring(0, 3);
};

Date.monthNumbers = {
  Jan : 0,
  Feb : 1,
  Mar : 2,
  Apr : 3,
  May : 4,
  Jun : 5,
  Jul : 6,
  Aug : 7,
  Sep : 8,
  Oct : 9,
  Nov : 10,
  Dec : 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
};

Date.dayNames = [
  trlKwf("Sunday"),
  trlKwf("Monday"),
  trlKwf("Tuesday"),
  trlKwf("Wednesday"),
  trlKwf("Thursday"),
  trlKwf("Friday"),
  "Saturday"
];

Date.getShortDayName = function(day) {
  return Date.dayNames[day].substring(0, 3);
};

if(Ext && Ext.MessageBox){
  Ext.MessageBox.buttonText = {
    ok     : trlKwf("OK"),
    cancel : trlKwf("Cancel"),
    yes    : trlKwf("Yes"),
    no     : trlKwf("No")
  };
}

//Auskommentiert da es beim Datum Probleme gegeben hat
/*if(Ext.util.Format){
  Ext.util.Format.date = function(v, format){
    if(!v) return "";
    if(!(v instanceof Date)) v = new Date(Date.parse(v));
    return v.dateFormat(format || "m/d/Y");
  };
}*/

if(Ext && Ext.DatePicker){
  Ext.apply(Ext.DatePicker.prototype, {
    todayText         : trlKwf("Today"),
    minText           : trlKwf("This date is before the minimum date"),
    maxText           : trlKwf("This date is after the maximum date"),
    disabledDaysText  : "",
    disabledDatesText : "",
    monthNames        : Date.monthNames,
    dayNames          : Date.dayNames,
    nextText          : trlKwf('Next Month (Control+Right)'),
    prevText          : trlKwf('Previous Month (Control+Left)'),
    monthYearText     : trlKwf('Choose a month (Control+Up/Down to move years)'),
    todayTip          : trlKwf("{0} (Spacebar)"),
    format            : trlKwf("m/d/y"),
    okText            : "&#160;"+trlKwf('OK')+"&#160;",
    cancelText        : trlKwf("Cancel"),
    startDay          : 0
  });
}

if(Ext && Ext.PagingToolbar){
  Ext.apply(Ext.PagingToolbar.prototype, {
    beforePageText : trlKwf("Page"),
    afterPageText  : trlKwf("of {0}"),
    firstText      : trlKwf("First Page"),
    prevText       : trlKwf("Previous Page"),
    nextText       : trlKwf("Next Page"),
    lastText       : trlKwf("Last Page"),
    refreshText    : trlKwf("Refresh"),
    displayMsg     : trlKwf("Displaying {0} - {1} of {2}"),
    emptyMsg       : trlKwf('No data to display')
  });
}

if(Ext && Ext.form.TextField){
  Ext.apply(Ext.form.TextField.prototype, {
    minLengthText : trlKwf("The minimum length for this field is {0}"),
    maxLengthText : trlKwf("The maximum length for this field is {0}"),
    blankText     : trlKwf("This field is required"),
    regexText     : "",
    emptyText     : null
  });
}

if(Ext && Ext.form.NumberField){
  Ext.apply(Ext.form.NumberField.prototype, {
    minText : trlKwf("The minimum value for this field is {0}"),
    maxText : trlKwf("The maximum value for this field is {0}"),
    nanText : trlKwf("{0} is not a valid number")
  });
}

if(Ext && Ext.form.DateField){
  Ext.apply(Ext.form.DateField.prototype, {
    disabledDaysText  : trlKwf("Disabled"),
    disabledDatesText : trlKwf("Disabled"),
    minText           : trlKwf("The date in this field must be after {0}"),
    maxText           : trlKwf("The date in this field must be before {0}"),
    invalidText       : trlKwf("{0} is not a valid date - it must be in the format {1}"),
    format            : trlKwf("m/d/y"),
    altFormats        : "m/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d"
  });
}

if(Ext && Ext.form.ComboBox){
  Ext.apply(Ext.form.ComboBox.prototype, {
    loadingText       : trlKwf("Loading..."),
    valueNotFoundText : undefined
  });
}

if(Ext && Ext.form.VTypes){
  Ext.apply(Ext.form.VTypes, {
    emailText    : trlKwf('This field should be an e-mail address in the format "user@domain.com"'),
    urlText      : trlKwf('This field should be a URL in the format "http:/'+'/www.domain.com"'),
    alphaText    : trlKwf('This field should only contain letters and _'),
    alphanumText : trlKwf('This field should only contain letters, numbers and _')
  });
}

if(Ext && Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : trlKwf('Please enter the URL for the link:'),
    buttonTips : {
      bold : {
        title: trlKwf('Bold (Ctrl+B)'),
        text:  trlKwf('Make the selected text bold.'),
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: trlKwf('Italic (Ctrl+I)'),
        text: trlKwf('Make the selected text italic.'),
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: trlKwf('Underline (Ctrl+U)'),
        text: trlKwf('Underline the selected text.'),
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: trlKwf('Grow Text'),
        text: trlKwf('Increase the font size.'),
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: trlKwf('Shrink Text'),
        text: trlKwf('Decrease the font size.'),
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: trlKwf('Text Highlight Color'),
        text: trlKwf('Change the background color of the selected text.'),
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: trlKwf('Font Color'),
        text: trlKwf('Change the color of the selected text.'),
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: trlKwf('Align Text Left'),
        text: trlKwf('Align text to the left.'),
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: trlKwf('Center Text'),
        text: trlKwf('Center text in the editor.'),
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: trlKwf('Align Text Right'),
        text: trlKwf('Align text to the right.'),
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: trlKwf('Bullet List'),
        text: trlKwf('Start a bulleted list.'),
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: trlKwf('Numbered List'),
        text: trlKwf('Start a numbered list.'),
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: trlKwf('Hyperlink'),
        text: trlKwf('Make the selected text a hyperlink.'),
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: trlKwf('Source Edit'),
        text: trlKwf('Switch to source editing mode.'),
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext && Ext.grid.GridView){
  Ext.apply(Ext.grid.GridView.prototype, {
    sortAscText  : trlKwf("Sort Ascending"),
    sortDescText : trlKwf("Sort Descending"),
    lockText     : trlKwf("Lock Column"),
    unlockText   : trlKwf("Unlock Column"),
    columnsText  : trlKwf("Columns")
  });
}

if(Ext && Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : trlKwf('(None)'),
    groupByText    : trlKwf('Group By This Field'),
    showGroupsText : trlKwf('Show in Groups')
  });
}

if(Ext && Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
    nameText   : trlKwf("Name"),
    valueText  : trlKwf("Value"),
    dateFormat : "m/j/Y"
  });
}

if(Ext && Ext.layout && Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
  Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
    splitTip            : trlKwf("Drag to resize."),
    collapsibleSplitTip : trlKwf("Drag to resize. Double click to hide.")
  });
}
