
// Advanced Dorm Filters
var DormFilters = {
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
        $('#school-filter').on('change', function() {
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
        var visibleCount = 0;

        // Check if DataTable exists and is initialized
        if (typeof dormsTable !== 'undefined' && dormsTable && $.fn.DataTable.isDataTable('#dorms-table')) {
            // Use DataTable's built-in filtering
            dormsTable.draw();
        } else {
            // Fallback to manual row filtering
            $('.dorm-row').each(function() {
                var $row = $(this);
                var schoolId = $row.data('school-id');
                var showRow = true;

                // Apply school filter
                if (schoolFilter && schoolId != schoolFilter) {
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

        // Clear DataTable filters if exists
        if (typeof dormsTable !== 'undefined' && dormsTable && $.fn.DataTable.isDataTable('#dorms-table')) {
            dormsTable.search('').draw();
        }

        // Show all rows
        $('.dorm-row').show();

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
        DormFilters.init();
    }
});
