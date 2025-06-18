
$(document).ready(function() {
    // Initialize DataTables with export functionality
    if ($('.classes-table table').length > 0) {
        $('.classes-table table').each(function() {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    pageLength: 25,
                    order: [[1, 'asc']], // Order by class name
                    columnDefs: [
                        { orderable: false, targets: [6] } // Disable ordering on actions column
                    ]
                });
            }
        });
    }
});

function applyFilters() {
    const schoolId = $('#school-filter').val();
    const classTypeId = $('#class-type-filter').val();
    const searchTerm = $('#search-filter').val().toLowerCase();

    $('.class-row').each(function() {
        let showRow = true;

        // School filter
        if (schoolId && $(this).data('school-id') != schoolId) {
            showRow = false;
        }

        // Class type filter
        if (classTypeId && $(this).data('class-type-id') != classTypeId) {
            showRow = false;
        }

        // Search filter
        if (searchTerm && !$(this).data('class-name').includes(searchTerm)) {
            showRow = false;
        }

        if (showRow) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });

    updateStats();
    showNotification('Filters applied successfully!', 'success');
}

function resetFilters() {
    $('#school-filter').val('').trigger('change');
    $('#class-type-filter').val('').trigger('change');
    $('#search-filter').val('');

    $('.class-row').show();
    updateStats();
    showNotification('Filters reset successfully!', 'info');
}

function updateStats() {
    const visibleRows = $('.class-row:visible').length;
    const totalRows = $('.class-row').length;

    // Update the stats if needed
    console.log(`Showing ${visibleRows} of ${totalRows} classes`);
}

function reinitializeDataTables() {
    $('.classes-table table').each(function() {
        if ($.fn.DataTable.isDataTable(this)) {
            $(this).DataTable().destroy();
        }
        $(this).DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            pageLength: 25,
            order: [[1, 'asc']], // Order by class name
            columnDefs: [
                { orderable: false, targets: [6] } // Disable ordering on actions column
            ]
        });
    });
}

function showNotification(message, type) {
    // Use your existing notification system
    if (typeof flash_msg !== 'undefined') {
        flash_msg(message, type);
    } else {
        // Fallback notification
        alert(message);
    }
}

// Real-time search
$('#search-filter').on('keyup', function() {
    if ($(this).val().length > 2 || $(this).val().length === 0) {
        applyFilters();
    }
});

// Auto-apply filters when dropdowns change
$('#school-filter, #class-type-filter').on('change', function() {
    applyFilters();
});
