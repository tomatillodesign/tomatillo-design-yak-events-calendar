
(function () {
    // Check if the element with the given ID exists
    if (!document.getElementById('clb-events-calendar-view-root')) {
        // Exit the script if the element is not found
        return;
    }

})();



// a few helpers
function daysInMonth(month, year) {
    // Note: months are zero-indexed. January = 0
    switch (month) {
        case 0: // January
        case 2: // March
        case 4: // May
        case 6: // July
        case 7: // August
        case 9: // October
        case 11: // December
            return 31;
        case 3: // April
        case 5: // June
        case 8: // September
        case 10: // November
            return 30;
        case 1: // February
            return (year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0)) ? 29 : 28;
        default:
            return -1; // Invalid month
    }
}


var lastday = function(month, year){
    // Create a new Date object representing the last day of the specified month
    // By passing m + 1 as the month parameter and 0 as the day parameter, it represents the last day of the specified month
    return new Date(year, month + 1, 0).getDate();
}



const clbRemoveCalendar = function() {

    const root = document.getElementById('clb-events-calendar-view-root');
    root.innerHTML = '';

}



///////////////////////




const clbCreateCalendarActions = function(month, year) {

    if (!document.getElementById('clb-events-calendar-view-root')) {
        // Exit the script if the element is not found
        return;
    }

    // logic here
    // on page load, get current month for display and internal calendar logic
    const now = new Date(year, month, 1);
    const options = { 
        month: "long",
        year: "numeric",
    };
    // const currentMonthValue = now.getMonth();
    // const currentYearValue = now.getFullYear();
    const currentMonthValue = month;
    const currentYearValue = year;
    const currentMonthLabel = new Intl.DateTimeFormat("en-US", options).format(now)

    // set calendar data at root level
    const root = document.getElementById('clb-events-calendar-view-root');
    root.setAttribute('data-current-month-label', currentMonthLabel);
    root.setAttribute('data-current-month-value', currentMonthValue);
    root.setAttribute('data-current-year-value', currentYearValue);

        // set data for view: past/present/future
        const today = new Date();
        const todayMonth = today.getMonth();
        const todayYear = today.getFullYear();
        const firstOfCurrentMonth = new Date(todayYear, todayMonth, 1);
        const firstOfCurrentMonthTimestamp = firstOfCurrentMonth.getTime();

        if( firstOfCurrentMonthTimestamp == now.getTime() ) { root.setAttribute('data-month-status', 'present'); }
        else if( firstOfCurrentMonthTimestamp < now.getTime() ) { root.setAttribute('data-month-status', 'future'); }
        else if( firstOfCurrentMonthTimestamp > now.getTime() ) { root.setAttribute('data-month-status', 'past'); }
        

        //////

    const calendarHeader = document.createElement("div");
    calendarHeader.classList.add('clb-calendar-action-wrapper');

    const prevActions = document.createElement("div");
    prevActions.classList.add('clb-calendar-prev-actions-wrapper');
    
    const prevBtn = document.createElement("button");
    prevBtn.innerHTML = '<i class="fa-light fa-circle-chevron-left fa-2x"></i>';
    prevBtn.setAttribute('id', 'clb-calendar-action-prev-month');
    prevBtn.classList.add('clb-calendar-action-btn');
    prevActions.appendChild(prevBtn);

    const todayBtnPrev = document.createElement("button");
    todayBtnPrev.innerHTML = 'Today';
    todayBtnPrev.setAttribute('id', 'clb-calendar-action-today-prev');
    todayBtnPrev.classList.add('clb-calendar-action-btn');
    todayBtnPrev.classList.add('clb-calendar-today-btn');
    prevActions.appendChild(todayBtnPrev);

    calendarHeader.appendChild(prevActions);

    const viewMonthTitle = document.createElement("h2");
    calendarHeader.classList.add('clb-calendar-month-title-wrapper');
    viewMonthTitle.innerHTML = currentMonthLabel;
    calendarHeader.appendChild(viewMonthTitle);

    const nextActions = document.createElement("div");
    nextActions.classList.add('clb-calendar-next-actions-wrapper');

    const todayBtnNext = document.createElement("button");
    todayBtnNext.innerHTML = 'Today';
    todayBtnNext.setAttribute('id', 'clb-calendar-action-today-next');
    todayBtnNext.classList.add('clb-calendar-action-btn');
    todayBtnNext.classList.add('clb-calendar-today-btn');
    nextActions.appendChild(todayBtnNext);

    const nextBtn = document.createElement("button");
    nextBtn.innerHTML = '<i class="fa-light fa-circle-chevron-right fa-2x"></i>';
    nextBtn.setAttribute('id', 'clb-calendar-action-next-month');
    nextBtn.classList.add('clb-calendar-action-btn');
    nextActions.appendChild(nextBtn);

    calendarHeader.appendChild(nextActions);

    return calendarHeader;

}



