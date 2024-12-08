<!DOCTYPE html>
<html>
<head>
    <title>Calender</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
</head>
<body>

<div class="container">
    <br/>
    <h1 class="text-center text-primary"><u>Calender</u></h1>
    <br/>
    <div id="calendar"></div>
</div>

<div class="modal" id="createModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Creation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="currentDate" value="" aria-describedby="basic-addon3">
                <input type="hidden" class="form-control" id="editId" value="" aria-describedby="basic-addon3">
                <label for="basic-url">Event Title</label>
                <input type="text" class="form-control" id="title" aria-describedby="basic-addon3">
                <label for="basic-url">Start Time</label>
                <input type="time" class="form-control" id="start" aria-describedby="basic-addon3">
                <label for="basic-url">End Time</label>
                <input type="time" class="form-control" id="end" aria-describedby="basic-addon3">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveEvent">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="editId" value="" aria-describedby="basic-addon3">
                <input type="hidden" class="form-control" id="currentDate" value="" aria-describedby="basic-addon3">
                <label for="editTitle">Event Title</label>
                <input type="text" class="form-control" id="editTitle" aria-describedby="basic-addon3"
                       value="Sample Event Title">
                <label for="editStartTime">Start Time</label>
                <input type="time" class="form-control" id="editStartTime" aria-describedby="basic-addon3"
                       value="10:00">
                <label for="editEndTime">End Time</label>
                <input type="time" class="form-control" id="editEndTime" aria-describedby="basic-addon3" value="12:00">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="deleteEvent">Delete</button>
                <button type="button" class="btn btn-primary" id="updateEvent">Update Event</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var calendar = $('#calendar').fullCalendar({
            editable: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events: '/api/events',
            selectable: true,
            selectHelper: true,
            select: function (start, end, allDay) {
                $('#createModal').show();
                $('#createModal #title').val('');
                $('#createModal #start').val('');
                $('#createModal #end').val('');
                var date = $.fullCalendar.formatDate(start, 'Y-MM-DD HH:mm:ss');
                $('#createModal #currentDate').val(date);
            },
            editable: true,
            eventResize: function (event, delta) {
                var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD HH:mm:ss');
                var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD HH:mm:ss');
                var title = event.title;
                var id = event.id;

                $.ajax({
                    url: '/api/events/' + id,
                    type: 'PUT',
                    data: {
                        title: title,
                        start: start,
                        end: end
                    },
                    success: function (response) {
                        calendar.fullCalendar('refetchEvents');
                        alert("Event Updated Successfully");
                    }
                });
            },
            eventDrop: function (event, delta) {
                var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD HH:mm:ss');
                var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD HH:mm:ss');
                var title = event.title;
                var id = event.id;

                $.ajax({
                    url: '/api/events/' + id,
                    type: 'PUT',
                    data: {
                        title: title,
                        start: start,
                        end: end
                    },
                    success: function (response) {
                        calendar.fullCalendar('refetchEvents');
                        alert("Event Updated Successfully");
                    }
                });
            },
            eventClick: function (event) {
                if (event.id) {
                    $('#editModal').show();
                    $('#editModal #editId').val(event.id);
                    var id = event.id;
                    $.ajax({
                        url: '/api/events/getEvent',
                        type: 'POST',
                        data: {
                            id: id,
                        },
                        success: function (response) {
                            $('#editModal #editTitle').val(response.title);
                            $('#editModal #editStartTime').val(response.start);
                            $('#editModal #editEndTime').val(response.end);
                            $('#editModal #currentDate').val(response.date);
                        }
                    });
                }
            }
        });
        $('#createModal .close').click(function () {
            $('#createModal').hide();
        });
        $('#editModal .close').click(function () {
            $('#editModal').hide();
        });
        $('#createModal #saveEvent').click(function () {
            var title = $('#createModal #title').val();
            var startTime = $('#createModal #start').val();
            var endTime = $('#createModal #end').val();
            var currentDate = $('#createModal #currentDate').val();
            currentDate = currentDate.split(" ")[0];
            var startDateTime = currentDate + ' ' + startTime + ':00';
            var endDateTime = currentDate + ' ' + endTime + ':00';
            var startMoment = moment(startDateTime, 'YYYY-MM-DD HH:mm:ss');
            var endMoment = moment(endDateTime, 'YYYY-MM-DD HH:mm:ss');
            if (!startMoment.isValid() || !endMoment.isValid()) {
                alert('Invalid date or time. Please check the values and try again.');
                return;
            }
            var start = startMoment.format('YYYY-MM-DD HH:mm:ss');
            var end = endMoment.format('YYYY-MM-DD HH:mm:ss');
            $.ajax({
                url: '/api/events',
                type: 'POST',
                data: {
                    title: title,
                    start: start,
                    end: end
                },
                success: function (data) {
                    $('#createModal').hide();
                    calendar.fullCalendar('refetchEvents');
                    toastr.success('Event Created Successfully');
                },
                error: function () {
                    $('#createModal').hide();
                    toastr.error('Error creating event.');
                }
            });
        });

        $('#editModal #updateEvent').click(function () {
            var title = $('#editModal #editTitle').val();
            var startTime = $('#editModal #editStartTime').val();
            var endTime = $('#editModal #editEndTime').val();
            var currentDate = $('#editModal #currentDate').val();
            var id = $('#editModal #editId').val();
            currentDate = moment(currentDate).format('YYYY-MM-DD');
            var startDateTime = currentDate + ' ' + startTime + ':00';
            var endDateTime = currentDate + ' ' + endTime + ':00';
            var startMoment = moment(startDateTime, 'YYYY-MM-DD HH:mm:ss');
            var endMoment = moment(endDateTime, 'YYYY-MM-DD HH:mm:ss');

            if (!startMoment.isValid() || !endMoment.isValid()) {
                alert('Invalid date or time. Please check the values and try again.');
                return;
            }

            var start = startMoment.format('YYYY-MM-DD HH:mm:ss');
            var end = endMoment.format('YYYY-MM-DD HH:mm:ss');
            $.ajax({
                url: '/api/events/' + id, // Updated endpoint for updating events
                type: 'PUT',
                data: {
                    title: title,
                    start: start,
                    end: end
                },
                success: function (response) {
                    $('#editModal').hide();
                    calendar.fullCalendar('refetchEvents');
                    toastr.success("Event Updated Successfully");
                }
            });
        });
        $('#editModal #deleteEvent').click(function () {
            var id = $('#editModal #editId').val();
            $.ajax({
                url: '/api/events/' + id,
                type: 'DELETE',
                success: function (response) {
                    $('#editModal').hide();
                    calendar.fullCalendar('refetchEvents');
                    toastr.success("Event Deleted Successfully");
                }
            });
        });
    });
</script>
</body>
</html>
