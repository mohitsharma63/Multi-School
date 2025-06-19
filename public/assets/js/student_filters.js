
// Advanced Student Filtering JavaScript

// Student Admission Filters
function applyStudentFilters() {
    var classFilter = $('#filter_class').val();
    var genderFilter = $('#filter_gender').val();
    var yearFilter = $('#filter_year').val();
    var schoolFilter = $('#filter_school').val();

    // Apply filters to student admission form
    if (classFilter) {
        $('#my_class_id').val(classFilter).trigger('change');
    }
    if (genderFilter) {
        $('#gender').val(genderFilter).trigger('change');
    }
    if (yearFilter) {
        $('#year_admitted').val(yearFilter).trigger('change');
    }
    if (schoolFilter) {
        // Update classes based on selected school
        updateClassesBySchool(schoolFilter);
    }

    // Show success message
    flash({msg: 'Filters applied successfully', type: 'success'});
}

function clearStudentFilters() {
    $('#filter_class, #filter_gender, #filter_year, #filter_school').val('').trigger('change');
    flash({msg: 'Filters cleared', type: 'info'});
}

// Update classes based on selected school
function updateClassesBySchool(schoolId) {
    if (!schoolId) {
        $('#my_class_id, #filter_class').html('<option value="">Select School First</option>');
        return;
    }

    $.ajax({
        url: '/ajax/get_school_classes/' + schoolId,
        type: 'GET',
        success: function(data) {
            var options = '<option value="">Select Class</option>';
            $.each(data, function(key, myClass) {
                options += '<option value="' + myClass.id + '">' + myClass.name + '</option>';
            });
            $('#my_class_id, #filter_class').html(options);
        },
        error: function() {
            flash({msg: 'Error loading classes for selected school', type: 'danger'});
        }
    });
}

// Student Promotion Filters
function applyPromotionFilters() {
    var fromClass = $('#promotion_filter_from_class').val();
    var toClass = $('#promotion_filter_to_class').val();
    var status = $('#promotion_filter_status').val();
    var performance = $('#promotion_filter_performance').val();
    var schoolFilter = $('#promotion_filter_school').val();

    // Hide/show promotion rows based on filters
    $('table tbody tr').show();

    if (schoolFilter) {
        $('table tbody tr').each(function() {
            var rowSchool = $(this).data('school-id');
            if (rowSchool && rowSchool != schoolFilter) {
                $(this).hide();
            }
        });
    }

    if (fromClass) {
        $('table tbody tr:visible').each(function() {
            var rowFromClass = $(this).data('from-class');
            if (rowFromClass && rowFromClass != fromClass) {
                $(this).hide();
            }
        });
    }

    if (toClass) {
        $('table tbody tr:visible').each(function() {
            var rowToClass = $(this).data('to-class');
            if (rowToClass && rowToClass != toClass) {
                $(this).hide();
            }
        });
    }

    if (status) {
        $('table tbody tr:visible').each(function() {
            var select = $(this).find('select');
            var currentStatus = select.val();

            if (status === 'eligible' && currentStatus !== 'P') {
                $(this).hide();
            } else if (status === 'repeat' && currentStatus !== 'D') {
                $(this).hide();
            } else if (status === 'graduate' && currentStatus !== 'G') {
                $(this).hide();
            }
        });
    }

    var visibleRows = $('table tbody tr:visible').length;
    flash({msg: visibleRows + ' students match your promotion filters', type: 'info'});
}

function clearPromotionFilters() {
    $('#promotion_filter_from_class, #promotion_filter_to_class, #promotion_filter_status, #promotion_filter_performance, #promotion_filter_school').val('');
    $('table tbody tr').show();
    flash({msg: 'All promotion filters cleared', type: 'info'});
}