const clbCreateCalendarHeaderRow = function(month, year) {

    const newRow = document.createElement("div");
    newRow.classList.add('clb-calendar-week-wrapper');
    newRow.classList.add('clb-calendar-week-header-row');

    for ( let i = 0; i < 7; i++ ) {

        const newDaySquare = document.createElement("div");
        newDaySquare.classList.add('clb-calendar-day-header-wrapper');

        if( i === 0 ) { newDaySquare.innerHTML = 'Sun'; newDaySquare.classList.add('clb-calendar-day-sun');}
        else if( i === 1 ) { newDaySquare.innerHTML = 'Mon'; newDaySquare.classList.add('clb-calendar-day-mon');}
        else if( i === 2 ) { newDaySquare.innerHTML = 'Tue'; newDaySquare.classList.add('clb-calendar-day-tue'); }
        else if( i === 3 ) { newDaySquare.innerHTML = 'Wed'; newDaySquare.classList.add('clb-calendar-day-wed'); }
        else if( i === 4 ) { newDaySquare.innerHTML = 'Thu'; newDaySquare.classList.add('clb-calendar-day-thu'); }
        else if( i === 5 ) { newDaySquare.innerHTML = 'Fri'; newDaySquare.classList.add('clb-calendar-day-fri'); }
        else if( i === 6 ) { newDaySquare.innerHTML = 'Sat'; newDaySquare.classList.add('clb-calendar-day-sat'); }

        newRow.appendChild(newDaySquare);

    }

    return newRow;

}



