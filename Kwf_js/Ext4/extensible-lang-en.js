/*
 * Default English (US) locale
 * By Extensible, LLC
 *
 * Contributors:
 * - Gabriel Sidler, http://teamup.com
 *
 * A general note regarding pluralization... Some locales require conditional logic
 * to properly pluralize certain terms. When this might be required there is an additional
 * "get*" method in addition to the standard config. By default these simply return the
 * same value as the corresponding config, but if your locale requires such logic simply
 * implement the appropriate method bodies. The configs in these cases are still listed for
 * backwards compatibility, but they are deprecated and will be removed in a future release.
 * The Czech locale (extensible-lang-cs.js) is an example that uses these method overrides.
 */

Ext4.onReady(function() {
    var exists = Ext4.Function.bind(Ext4.ClassManager.get, Ext4.ClassManager);
    
    Extensible.Date.use24HourTime = trlcKwf('Hour Time Format', '12') == '24';
    
    if (exists('Extensible.calendar.view.AbstractCalendar')) {
        Ext4.apply(Extensible.calendar.view.AbstractCalendar.prototype, {
            startDay: parseInt(trlcKwf('start day of week', '0')),
            todayText: trlKwf('Today'),
            defaultEventTitleText: trlKwf('(No title)'),
            ddCreateEventText: trlKwf('Create event for {0}'),
            ddMoveEventText: trlKwf('Move event to {0}'),
            ddResizeEventText: trlKwf('Update event to {0}')
        });
    }
    
    if (exists('Extensible.calendar.view.Month')) {
        Ext4.apply(Extensible.calendar.view.Month.prototype, {
            moreText: trlKwf('+{0} more...'), // deprecated
            getMoreText: function(numEvents) {
                return trlKwf('+{0} more...');
            },
            detailsTitleDateFormat: 'F j'
        });
    }
    
    if (exists('Extensible.calendar.CalendarPanel')) {
        Ext4.apply(Extensible.calendar.CalendarPanel.prototype, {
            todayText: trlKwf('Today'),
            dayText: trlKwf('Day'),
            weekText: trlKwf('Week'),
            monthText: trlKwf('Month'),
            jumpToText: trlKwf('Jump to:'),
            goText: trlKwf('Go'),
            multiDayText: trlKwf('{0} Days'), // deprecated
            multiWeekText: trlKwf('{0} Weeks'), // deprecated
            getMultiDayText: function(numDays) {
                return trlKwf('{0} Days');
            },
            getMultiWeekText: function(numWeeks) {
                return trlKwf('{0} Weeks');
            },
            startDay: parseInt(trlcKwf('start day of week', '0'))
        });
    }
    
    if (exists('Extensible.calendar.form.EventWindow')) {
        Ext4.apply(Extensible.calendar.form.EventWindow.prototype, {
            width: 600,
            labelWidth: 65,
            titleTextAdd: trlKwf('Add Event'),
            titleTextEdit: trlKwf('Edit Event'),
            savingMessage: trlKwf('Saving changes...'),
            deletingMessage: trlKwf('Deleting event...'),
            detailsLinkText: trlKwf('Edit Details...'),
            saveButtonText: trlKwf('Save'),
            deleteButtonText: trlKwf('Delete'),
            cancelButtonText: trlKwf('Cancel'),
            titleLabelText: trlKwf('Title'),
            datesLabelText: trlKwf('When'),
            calendarLabelText: trlKwf('Calendar')
        });
    }
    
    if (exists('Extensible.calendar.form.EventDetails')) {
        Ext4.apply(Extensible.calendar.form.EventDetails.prototype, {
            labelWidth: 65,
            labelWidthRightCol: 65,
            title: ('Event Form'),
            titleTextAdd: ('Add Event'),
            titleTextEdit: ('Edit Event'),
            saveButtonText: ('Save'),
            deleteButtonText: ('Delete'),
            cancelButtonText: ('Cancel'),
            titleLabelText: ('Title'),
            datesLabelText: ('When'),
            reminderLabelText: ('Reminder'),
            notesLabelText: ('Notes'),
            locationLabelText: ('Location'),
            webLinkLabelText: ('Web Link'),
            calendarLabelText: ('Calendar'),
            repeatsLabelText: ('Repeats')
        });
    }
    
    if (exists('Extensible.form.field.DateRange')) {
        Ext4.apply(Extensible.form.field.DateRange.prototype, {
            toText: trlKwf('to'),
            allDayText: trlKwf('All day'),
            startDay: parseInt(trlcKwf('start day of week', '0'))
        });
    }
    
    if (exists('Extensible.calendar.form.field.CalendarCombo')) {
        Ext4.apply(Extensible.calendar.form.field.CalendarCombo.prototype, {
            fieldLabel: trlKwf('Calendar')
        });
    }
    
    if (exists('Extensible.calendar.gadget.CalendarListPanel')) {
        Ext4.apply(Extensible.calendar.gadget.CalendarListPanel.prototype, {
            title: trlKwf('Calendars')
        });
    }
    
    if (exists('Extensible.calendar.gadget.CalendarListMenu')) {
        Ext4.apply(Extensible.calendar.gadget.CalendarListMenu.prototype, {
            displayOnlyThisCalendarText: ('Display only this calendar')
        });
    }
    
    if (exists('Extensible.form.recurrence.Combo')) {
        Ext4.apply(Extensible.form.recurrence.Combo.prototype, {
            fieldLabel: ('Repeats'),
            recurrenceText: {
                none: ('Does not repeat'),
                daily: ('Daily'),
                weekly: ('Weekly'),
                monthly: ('Monthly'),
                yearly: ('Yearly')
            }
        });
    }
    
    if (exists('Extensible.calendar.form.field.ReminderCombo')) {
        Ext4.apply(Extensible.calendar.form.field.ReminderCombo.prototype, {
            fieldLabel: ('Reminder'),
            noneText: ('None'),
            atStartTimeText: ('At start time'),
            getMinutesText: function(numMinutes) {
                return numMinutes === 1 ? ('minute') : ('minutes');
            },
            getHoursText: function(numHours) {
                return numHours === 1 ? ('hour') : ('hours');
            },
            getDaysText: function(numDays) {
                return numDays === 1 ? ('day') : ('days');
            },
            getWeeksText: function(numWeeks) {
                return numWeeks === 1 ? ('week') : ('weeks');
            },
            reminderValueFormat: ('{0} {1} before start') // e.g. "2 hours before start"
        });
    }
    
    if (exists('Extensible.form.field.DateRange')) {
        Ext4.apply(Extensible.form.field.DateRange.prototype, {
            dateFormat: trlKwf('Y-m-d')
        });
    }
    
    if (exists('Extensible.calendar.menu.Event')) {
        Ext4.apply(Extensible.calendar.menu.Event.prototype, {
            editDetailsText: trlKwf('Edit Details'),
            deleteText: trlKwf('Delete'),
            moveToText: trlKwf('Move to...')
        });
    }
    
    if (exists('Extensible.calendar.dd.DropZone')) {
        Ext4.apply(Extensible.calendar.dd.DropZone.prototype, {
            dateRangeFormat: '{0}-{1}',
            dateFormat: 'n/j'
        });
    }
    
    if (exists('Extensible.calendar.dd.DayDropZone')) {
        Ext4.apply(Extensible.calendar.dd.DayDropZone.prototype, {
            dateRangeFormat: '{0}-{1}',
            dateFormat : 'n/j'
        });
    }
    
    if (exists('Extensible.calendar.template.BoxLayout')) {
        Ext4.apply(Extensible.calendar.template.BoxLayout.prototype, {
            firstWeekDateFormat: 'D j',
            otherWeeksDateFormat: 'j',
            singleDayDateFormat: 'l, F j, Y',
            multiDayFirstDayFormat: 'M j, Y',
            multiDayMonthStartFormat: 'M j'
        });
    }
    
    if (exists('Extensible.calendar.template.Month')) {
        Ext4.apply(Extensible.calendar.template.Month.prototype, {
            dayHeaderFormat: 'D',
            dayHeaderTitleFormat: 'l, F j, Y'
        });
    }

    /*
     * Recurrence strings added in v.1.6.0
     */
    if (exists('Extensible.form.recurrence.Rule')) {
        Ext4.apply(Extensible.form.recurrence.Rule.prototype, {
            strings: {
                dayNamesShort: {
                    SU: trlKwf('Sun'),
                    MO: trlKwf('Mon'),
                    TU: trlKwf('Tue'),
                    WE: trlKwf('Wed'),
                    TH: trlKwf('Thu'),
                    FR: trlKwf('Fri'),
                    SA: trlKwf('Sat')
                },

                dayNamesShortByIndex: [
                    trlKwf('Sun'),
                    trlKwf('Mon'),
                    trlKwf('Tue'),
                    trlKwf('Wed'),
                    trlKwf('Thu'),
                    trlKwf('Fri'),
                    trlKwf('Sat')
                ],

                dayNamesLong: {
                    SU: trlKwf('Sunday'),
                    MO: trlKwf('Monday'),
                    TU: trlKwf('Tuesday'),
                    WE: trlKwf('Wednesday'),
                    TH: trlKwf('Thursday'),
                    FR: trlKwf('Friday'),
                    SA: trlKwf('Saturday')
                },

                ordinals: {
                    1: trlKwf('first'),
                    2: trlKwf('second'),
                    3: trlKwf('third'),
                    4: trlKwf('fourth'),
                    5: trlKwf('fifth'),
                    6: trlKwf('sixth')
                },

                frequency: {
                    none: ('Does not repeat'),
                    daily: ('Daily'),
                    weekly: ('Weekly'),
                    weekdays: ('Every weekday (Mon-Fri)'),
                    monthly: ('Monthly'),
                    yearly: ('Yearly')
                },

                every: 'Every',       // e.g. Every 2 days
                days: 'days',
                weeks: 'weeks',
                weekdays: 'weekdays',
                months: 'months',
                years: 'years',
                time: 'time',        // e.g. Daily, 1 time
                times: 'times',      // e.g. Daily, 5 times
                until: 'until',      // e.g. Daily, until Dec, 31 2012
                untilFormat: 'M j, Y', // e.g. Dec 10, 2012
                and: 'and',          // e.g. Weekly on Tuesday and Friday
                on: 'on',            // e.g. Weekly on Thursday
                onDay: 'on day',     // e.g. Monthly on day 23
                onDayPostfix: '',    // In some languages a postfix is need for the onDay term,
                // for example in German: 'Monatlich am 23.'
                // Here the postfix would be '.'
                onThe: 'on the',     // e.g. Monthly on the first Thursday
                onTheLast: 'on the last', // e.g. Monthly on the last Friday
                onTheLastDay: 'on the last day', // e.g. Monthly on the last day
                of: 'of',            // e.g. Annually on the last day of November
                monthFormat: 'F',    // e.g. November
                monthDayFormat: 'F j' // e.g. November 10
            }
        });
    }

    if (exists('Extensible.form.recurrence.FrequencyCombo')) {
        Ext4.apply(Extensible.form.recurrence.FrequencyCombo.prototype, {
            fieldLabel: 'Repeats'
        });
    }

    if (exists('Extensible.form.recurrence.RangeEditWindow')) {
        Ext4.apply(Extensible.form.recurrence.RangeEditWindow.prototype, {
            title: 'Recurring Event Options',
            saveButtonText: 'Save',
            cancelButtonText: 'Cancel'
        });
    }

    if (exists('Extensible.form.recurrence.RangeEditPanel')) {
        Ext4.apply(Extensible.form.recurrence.RangeEditPanel.prototype, {
            headerText: 'There are multiple events in this series. How would you like your changes applied?',
            optionSingleButtonText: 'Single',
            optionSingleDescription: 'Apply to this event only. No other events in the series will be affected.',
            optionFutureButtonText: 'Future',
            optionFutureDescription: 'Apply to this and all following events only. Past events will be unaffected.',
            optionAllButtonText: 'All Events',
            optionAllDescription: 'Apply to every event in this series.'
        });
    }

    if (exists('Extensible.form.recurrence.option.Interval')) {
        Ext4.apply(Extensible.form.recurrence.option.Interval.prototype, {
            dateLabelFormat: 'l, F j',
            strings: {
                repeatEvery: 'Repeat every',
                beginning: 'beginning',
                day: 'day',
                days: 'days',
                week: 'week',
                weeks: 'weeks',
                month: 'month',
                months: 'months',
                year: 'year',
                years: 'years'
            }
        });
    }

    if (exists('Extensible.form.recurrence.option.Duration')) {
        Ext4.apply(Extensible.form.recurrence.option.Duration.prototype, {
            strings: {
                andContinuing: 'and continuing',
                occurrences: 'occurrences',
                forever: 'forever',
                forText: 'for',
                until: 'until'
            }
        });
    }

    if (exists('Extensible.form.recurrence.option.Weekly')) {
        Ext4.apply(Extensible.form.recurrence.option.Weekly.prototype, {
            strings: {
                on: 'on'
            }
        });
    }

    if (exists('Extensible.form.recurrence.option.Monthly')) {
        Ext4.apply(Extensible.form.recurrence.option.Monthly.prototype, {
            strings: {
                // E.g. "on the 15th day of each month/year"
                onThe: 'on the',
                ofEach: 'of each',
                inText: 'in',
                day: 'day',
                month: 'month',
                year: 'year',
                last: 'last',
                lastDay: 'last day',
                monthDayDateFormat: 'jS',
                nthWeekdayDateFormat: 'S' // displays the ordinal postfix, e.g. th for 5th.

            }
        });
    }

});