// Graduated Students Filters
function applyGraduatedFilters() {
    var year = $('#graduated_filter_year').val();
    var className = $('#graduated_filter_class').val();
    var gender = $('#graduated_filter_gender').val();
    var performance = $('#graduated_filter_performance').val();
    var name = $('#graduated_filter_name').val().toLowerCase();
    var schoolFilter = $('#graduated_filter_school').val();

    $('table tbody tr').show();

    $('table tbody tr').each(function() {
        var row = $(this);
        var shouldHide = false;

        // Filter by school
        if (schoolFilter && !shouldHide) {
            var schoolId = row.data('school-id');
            if (schoolId && schoolId != schoolFilter) {
                shouldHide = true;
            }
        }

        // Filter by graduation year
        if (year && !shouldHide) {
            var gradYear = row.find('td:nth-child(6)').text().trim();
            if (!gradYear.includes(year)) {
                shouldHide = true;
            }
        }

        // Filter by class
        if (className && !shouldHide) {
            var classText = row.find('td:nth-child(5)').text().trim();
            if (!classText.includes(className)) {
                shouldHide = true;
            }
        }

        // Filter by name
        if (name && !shouldHide) {
            var studentName = row.find('td:nth-child(3)').text().toLowerCase().trim();
            if (!studentName.includes(name)) {
                shouldHide = true;
            }
        }

        if (shouldHide) {
            row.hide();
        }
    });

    var visibleRows = $('table tbody tr:visible').length;
    flash({msg: visibleRows + ' graduated students match your filters', type: 'success'});
}

function clearGraduatedFilters() {
    $('#graduated_filter_year, #graduated_filter_class, #graduated_filter_gender, #graduated_filter_performance, #graduated_filter_name, #graduated_filter_school').val('');
    $('table tbody tr').show();
    flash({msg: 'All graduated filters cleared', type: 'info'});
}

function getClassSections(classId, sectionSelect = '#section_id') {
    if (!classId) {
        $(sectionSelect).html('<option value="">Select Class First</option>');
        return;
    }

    $.ajax({
        url: '/ajax/get_class_sections/' + classId,
        type: 'GET',
        success: function(data) {
            var options = '<option value="">Select Section</option>';
            $.each(data, function(key, section) {
                options += '<option value="' + section.id + '">' + section.name + '</option>';
            });
            $(sectionSelect).html(options);
        },
        error: function() {
            flash({msg: 'Error loading sections', type: 'danger'});
        }
    });
}

function exportGraduatedStudents() {
    var visibleRows = $('table tbody tr:visible');
    if (visibleRows.length === 0) {
        flash({msg: 'No students to export', type: 'warning'});
        return;
    }

    // Create CSV content
    var csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "S/N,Name,ADM_No,Section,Grad Year\n";

    visibleRows.each(function(index) {
        var row = $(this);
        var data = [
            index + 1,
            row.find('td:nth-child(3)').text().trim(),
            row.find('td:nth-child(4)').text().trim(),
            row.find('td:nth-child(5)').text().trim(),
            row.find('td:nth-child(6)').text().trim()
        ];
        csvContent += data.join(",") + "\n";
    });

    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "graduated_students.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function printGraduatedList() {
    var printContent = $('table').clone();
    printContent.find('tbody tr:hidden').remove();
    printContent.find('th:last-child, td:last-child').remove();

    var printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Graduated Students List</title>
            <style>
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                @media print { button { display: none; } }
            </style>
        </head>
        <body>
            <h2>Graduated Students List</h2>
            ${printContent[0].outerHTML}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Bulk promotion actions
function bulkPromoteStudents(action) {
    var selectedStudents = $('input[name="selected_students[]"]:checked');
    if (selectedStudents.length === 0) {
        flash({msg: 'Please select students to promote', type: 'warning'});
        return;
    }

    selectedStudents.each(function() {
        var studentId = $(this).val();
        $('#p-' + studentId).val(action);
    });

    var actionText = action === 'P' ? 'promoted' : (action === 'D' ? 'set to repeat' : 'graduated');
    flash({msg: selectedStudents.length + ' students ' + actionText, type: 'success'});
}

// Initialize filters on page load
$(document).ready(function() {
    // Auto-hide filter panels initially
    $('#advancedFilters, #promotionFilters, #graduatedFilters').addClass('show');

    // Add data attributes to promotion table rows for filtering
    if ($('table tbody tr').length > 0) {
        $('table tbody tr').each(function(index) {
            $(this).attr('data-index', index);
        });
    }

    // Enable real-time search for graduated students
    $('#graduated_filter_name').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        if (searchTerm.length > 2 || searchTerm.length === 0) {
            applyGraduatedFilters();
        }
    });

    // School filter change events
    $('#filter_school, #promotion_filter_school, #graduated_filter_school').on('change', function() {
        var schoolId = $(this).val();
        if ($(this).attr('id') === 'filter_school') {
            updateClassesBySchool(schoolId);
        }
    });

    // Add select all functionality for promotions
    $('#select_all_students').on('change', function() {
        $('input[name="selected_students[]"]:visible').prop('checked', this.checked);
    });
});