const clbCreateMonth = function(month, year) {

    if (!document.getElementById('clb-events-calendar-view-root')) {
        // Exit the script if the element is not found
        return;
    }

    const firstDayOfMonthDateObj = new Date(year, month, 1);
    
    const firstDayOfMonthNumber = firstDayOfMonthDateObj.getDay();
    let dayCounter = null;

    const totalDaysInMonth = daysInMonth(month, year);
    const monthOpeningTimestamp = firstDayOfMonthDateObj.getTime();
    const lastDayOfMonthDateObj = new Date(year, month, totalDaysInMonth, 23, 59, 59);
    const monthClosingTimestamp =  lastDayOfMonthDateObj.getTime();

    const root = document.getElementById('clb-events-calendar-view-root');
    let gridItem = 1;

    root.appendChild(clbCreateCalendarActions(month, year));
    root.appendChild(clbCreateCalendarHeaderRow(month, year));

    const today = new Date();
    const todayMonth = today.getMonth();
    const todayDay = today.getDay();
    const todayYear = today.getFullYear();

    /////// get events only from this month + log
    const thisMonthEvents = dhmEvents.filter((event, index) => {
         
        // Handle multi-session events differently
        const hasSessions = event['event_has_sessions'];
        
        if (hasSessions && event['event_sessions'] && event['event_sessions'].length > 0) {
            // Check if any session falls within this month
            return event['event_sessions'].some(session => {
                const sessionAllDay = session['session_all_day'];
                const sessionStart = sessionAllDay ? session['session_start_date'] : session['session_start_datetime'];
                if (!sessionStart) return false;
                const sessionStartObj = new Date(sessionStart);
                const sessionStartTimestamp = sessionStartObj.getTime();
                return ((sessionStartTimestamp >= monthOpeningTimestamp) && (sessionStartTimestamp <= monthClosingTimestamp));
            });
        } else {
            // Handle both all-day and timed events
            const isAllDay = event['event_all_day'];
            const eventStartDateString = isAllDay ? event['event_start_date'] : event['event_start_date_time'];
            
            if (!eventStartDateString) return false;
            
            const eventStartDateObj = new Date(eventStartDateString);
            const eventStartTimestamp = eventStartDateObj.getTime();
            return ((eventStartTimestamp >= monthOpeningTimestamp) && (eventStartTimestamp <= monthClosingTimestamp));
        }

    } );

    //////////////////////////////////////////////

    // setup the week (row)
    for ( let rowCounter = 0; rowCounter < 5; rowCounter++ ) {
        
        const newRow = document.createElement("div");
        newRow.classList.add('clb-calendar-week-wrapper');

            // setup each day
            for ( let i = 0; i < 7; i++ ) {

                const newDaySquare = document.createElement("div");
                newDaySquare.classList.add('clb-calendar-day-wrapper');
                newDaySquare.setAttribute('data-gridItem', gridItem);

                // date logic for displaying date numbers
                // if is first row
                // get first day of current month
                // line it up, set InnerHTML, then activate counter and move forward with each new day
                if( rowCounter === 0 && i === firstDayOfMonthNumber ) {
                    dayCounter = 1;
                }

                if( i === 0 ) { newDaySquare.setAttribute('data-dayOfWeekLabel', 'Sunday'); newDaySquare.classList.add('clb-calendar-day-sun'); }
                else if( i === 1 ) { newDaySquare.setAttribute('data-dayOfWeekLabel', 'Monday'); newDaySquare.classList.add('clb-calendar-day-mon'); }
                else if( i === 2 ) { newDaySquare.setAttribute('data-dayOfWeekLabel', 'Tuesday'); newDaySquare.classList.add('clb-calendar-day-tue'); }
                else if( i === 3 ) { newDaySquare.setAttribute('data-dayOfWeekLabel', 'Wednesday'); newDaySquare.classList.add('clb-calendar-day-wed'); }
                else if( i === 4 ) { newDaySquare.setAttribute('data-dayOfWeekLabel', 'Thursday'); newDaySquare.classList.add('clb-calendar-day-thu'); }
                else if( i === 5 ) { newDaySquare.setAttribute('data-dayOfWeekLabel', 'Friday'); newDaySquare.classList.add('clb-calendar-day-fri'); }
                else if( i === 6 ) { newDaySquare.setAttribute('data-dayOfWeekLabel', 'Saturday'); newDaySquare.classList.add('clb-calendar-day-sat'); }

                if( i === todayDay && month === todayMonth && year === todayYear ) {
                    if( dayCounter === today.getDate() ) {
                        newDaySquare.classList.add('clb-calendar-today');
                    }
                }

                newRow.appendChild(newDaySquare);

                if( dayCounter ) { 
                    if( dayCounter <= totalDaysInMonth ) {

                    // date logic
                    const startOfDayObj = new Date(year, month, dayCounter);
                    const startOfDayTimestamp = startOfDayObj.getTime();
                    const endOfDayObj = new Date(year, month, dayCounter, 23, 59, 59);
                    const endOfDayTimestamp = endOfDayObj.getTime();
                    
                    /////// get events only from this DAY + log
                    const thisDayEvents = thisMonthEvents.filter((event, index) => {

                        let showEvent = false;
                        
                        // Handle multi-session events
                        const hasSessions = event['event_has_sessions'];
                        
                        if (hasSessions && event['event_sessions'] && event['event_sessions'].length > 0) {
                            // Check if any session falls on this day
                            showEvent = event['event_sessions'].some(session => {
                                const sessionAllDay = session['session_all_day'];
                                const sessionStart = sessionAllDay ? session['session_start_date'] : session['session_start_datetime'];
                                const sessionEnd = sessionAllDay ? session['session_end_date'] : session['session_end_datetime'];
                                if (!sessionStart) return false;
                                
                                const sessionStartObj = new Date(sessionStart);
                                const sessionStartTimestamp = sessionStartObj.getTime();
                                const sessionEndObj = sessionEnd ? new Date(sessionEnd) : sessionStartObj;
                                const sessionEndTimestamp = sessionEndObj.getTime();
                                
                                // Session starts today
                                if((sessionStartTimestamp >= startOfDayTimestamp) && (sessionStartTimestamp <= endOfDayTimestamp)) return true;
                                // Session ends today
                                if((sessionEndTimestamp >= startOfDayTimestamp) && (sessionEndTimestamp <= endOfDayTimestamp)) return true;
                                // Session spans across today
                                if((sessionStartTimestamp < startOfDayTimestamp) && (sessionEndTimestamp > endOfDayTimestamp)) return true;
                                
                                return false;
                            });
                        } else {
                            // Handle both all-day and timed events
                            const isAllDay = event['event_all_day'];
                            
                            // event start
                            const eventStartDateString = isAllDay ? event['event_start_date'] : event['event_start_date_time'];
                            if (!eventStartDateString) return false;
                            const eventStartDateObj = new Date(eventStartDateString);
                            const eventStartTimestamp = eventStartDateObj.getTime();

                            // event end
                            const eventEndDateString = isAllDay ? event['event_end_date'] : event['event_end_date_time'];
                            if (!eventEndDateString) return false;
                            const eventEndDateObj = new Date(eventEndDateString);
                            const eventEndTimestamp = eventEndDateObj.getTime();

                            // Check if event starts today
                            if((eventStartTimestamp >= startOfDayTimestamp) && (eventStartTimestamp <= endOfDayTimestamp)) { showEvent = true; }
                            // Check if event ends today
                            if((eventEndTimestamp >= startOfDayTimestamp) && (eventEndTimestamp <= endOfDayTimestamp)) { showEvent = true; } 
                            // Check if event spans across today (starts before and ends after)
                            if((eventStartTimestamp < startOfDayTimestamp) && (eventEndTimestamp > endOfDayTimestamp)) { showEvent = true; }
                        }

                        return showEvent;

                    } );

                    newDaySquare.innerHTML += '<span class="clb-calendar-date-wrapper">' + dayCounter + '</span>';

                    thisDayEvents.forEach((event) => {
                        const eventTitle = event['title'];
                        const eventPermalink = event['permalink'];
                        const hasSessions = event['event_has_sessions'];
                        const eventSessions = event['event_sessions'];
                        
                        // Check if event has sessions that match this day
                        let sessionForToday = null;
                        if (hasSessions && eventSessions && eventSessions.length > 0) {
                            // Find session(s) for this specific day
                            const currentDayStart = new Date(year, month, dayCounter).getTime();
                            const currentDayEnd = new Date(year, month, dayCounter, 23, 59, 59).getTime();
                            
                            sessionForToday = eventSessions.find(session => {
                                const sessionAllDay = session.session_all_day;
                                const sessionStart = sessionAllDay ? session.session_start_date : session.session_start_datetime;
                                if (!sessionStart) return false;
                                
                                const sessionStartObj = new Date(sessionStart);
                                const sessionStartTimestamp = sessionStartObj.getTime();
                                
                                const sessionEnd = sessionAllDay ? session.session_end_date : session.session_end_datetime;
                                const sessionEndObj = sessionEnd ? new Date(sessionEnd) : sessionStartObj;
                                const sessionEndTimestamp = sessionEndObj.getTime();
                                
                                // Check if session overlaps with this day
                                return (sessionStartTimestamp <= currentDayEnd && sessionEndTimestamp >= currentDayStart);
                            });
                        }
                        
                        // Display event with session times if available
                        let eventDisplay = eventTitle;
                        if (sessionForToday) {
                            // Only show times if not all-day
                            if (!sessionForToday.session_all_day) {
                                const startDateTime = new Date(sessionForToday.session_start_datetime);
                                const endDateTime = new Date(sessionForToday.session_end_datetime);
                                const startTime = startDateTime.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                                const endTime = endDateTime.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                                eventDisplay += '<br><span class="clb-calendar-session-time">' + startTime + ' – ' + endTime + '</span>';
                            }
                        }
                        
                        newDaySquare.innerHTML += '<div class="clb-calendar-single-event-wrapper"><a href="' + eventPermalink + '">' + eventDisplay + '</a></div>';
                    });

                    dayCounter++; 


                    }
                }
                gridItem++;

            }

        root.appendChild(newRow);

    }

}


