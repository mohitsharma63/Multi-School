
$(document).ready(function() {
    // Initialize advanced filters
    initializeTimetableFilters();

    // School selection change handler for create form
    $('#school_id').on('change', function() {
        var schoolId = $(this).val();
        loadClassesBySchool(schoolId);
    });

    // Apply filters
    $('#apply-filters').on('click', function() {
        applyTimetableFilters();
    });

    // Reset filters
    $('#reset-filters').on('click', function() {
        resetTimetableFilters();
    });

    // Initialize DataTables with export functionality
    if ($('.timetable-table').length > 0) {
        $('.timetable-table').each(function() {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    pageLength: 25,
                    order: [[7, 'desc']], // Order by created date
                    columnDefs: [
                        { orderable: false, targets: [8] } // Disable ordering on actions column
                    ]
                });
            }
        });
    }
});

function initializeTimetableFilters() {
    // Initialize select2 if available
    if ($.fn.select2) {
        $('.select').select2({
            minimumResultsForSearch: Infinity
        });
    }
}

function loadClassesBySchool(schoolId) {
    if (!schoolId) {
        $('#my_class_id').html('<option value="">Select School First</option>');
        return;
    }

    // Show loading
    $('#my_class_id').html('<option value="">Loading classes...</option>');

    // AJAX call to get classes by school
    $.ajax({
        url: '/ajax/get-classes-by-school',
        type: 'GET',
        data: { school_id: schoolId },
        success: function(response) {
            var options = '<option value="">Select Class</option>';

            if (response.classes && response.classes.length > 0) {
                $.each(response.classes, function(index, myClass) {
                    options += '<option value="' + myClass.id + '">' + myClass.name + '</option>';
                });
            } else {
                options = '<option value="">No classes found for this school</option>';
            }

            $('#my_class_id').html(options);
        },
        error: function() {
            $('#my_class_id').html('<option value="">Error loading classes</option>');
            showNotification('Error loading classes. Please try again.', 'error');
        }
    });
}

function applyTimetableFilters() {
    var schoolFilter = $('#school-filter').val();
    var classFilter = $('#class-filter').val();
    var typeFilter = $('#type-filter').val();
    var yearFilter = $('#year-filter').val();

    // Show/hide timetable rows based on filters
    $('.timetable-row').each(function() {
        var row = $(this);
        var showRow = true;

        // School filter
        if (schoolFilter && row.data('school-id') != schoolFilter) {
            showRow = false;
        }

        // Class filter
        if (classFilter && row.data('class-id') != classFilter) {
            showRow = false;
        }

        // Type filter
        if (typeFilter && row.data('type') != typeFilter) {
            showRow = false;
        }

        // Year filter
        if (yearFilter && row.data('year') != yearFilter) {
            showRow = false;
        }

        if (showRow) {
            row.show();
        } else {
            row.hide();
        }
    });

    // Show/hide tabs based on filters
    $('.class-tab').each(function() {
        var tab = $(this);
        var classId = tab.data('class-id');
        var schoolId = tab.data('school-id');
        var showTab = true;

        // School filter
        if (schoolFilter && schoolId != schoolFilter) {
            showTab = false;
        }

        // Class filter
        if (classFilter && classId != classFilter) {
            showTab = false;
        }

        if (showTab) {
            tab.show();
        } else {
            tab.hide();
        }
    });

    // Update DataTables
    $('.timetable-table').each(function() {
        if ($.fn.DataTable.isDataTable(this)) {
            $(this).DataTable().draw();
        }
    });

    showNotification('Filters applied successfully!', 'success');
}

function resetTimetableFilters() {
    // Reset all filter dropdowns
    $('#school-filter, #class-filter, #type-filter, #year-filter').val('').trigger('change');

    // Show all rows and tabs
    $('.timetable-row, .class-tab').show();

    // Update DataTables
    $('.timetable-table').each(function() {
        if ($.fn.DataTable.isDataTable(this)) {
            $(this).DataTable().draw();
        }
    });

    showNotification('Filters reset successfully!', 'info');
}

function showNotification(message, type) {
    // Use your existing notification system
    if (typeof flash_msg !== 'undefined') {
        flash_msg(message, type);
    } else if ($.fn.toast) {
        $.toast({
            heading: type.charAt(0).toUpperCase() + type.slice(1),
            text: message,
            icon: type,
            position: 'top-right'
        });
    } else {
        alert(message);
    }
}
