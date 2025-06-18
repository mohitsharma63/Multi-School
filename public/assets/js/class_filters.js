
// Advanced Class Filters
var ClassFilters = {
    init: function() {
        this.bindEvents();
        this.initializeSelects();
    },

    bindEvents: function() {
        var self = this;

        // Apply filters button
        $('#apply-filters').on('click', function(e) {
            e.preventDefault();
            self.applyFilters();
        });

        // Reset filters button
        $('#reset-filters').on('click', function(e) {
            e.preventDefault();
            self.resetFilters();
        });

        // Auto-apply filters on change
        $('#school-filter, #class-type-filter').on('change', function() {
            self.applyFilters();
        });
    },

    initializeSelects: function() {
        if ($.fn.select2) {
            $('.select').select2({
                minimumResultsForSearch: Infinity,
                placeholder: function() {
                    return $(this).data('placeholder');
                }
            });
        }
    },

    applyFilters: function() {
        var schoolFilter = $('#school-filter').val();
        var classTypeFilter = $('#class-type-filter').val();
        var visibleCount = 0;

        // Check if DataTable exists and is initialized
        if (typeof classesTable !== 'undefined' && classesTable && $.fn.DataTable.isDataTable('#classes-table')) {
            // Use DataTable's built-in filtering
            classesTable.draw();
        } else {
            // Fallback to manual row filtering
            $('.class-row').each(function() {
                var $row = $(this);
                var schoolId = $row.data('school-id');
                var classTypeId = $row.data('class-type-id');
                var showRow = true;

                // Apply school filter
                if (schoolFilter && schoolId != schoolFilter) {
                    showRow = false;
                }

                // Apply class type filter
                if (classTypeFilter && classTypeId != classTypeFilter) {
                    showRow = false;
                }

                // Show/hide row
                if (showRow) {
                    $row.show();
                    visibleCount++;
                } else {
                    $row.hide();
                }
            });
        }

        this.showNotification('Filters applied successfully.', 'success');
    },

    resetFilters: function() {
        // Reset select values
        $('#school-filter').val('').trigger('change');
        $('#class-type-filter').val('').trigger('change');

        // Clear DataTable filters if exists
        if (typeof classesTable !== 'undefined' && classesTable && $.fn.DataTable.isDataTable('#classes-table')) {
            classesTable.search('').draw();
        }

        // Show all rows
        $('.class-row').show();

        this.showNotification('All filters cleared.', 'info');
    },

    showNotification: function(message, type) {
        if (typeof flash_msg !== 'undefined') {
            flash_msg(message, type);
        } else if (typeof PNotify !== 'undefined') {
            new PNotify({
                title: 'Filter Status',
                text: message,
                type: type,
                delay: 3000
            });
        } else {
            console.log(type.toUpperCase() + ': ' + message);
        }
    }
};

// Initialize when document is ready
$(document).ready(function() {
    if ($('#school-filter').length > 0) {
        ClassFilters.init();
    }
});