const clbInitializeCalendar = function() {

    ///////////// create new divs in a grid
    const newDiv = document.createElement("div");
    newDiv.classList.add('clb-calendar-day');

    const now = new Date();
    const currentMonthValue = now.getMonth();
    const currentYearValue = now.getFullYear();

    clbCreateMonth( currentMonthValue, currentYearValue);

}
clbInitializeCalendar(); // run once on page load


// add btn event listeners & actions
const clbPrevMonth = function() {

    const root = document.getElementById('clb-events-calendar-view-root');
    const currentMonth = parseInt(root.dataset.currentMonthValue);
    const currentYear = parseInt(root.dataset.currentYearValue);
    let newMonth;
    let newYear;
    
    // logic going back from January to December (prev year)
    if( currentMonth === 0 ) {
        newMonth = 11;
        newYear = currentYear - 1;
    } else {
        newMonth = parseInt(currentMonth - 1);
        newYear = currentYear;
    }

    clbRemoveCalendar();
    clbCreateMonth( newMonth, newYear );
    clbPrevMonthListener();
    clbNextMonthListener();
    clbTodayMonthListenerPrev();
    clbTodayMonthListenerNext();
}

const clbNextMonth = function() {

    const root = document.getElementById('clb-events-calendar-view-root');
    const currentMonth = parseInt(root.dataset.currentMonthValue);
    const currentYear = parseInt(root.dataset.currentYearValue);
    let newMonth;
    let newYear;
    
    // logic going back from January to December (prev year)
    if( currentMonth === 11 ) {
        newMonth = 0;
        newYear = currentYear + 1;
    } else {
        newMonth = parseInt(currentMonth + 1);
        newYear = currentYear;
    }

    clbRemoveCalendar();
    clbCreateMonth( newMonth, newYear );
    clbPrevMonthListener();
    clbNextMonthListener();
    clbTodayMonthListenerPrev();
    clbTodayMonthListenerNext();
}

