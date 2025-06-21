
<form method="post" action="{{ route('students.promote', [$fc, $fs, $tc, $ts]) }}">
    @csrf

    <!-- Bulk Operations Panel -->
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="card-title">Bulk Operations</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Select All:</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="select_all_students">
                            <label class="form-check-label" for="select_all_students">
                                Select All Students
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        <label>Bulk Actions:</label>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-sm" onclick="bulkPromoteStudents('P')">
                                <i class="icon-stairs-up"></i> Bulk Promote
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" onclick="bulkPromoteStudents('D')">
                                <i class="icon-stairs-down"></i> Bulk Repeat
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="bulkPromoteStudents('G')">
                                <i class="icon-graduation"></i> Bulk Graduate
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="table-responsive">
        <table class="table table-striped" id="promotion-table">
            <thead>
                <tr>
                    <th width="5%">
                        <input type="checkbox" id="master_select_all" class="form-check-input">
                    </th>
                    <th width="5%">#</th>
                    <th width="10%">Photo</th>
                    <th width="25%">Name</th>
                    <th width="15%">Admission No</th>
                    <th width="15%">Current Session</th>
                    @if(Qs::userIsSuperAdmin())
                    <th width="15%">School</th>
                    @endif
                    <th width="10%">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students->sortBy('user.name') as $sr)
                    <tr data-school-id="{{ $sr->my_class->school_id ?? '' }}"
                        data-from-class="{{ $sr->my_class_id }}"
                        data-to-class="{{ $tc }}"
                        data-student-id="{{ $sr->id }}">
                        <td>
                            <input type="checkbox" name="selected_students[]" value="{{ $sr->id }}" class="form-check-input student-checkbox">
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <img class="rounded-circle" style="height: 40px; width: 40px;"
                                 src="{{ $sr->user->photo }}" alt="Student Photo">
                        </td>
                        <td>
                            <strong>{{ $sr->user->name }}</strong>
                            <br><small class="text-muted">{{ $sr->user->email }}</small>
                        </td>
                        <td>{{ $sr->adm_no ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-primary">{{ $sr->session }}</span>
                        </td>
                        @if(Qs::userIsSuperAdmin())
                        <td>
                            <span class="badge badge-secondary">{{ $sr->my_class->school->name ?? 'N/A' }}</span>
                        </td>
                        @endif
                        <td>
                            <select class="form-control select-sm promotion-action"
                                    name="p-{{$sr->id}}"
                                    id="p-{{$sr->id}}"
                                    data-student-id="{{ $sr->id }}">
                                <option value="P" class="text-success">✓ Promote</option>
                                <option value="D" class="text-warning">↻ Don't Promote</option>
                                <option value="G" class="text-info">🎓 Graduated</option>
                            </select>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Summary Panel -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h5 class="text-success mb-1" id="promote-count">0</h5>
                        <p class="mb-0">To Promote</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h5 class="text-warning mb-1" id="repeat-count">0</h5>
                        <p class="mb-0">To Repeat</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h5 class="text-info mb-1" id="graduate-count">0</h5>
                        <p class="mb-0">To Graduate</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h5 class="text-primary mb-1" id="total-students">{{ $students->count() }}</h5>
                        <p class="mb-0">Total Students</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="icon-stairs-up mr-2"></i> Process Student Promotions
        </button>
        <a href="{{ route('students.promotion') }}" class="btn btn-secondary btn-lg ml-2">
            <i class="icon-arrow-left mr-2"></i> Back to Selector
        </a>
    </div>
</form>

<script>
$(document).ready(function() {
    // Master select all functionality
    $('#master_select_all').on('change', function() {
        $('.student-checkbox').prop('checked', this.checked);
        updateCounts();
    });

    // Individual checkbox functionality
    $('.student-checkbox').on('change', function() {
        var totalCheckboxes = $('.student-checkbox').length;
        var checkedCheckboxes = $('.student-checkbox:checked').length;

        $('#master_select_all').prop('checked', totalCheckboxes === checkedCheckboxes);
        updateCounts();
    });

    // Update counts when promotion action changes
    $('.promotion-action').on('change', function() {
        updateCounts();
    });

    // Initial count update
    updateCounts();

    function updateCounts() {
        var promoteCount = $('select[name^="p-"] option:selected[value="P"]').length;
        var repeatCount = $('select[name^="p-"] option:selected[value="D"]').length;
        var graduateCount = $('select[name^="p-"] option:selected[value="G"]').length;

        $('#promote-count').text(promoteCount);
        $('#repeat-count').text(repeatCount);
        $('#graduate-count').text(graduateCount);
    }
});

// Bulk promotion functions
function bulkPromoteStudents(action) {
    var checkedStudents = $('.student-checkbox:checked');

    if (checkedStudents.length === 0) {
        alert('Please select at least one student first.');
        return;
    }

    var actionText = {
        'P': 'promote',
        'D': 'repeat',
        'G': 'graduate'
    };

    if (confirm('Are you sure you want to ' + actionText[action] + ' ' + checkedStudents.length + ' selected student(s)?')) {
        checkedStudents.each(function() {
            var studentId = $(this).val();
            $('#p-' + studentId).val(action);
        });
        updateCounts();

        // Show success message
        var message = checkedStudents.length + ' student(s) set to be ' + actionText[action] + 'd.';
        showNotification(message, 'success');
    }
}

function showNotification(message, type) {
    // Simple notification - you can enhance this with a proper notification library
    var alertClass = 'alert-' + (type === 'success' ? 'success' : 'info');
    var notification = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                      message +
                      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                      '<span aria-hidden="true">&times;</span>' +
                      '</button>' +
                      '</div>';

    // Prepend to the form
    $('form').prepend(notification);

    // Auto-dismiss after 3 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 3000);
}
</script>

<style>
.select-sm {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
}

.promotion-action option {
    padding: 5px;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}

#promotion-table {
    font-size: 0.9rem;
}

.card-title {
    font-weight: 600;
    color: #333;
}
</style>
