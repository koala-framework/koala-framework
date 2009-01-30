/**
 * List compiled by mystix on the extjs.com forums.
 * Thank you Mystix!
 *
 * English Translations
 */

if(Ext && Ext.UpdateManager && Ext.UpdateManager.defaults){
    Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">'+trlVps('Loading...')+'</div>';
}
if(Ext && Ext.View){
  Ext.View.prototype.emptyText = "";
}

if(Ext && Ext.grid.Grid){
  Ext.grid.Grid.prototype.ddText = trlVps("{0} selected row(s)");
}

if(Ext && Ext.TabPanelItem){
  Ext.TabPanelItem.prototype.closeText = trlVps("Close this tab");
}

if(Ext && Ext.form.Field){
  Ext.form.Field.prototype.invalidText = trlVps("The value in this field is invalid");
}

if(Ext && Ext.LoadMask){
  Ext.LoadMask.prototype.msg = trlVps("Loading...");
}

Date.monthNames = [
  trlVps("January"),
  trlVps("February"),
  trlVps("March"),
  trlVps("April"),
  trlVps("May"),
  trlVps("June"),
  trlVps("July"),
  trlVps("August"),
  trlVps("September"),
  trlVps("October"),
  trlVps("November"),
  trlVps("December")
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
  trlVps("Sunday"),
  trlVps("Monday"),
  trlVps("Tuesday"),
  trlVps("Wednesday"),
  trlVps("Thursday"),
  trlVps("Friday"),
  "Saturday"
];

Date.getShortDayName = function(day) {
  return Date.dayNames[day].substring(0, 3);
};

if(Ext && Ext.MessageBox){
  Ext.MessageBox.buttonText = {
    ok     : trlVps("OK"),
    cancel : trlVps("Cancel"),
    yes    : trlVps("Yes"),
    no     : trlVps("No")
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
    todayText         : trlVps("Today"),
    minText           : trlVps("This date is before the minimum date"),
    maxText           : trlVps("This date is after the maximum date"),
    disabledDaysText  : "",
    disabledDatesText : "",
    monthNames        : Date.monthNames,
    dayNames          : Date.dayNames,
    nextText          : trlVps('Next Month (Control+Right)'),
    prevText          : trlVps('Previous Month (Control+Left)'),
    monthYearText     : trlVps('Choose a month (Control+Up/Down to move years)'),
    todayTip          : trlVps("{0} (Spacebar)"),
    format            : trlVps("m/d/y"),
    okText            : "&#160;"+trlVps('OK')+"&#160;",
    cancelText        : trlVps("Cancel"),
    startDay          : 0
  });
}

if(Ext && Ext.PagingToolbar){
  Ext.apply(Ext.PagingToolbar.prototype, {
    beforePageText : trlVps("Page"),
    afterPageText  : trlVps("of {0}"),
    firstText      : trlVps("First Page"),
    prevText       : trlVps("Previous Page"),
    nextText       : trlVps("Next Page"),
    lastText       : trlVps("Last Page"),
    refreshText    : trlVps("Refresh"),
    displayMsg     : trlVps("Displaying {0} - {1} of {2}"),
    emptyMsg       : trlVps('No data to display')
  });
}

if(Ext && Ext.form.TextField){
  Ext.apply(Ext.form.TextField.prototype, {
    minLengthText : trlVps("The minimum length for this field is {0}"),
    maxLengthText : trlVps("The maximum length for this field is {0}"),
    blankText     : trlVps("This field is required"),
    regexText     : "",
    emptyText     : null
  });
}

if(Ext && Ext.form.NumberField){
  Ext.apply(Ext.form.NumberField.prototype, {
    minText : trlVps("The minimum value for this field is {0}"),
    maxText : trlVps("The maximum value for this field is {0}"),
    nanText : trlVps("{0} is not a valid number")
  });
}

if(Ext && Ext.form.DateField){
  Ext.apply(Ext.form.DateField.prototype, {
    disabledDaysText  : trlVps("Disabled"),
    disabledDatesText : trlVps("Disabled"),
    minText           : trlVps("The date in this field must be after {0}"),
    maxText           : trlVps("The date in this field must be before {0}"),
    invalidText       : trlVps("{0} is not a valid date - it must be in the format {1}"),
    format            : trlVps("m/d/y"),
    altFormats        : "m/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d"
  });
}

if(Ext && Ext.form.ComboBox){
  Ext.apply(Ext.form.ComboBox.prototype, {
    loadingText       : trlVps("Loading..."),
    valueNotFoundText : undefined
  });
}

if(Ext && Ext.form.VTypes){
  Ext.apply(Ext.form.VTypes, {
    emailText    : trlVps('This field should be an e-mail address in the format "user@domain.com"'),
    urlText      : trlVps('This field should be a URL in the format "http:/'+'/www.domain.com"'),
    alphaText    : trlVps('This field should only contain letters and _'),
    alphanumText : trlVps('This field should only contain letters, numbers and _')
  });
}

if(Ext && Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : trlVps('Please enter the URL for the link:'),
    buttonTips : {
      bold : {
        title: trlVps('Bold (Ctrl+B)'),
        text:  trlVps('Make the selected text bold.'),
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: trlVps('Italic (Ctrl+I)'),
        text: trlVps('Make the selected text italic.'),
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: trlVps('Underline (Ctrl+U)'),
        text: trlVps('Underline the selected text.'),
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: trlVps('Grow Text'),
        text: trlVps('Increase the font size.'),
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: trlVps('Shrink Text'),
        text: trlVps('Decrease the font size.'),
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: trlVps('Text Highlight Color'),
        text: trlVps('Change the background color of the selected text.'),
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: trlVps('Font Color'),
        text: trlVps('Change the color of the selected text.'),
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: trlVps('Align Text Left'),
        text: trlVps('Align text to the left.'),
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: trlVps('Center Text'),
        text: trlVps('Center text in the editor.'),
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: trlVps('Align Text Right'),
        text: trlVps('Align text to the right.'),
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: trlVps('Bullet List'),
        text: trlVps('Start a bulleted list.'),
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: trlVps('Numbered List'),
        text: trlVps('Start a numbered list.'),
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: trlVps('Hyperlink'),
        text: trlVps('Make the selected text a hyperlink.'),
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: trlVps('Source Edit'),
        text: trlVps('Switch to source editing mode.'),
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext && Ext.grid.GridView){
  Ext.apply(Ext.grid.GridView.prototype, {
    sortAscText  : trlVps("Sort Ascending"),
    sortDescText : trlVps("Sort Descending"),
    lockText     : trlVps("Lock Column"),
    unlockText   : trlVps("Unlock Column"),
    columnsText  : trlVps("Columns")
  });
}

if(Ext && Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : trlVps('(None)'),
    groupByText    : trlVps('Group By This Field'),
    showGroupsText : trlVps('Show in Groups')
  });
}

if(Ext && Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
    nameText   : trlVps("Name"),
    valueText  : trlVps("Value"),
    dateFormat : "m/j/Y"
  });
}

if(Ext && Ext.layout && Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
  Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
    splitTip            : trlVps("Drag to resize."),
    collapsibleSplitTip : trlVps("Drag to resize. Double click to hide.")
  });
}