const clbToday = function() {

    const now = new Date();
    const newMonth = now.getMonth();
    const newYear = now.getFullYear();

    clbRemoveCalendar();
    clbCreateMonth( newMonth, newYear );
    clbPrevMonthListener();
    clbNextMonthListener();
    clbTodayMonthListenerPrev();
    clbTodayMonthListenerNext();
}



const clbPrevMonthListener = function() {
    if (!document.getElementById('clb-events-calendar-view-root')) {
        // Exit the script if the element is not found
        return;
    }

    const element = document.getElementById('clb-calendar-action-prev-month');
    element.addEventListener("click", clbPrevMonth);
}
clbPrevMonthListener();

const clbNextMonthListener = function() {
    if (!document.getElementById('clb-events-calendar-view-root')) {
        // Exit the script if the element is not found
        return;
    }
    const element = document.getElementById('clb-calendar-action-next-month');
    element.addEventListener("click", clbNextMonth);
}
clbNextMonthListener();

const clbTodayMonthListenerPrev = function() {
    if (!document.getElementById('clb-events-calendar-view-root')) {
        // Exit the script if the element is not found
        return;
    }
    const element = document.getElementById('clb-calendar-action-today-prev');
    element.addEventListener("click", clbToday);
}
clbTodayMonthListenerPrev();

const clbTodayMonthListenerNext = function() {
    if (!document.getElementById('clb-events-calendar-view-root')) {
        // Exit the script if the element is not found
        return;
    }
    const element = document.getElementById('clb-calendar-action-today-next');
    element.addEventListener("click", clbToday);
}
clbTodayMonthListenerNext();

