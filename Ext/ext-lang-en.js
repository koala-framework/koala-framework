/**
 * List compiled by mystix on the extjs.com forums.
 * Thank you Mystix!
 *
 * English Translations
 */
Ext2.onReady(function() {

  if (Ext2 && Ext2.UpdateManager && Ext2.UpdateManager.defaults) {
    Ext2.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">' + trlKwf('Loading...') + '</div>';
  }
  if (Ext2 && Ext2.View) {
    Ext2.View.prototype.emptyText = "";
  }

  if (Ext2 && Ext2.grid.Grid) {
    Ext2.grid.Grid.prototype.ddText = trlKwf("{0} selected row(s)");
  }

  if (Ext2 && Ext2.TabPanelItem) {
    Ext2.TabPanelItem.prototype.closeText = trlKwf("Close this tab");
  }

  if (Ext2 && Ext2.form.Field) {
    Ext2.form.Field.prototype.invalidText = trlKwf("The value in this field is invalid");
  }

  if (Ext2 && Ext2.LoadMask) {
    Ext2.LoadMask.prototype.msg = trlKwf("Loading...");
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

  Date.getShortMonthName = function (month) {
    return Date.monthNames[month].substring(0, 3);
  };

  Date.monthNumbers = {
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

  Date.getMonthNumber = function (name) {
    return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
  };

  Date.dayNames = [
    trlKwf("Sunday"),
    trlKwf("Monday"),
    trlKwf("Tuesday"),
    trlKwf("Wednesday"),
    trlKwf("Thursday"),
    trlKwf("Friday"),
    trlKwf("Saturday")
  ];

  Date.getShortDayName = function (day) {
    return Date.dayNames[day].substring(0, 3);
  };

  if (Ext2 && Ext2.MessageBox) {
    Ext2.MessageBox.buttonText = {
      ok: trlKwf("OK"),
      cancel: trlKwf("Cancel"),
      yes: trlKwf("Yes"),
      no: trlKwf("No")
    };
  }

//Auskommentiert da es beim Datum Probleme gegeben hat
  /*if(Ext2.util.Format){
   Ext2.util.Format.date = function(v, format){
   if(!v) return "";
   if(!(v instanceof Date)) v = new Date(Date.parse(v));
   return v.dateFormat(format || "m/d/Y");
   };
   }*/

  if (Ext2 && Ext2.DatePicker) {
    Ext2.apply(Ext2.DatePicker.prototype, {
      todayText: trlKwf("Today"),
      minText: trlKwf("This date is before the minimum date"),
      maxText: trlKwf("This date is after the maximum date"),
      disabledDaysText: "",
      disabledDatesText: "",
      monthNames: Date.monthNames,
      dayNames: Date.dayNames,
      nextText: trlKwf('Next Month (Control+Right)'),
      prevText: trlKwf('Previous Month (Control+Left)'),
      monthYearText: trlKwf('Choose a month (Control+Up/Down to move years)'),
      todayTip: trlKwf("{0} (Spacebar)"),
      format: trlKwf("m/d/y"),
      okText: "&#160;" + trlKwf('OK') + "&#160;",
      cancelText: trlKwf("Cancel"),
      startDay: parseInt(trlcKwf('start day of week', '0'))
    });
  }

  if (Ext2 && Ext2.PagingToolbar) {
    Ext2.apply(Ext2.PagingToolbar.prototype, {
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
  }

  if (Ext2 && Ext2.form.TextField) {
    Ext2.apply(Ext2.form.TextField.prototype, {
      minLengthText: trlKwf("The minimum length for this field is {0}"),
      maxLengthText: trlKwf("The maximum length for this field is {0}"),
      blankText: trlKwf("This field is required"),
      regexText: "",
      emptyText: null
    });
  }

  if (Ext2 && Ext2.form.NumberField) {
    Ext2.apply(Ext2.form.NumberField.prototype, {
      minText: trlKwf("The minimum value for this field is {0}"),
      maxText: trlKwf("The maximum value for this field is {0}"),
      nanText: trlKwf("{0} is not a valid number")
    });
  }

  if (Ext2 && Ext2.form.DateField) {
    Ext2.apply(Ext2.form.DateField.prototype, {
      disabledDaysText: trlKwf("Disabled"),
      disabledDatesText: trlKwf("Disabled"),
      minText: trlKwf("The date in this field must be after {0}"),
      maxText: trlKwf("The date in this field must be before {0}"),
      invalidText: trlKwf("{0} is not a valid date - it must be in the format {1}"),
      format: trlKwf("m/d/y"),
      altFormats: "m/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d"
    });
  }

  if (Ext2 && Ext2.form.ComboBox) {
    Ext2.apply(Ext2.form.ComboBox.prototype, {
      loadingText: trlKwf("Loading..."),
      valueNotFoundText: undefined
    });
  }

  if (Ext2 && Ext2.form.VTypes) {
    Ext2.apply(Ext2.form.VTypes, {
      emailText: trlKwf('This field should be an e-mail address in the format "user@domain.com"'),
      urlText: trlKwf('This field should be a URL in the format "http://www.domain.com"'),
      alphaText: trlKwf('This field should only contain letters and _'),
      alphanumText: trlKwf('This field should only contain letters, numbers and _')
    });
  }

  if (Ext2 && Ext2.form.HtmlEditor) {
    Ext2.apply(Ext2.form.HtmlEditor.prototype, {
      createLinkText: trlKwf('Please enter the URL for the link:'),
      buttonTips: {
        bold: {
          title: trlKwf('Bold (Ctrl+B)'),
          text: trlKwf('Make the selected text bold.'),
          cls: 'x2-html-editor-tip'
        },
        italic: {
          title: trlKwf('Italic (Ctrl+I)'),
          text: trlKwf('Make the selected text italic.'),
          cls: 'x2-html-editor-tip'
        },
        underline: {
          title: trlKwf('Underline (Ctrl+U)'),
          text: trlKwf('Underline the selected text.'),
          cls: 'x2-html-editor-tip'
        },
        increasefontsize: {
          title: trlKwf('Grow Text'),
          text: trlKwf('Increase the font size.'),
          cls: 'x2-html-editor-tip'
        },
        decreasefontsize: {
          title: trlKwf('Shrink Text'),
          text: trlKwf('Decrease the font size.'),
          cls: 'x2-html-editor-tip'
        },
        backcolor: {
          title: trlKwf('Text Highlight Color'),
          text: trlKwf('Change the background color of the selected text.'),
          cls: 'x2-html-editor-tip'
        },
        forecolor: {
          title: trlKwf('Font Color'),
          text: trlKwf('Change the color of the selected text.'),
          cls: 'x2-html-editor-tip'
        },
        justifyleft: {
          title: trlKwf('Align Text Left'),
          text: trlKwf('Align text to the left.'),
          cls: 'x2-html-editor-tip'
        },
        justifycenter: {
          title: trlKwf('Center Text'),
          text: trlKwf('Center text in the editor.'),
          cls: 'x2-html-editor-tip'
        },
        justifyright: {
          title: trlKwf('Align Text Right'),
          text: trlKwf('Align text to the right.'),
          cls: 'x2-html-editor-tip'
        },
        insertunorderedlist: {
          title: trlKwf('Bullet List'),
          text: trlKwf('Start a bulleted list.'),
          cls: 'x2-html-editor-tip'
        },
        insertorderedlist: {
          title: trlKwf('Numbered List'),
          text: trlKwf('Start a numbered list.'),
          cls: 'x2-html-editor-tip'
        },
        createlink: {
          title: trlKwf('Hyperlink'),
          text: trlKwf('Make the selected text a hyperlink.'),
          cls: 'x2-html-editor-tip'
        },
        sourceedit: {
          title: trlKwf('Source Edit'),
          text: trlKwf('Switch to source editing mode.'),
          cls: 'x2-html-editor-tip'
        }
      }
    });
  }

  if (Ext2 && Ext2.grid.GridView) {
    Ext2.apply(Ext2.grid.GridView.prototype, {
      sortAscText: trlKwf("Sort Ascending"),
      sortDescText: trlKwf("Sort Descending"),
      lockText: trlKwf("Lock Column"),
      unlockText: trlKwf("Unlock Column"),
      columnsText: trlKwf("Columns")
    });
  }

  if (Ext2 && Ext2.grid.GroupingView) {
    Ext2.apply(Ext2.grid.GroupingView.prototype, {
      emptyGroupText: trlKwf('(None)'),
      groupByText: trlKwf('Group By This Field'),
      showGroupsText: trlKwf('Show in Groups')
    });
  }

  if (Ext2 && Ext2.grid.PropertyColumnModel) {
    Ext2.apply(Ext2.grid.PropertyColumnModel.prototype, {
      nameText: trlKwf("Name"),
      valueText: trlKwf("Value"),
      dateFormat: "m/j/Y"
    });
  }

  if (Ext2 && Ext2.layout && Ext2.layout.BorderLayout && Ext2.layout.BorderLayout.SplitRegion) {
    Ext2.apply(Ext2.layout.BorderLayout.SplitRegion.prototype, {
      splitTip: trlKwf("Drag to resize."),
      collapsibleSplitTip: trlKwf("Drag to resize. Double click to hide.")
    });
  }

});
