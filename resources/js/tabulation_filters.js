
$(document).ready(function() {
    // Initialize advanced filters
    initializeTabulationFilters();

    // Apply filters
    $('#apply-filters').on('click', function() {
        applyTabulationFilters();
    });

    // Reset filters
    $('#reset-filters').on('click', function() {
        resetTabulationFilters();
    });
});

function initializeTabulationFilters() {
    // Initialize select2 if available
    if ($.fn.select2) {
        $('.select').select2({
            minimumResultsForSearch: Infinity
        });
    }
}

function filterClassesBySchool(schoolId) {
    var classFilter = $('#my_class_id');
    var classOptions = classFilter.find('option');
    var examFilter = $('#exam_id');
    var examOptions = examFilter.find('option');

    // Show all options first
    classOptions.show();
    examOptions.show();

    if (schoolId) {
        // Hide class options that don't match the selected school
        classOptions.each(function() {
            var option = $(this);
            var optionSchoolId = option.data('school-id');

            if (optionSchoolId && optionSchoolId != schoolId) {
                option.hide();
            }
        });

        // Hide exam options that don't match the selected school
        examOptions.each(function() {
            var option = $(this);
            var optionSchoolId = option.data('school-id');

            if (optionSchoolId && optionSchoolId != schoolId) {
                option.hide();
            }
        });
    }

    // Reset selections
    classFilter.val('').trigger('change');
    examFilter.val('').trigger('change');
    $('#section_id').val('').trigger('change');
}

function applyTabulationFilters() {
    var schoolFilter = $('#school-filter').val();
    var examFilter = $('#exam-filter').val();

    // Update form dropdowns based on filters
    if (schoolFilter) {
        filterClassesBySchool(schoolFilter);
    }

    if (examFilter) {
        $('#exam_id').val(examFilter).trigger('change');
    }

    showNotification('Filters applied successfully!', 'success');
}

function resetTabulationFilters() {
    // Reset all filter dropdowns
    $('#school-filter, #exam-filter').val('').trigger('change');

    // Show all options in form dropdowns
    $('#exam_id option, #my_class_id option, #section_id option').show();

    // Reset form selections
    $('#exam_id, #my_class_id, #section_id').val('').trigger('change');

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
