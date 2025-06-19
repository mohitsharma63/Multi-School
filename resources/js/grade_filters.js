
var GradeFilters = {
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
        $('#school-filter, #grade-type-filter').on('change', function() {
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
        var gradeTypeFilter = $('#grade-type-filter').val();
        var visibleCount = 0;

        // Check if DataTable exists and is initialized
        if (typeof gradesTable !== 'undefined' && gradesTable && $.fn.DataTable.isDataTable('.datatable-button-html5-columns')) {
            // Use DataTable's built-in filtering
            gradesTable.draw();
        } else {
            // Fallback to manual row filtering
            $('.grade-row').each(function() {
                var $row = $(this);
                var schoolId = $row.data('school-id');
                var gradeTypeId = $row.data('grade-type-id');
                var showRow = true;

                // Apply school filter
                if (schoolFilter && schoolId != schoolFilter) {
                    showRow = false;
                }

                // Apply grade type filter
                if (gradeTypeFilter) {
                    if (gradeTypeFilter === 'not_applicable' && gradeTypeId !== 'not_applicable') {
                        showRow = false;
                    } else if (gradeTypeFilter !== 'not_applicable' && gradeTypeId != gradeTypeFilter) {
                        showRow = false;
                    }
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
        $('#school-filter, #grade-type-filter').val('').trigger('change');

        // Clear DataTable filters if exists
        if (typeof gradesTable !== 'undefined' && gradesTable && $.fn.DataTable.isDataTable('.datatable-button-html5-columns')) {
            gradesTable.search('').draw();
        }

        // Show all rows
        $('.grade-row').show();

        this.showNotification('All filters cleared.', 'info');
    },

    showNotification: function(message, type) {
        if (typeof new_noty !== 'undefined') {
            new_noty(message, type);
        }
    }
};

// Initialize when document is ready
$(document).ready(function() {
    GradeFilters.init();
});
