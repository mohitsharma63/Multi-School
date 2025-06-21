<form method="post" action="{{ route('students.promotion_selector') }}">
    @csrf

    @if(Qs::userIsSuperAdmin() && $schools->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="school_filter" class="col-form-label font-weight-bold">Filter by School:</label>
                <select id="school_filter" class="form-control select">
                    <option value="">All Schools</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="fc" class="col-form-label font-weight-bold">From Class:</label>
                <select required id="fc" name="fc" class="form-control select">
                    <option value="">Select Class</option>
                    @foreach($my_classes as $c)
                        <option {{ ($selected && $fc == $c->id) ? 'selected' : '' }}
                                value="{{ $c->id }}"
                                data-school-id="{{ $c->school_id ?? '' }}">
                            {{ $c->name }}
                            @if(Qs::userIsSuperAdmin() && $c->school)
                                ({{ $c->school->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="fs" class="col-form-label font-weight-bold">From Section:</label>
                <select required id="fs" name="fs" class="form-control select-search">
                    <option value="">Select Section</option>
                    @foreach($sections as $s)
                        <option {{ ($selected && $fs == $s->id) ? 'selected' : '' }}
                                value="{{ $s->id }}"
                                data-class-id="{{ $s->my_class_id ?? '' }}">
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="tc" class="col-form-label font-weight-bold">To Class:</label>
                <select required id="tc" name="tc" class="form-control select">
                    <option value="">Select Class</option>
                    @foreach($my_classes as $c)
                        <option {{ ($selected && $tc == $c->id) ? 'selected' : '' }}
                                value="{{ $c->id }}"
                                data-school-id="{{ $c->school_id ?? '' }}">
                            {{ $c->name }}
                            @if(Qs::userIsSuperAdmin() && $c->school)
                                ({{ $c->school->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="ts" class="col-form-label font-weight-bold">To Section:</label>
                <select required id="ts" name="ts" class="form-control select-search">
                    <option value="">Select Section</option>
                    @foreach($sections as $s)
                        <option {{ ($selected && $ts == $s->id) ? 'selected' : '' }}
                                value="{{ $s->id }}"
                                data-class-id="{{ $s->my_class_id ?? '' }}">
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-primary">Manage Promotion <i class="icon-paperplane ml-2"></i></button>
    </div>

</form>

<script>
$(document).ready(function() {
    // School filter functionality for super admin
    $('#school_filter').on('change', function() {
        var schoolId = $(this).val();

        // Filter from and to classes
        $('#fc, #tc').find('option').each(function() {
            var optionSchoolId = $(this).data('school-id');
            if (!schoolId || !optionSchoolId || optionSchoolId == schoolId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Reset selections and update sections
        $('#fc, #tc').val('');
        $('#fs, #ts').html('<option value="">Select Class First</option>');

        // Show message about selected school
        if (schoolId) {
            var schoolName = $(this).find('option:selected').text();
            showNotification('Filtered classes for: ' + schoolName, 'info');
        } else {
            showNotification('Showing all schools', 'info');
        }
    });

    // Enhanced class change functionality
    $('#fc, #tc').on('change', function() {
        var classId = $(this).val();
        var targetSectionId = $(this).attr('id') === 'fc' ? '#fs' : '#ts';

        if (!classId) {
            $(targetSectionId).html('<option value="">Select Class First</option>');
            return;
        }

        // Filter sections by class
        var sections = '<option value="">Select Section</option>';
        @foreach($sections as $section)
            if ({{ $section->my_class_id ?? 'null' }} == classId) {
                sections += '<option value="{{ $section->id }}">{{ $section->name }}</option>';
            }
        @endforeach

        $(targetSectionId).html(sections);
    });

    // Validation before form submission
    $('form').on('submit', function(e) {
        var fc = $('#fc').val();
        var fs = $('#fs').val();
        var tc = $('#tc').val();
        var ts = $('#ts').val();

        if (!fc || !fs || !tc || !ts) {
            e.preventDefault();
            showNotification('Please select all required fields', 'danger');
            return false;
        }

        // Check if promoting within same class and section
        if (fc === tc && fs === ts) {
            e.preventDefault();
            showNotification('Cannot promote students to the same class and section', 'warning');
            return false;
        }

        return true;
    });

    function showNotification(message, type) {
        var alertClass = 'alert-' + (type === 'danger' ? 'danger' : type === 'warning' ? 'warning' : type === 'success' ? 'success' : 'info');
        var notification = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                          message +
                          '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                          '<span aria-hidden="true">&times;</span>' +
                          '</button>' +
                          '</div>';

        // Remove existing alerts
        $('.alert').remove();

        // Prepend to the form
        $('form').prepend(notification);

        // Auto-dismiss after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 3000);
    }

    // Update sections when class changes
    $('#fc, #tc').on('change', function() {
        updateSections();
    });

    function updateSections() {
        var fcValue = $('#fc').val();
        var tcValue = $('#tc').val();

        // Update from sections
        $('#fs').find('option').each(function() {
            var sectionClassId = $(this).data('class-id');
            if (!fcValue || !sectionClassId || sectionClassId == fcValue) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Update to sections
        $('#ts').find('option').each(function() {
            var sectionClassId = $(this).data('class-id');
            if (!tcValue || !sectionClassId || sectionClassId == tcValue) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Reset section selections if needed
        if (fcValue && $('#fs').val()) {
            var selectedSectionClassId = $('#fs').find('option:selected').data('class-id');
            if (selectedSectionClassId != fcValue) {
                $('#fs').val('');
            }
        }

        if (tcValue && $('#ts').val()) {
            var selectedSectionClassId = $('#ts').find('option:selected').data('class-id');
            if (selectedSectionClassId != tcValue) {
                $('#ts').val('');
            }
        }
    }
});
</script>
