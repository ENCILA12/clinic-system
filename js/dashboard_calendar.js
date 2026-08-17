document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('dashboardCalendar');
    
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            events: 'api/get_calendar_events.php',
            navLinks: true, // can click day/week names to navigate views
            businessHours: true, // display business hours
            editable: false,
            selectable: true,
            eventClick: function(info) {
                // Show a simple alert with details
                const props = info.event.extendedProps;
                const time = info.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                let msg = "Patient & Procedure: " + info.event.title + "\n";
                msg += "Time: " + time + "\n";
                msg += "Status: " + props.status + "\n";
                msg += "Dentist: " + props.dentist + "\n";
                msg += "Duration: " + props.duration + "\n";
                if(props.notes) {
                    msg += "Notes: " + props.notes + "\n";
                }
                
                alert(msg);
            }
        });
        calendar.render();
    }
});
