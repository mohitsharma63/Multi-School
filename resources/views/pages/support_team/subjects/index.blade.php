@extends('layouts.master')
@section('page_title', 'Manage Subjects')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Subjects</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <!-- Advanced Filter Section -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">
                                <i class="icon-filter4 mr-2"></i>Advanced Filters
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Filter by School:</label>
                                    <select id="school-filter" class="form-control select" onchange="filterClassesBySchool(this.value)">
                                        <option value="">All Schools</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Filter by Class:</label>
                                    <select id="class-filter" class="form-control select" onchange="filterSubjectsByClass(this.value)">
                                        <option value="">All Classes</option>
                                        @foreach($my_classes as $c)
                                            <option value="{{ $c->id }}" data-school-id="{{ $c->school_id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mt-4">
                                        <button type="button" class="btn btn-primary" onclick="applySubjectFilters()">
                                            <i class="icon-filter4 mr-2"></i>Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-light ml-2" onclick="resetSubjectFilters()">
                                            <i class="icon-reload-alt mr-2"></i>Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#new-subject" class="nav-link active" data-toggle="tab">Add Subject</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Manage Subjects</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($my_classes as $c)
                            <a href="#c{{ $c->id }}" class="dropdown-item" data-toggle="tab">{{ $c->name }}</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane show  active fade" id="new-subject">
                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="{{ route('subjects.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label for="school_id" class="col-lg-3 col-form-label font-weight-semibold">Select School <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select School" class="form-control select" name="school_id" id="school_id">
                                            <option value="">Select School</option>
                                            @foreach($schools as $school)
                                                <option {{ old('school_id') == $school->id ? 'selected' : '' }} value="{{ $school->id }}">{{ $school->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="name" class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input id="name" name="name" value="{{ old('name') }}" required type="text" class="form-control" placeholder="Name of subject">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="slug" class="col-lg-3 col-form-label font-weight-semibold">Short Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input id="slug" required name="slug" value="{{ old('slug') }}" type="text" class="form-control" placeholder="Eg. Math, ENG">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="my_class_id" class="col-lg-3 col-form-label font-weight-semibold">Select Class <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select Class" class="form-control select" name="my_class_id" id="my_class_id">
                                            <option value="">Select School First</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="teacher_id" class="col-lg-3 col-form-label font-weight-semibold">Teacher <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select Teacher" class="form-control select-search" name="teacher_id" id="teacher_id">
                                            <option value=""></option>
                                            @foreach($teachers as $t)
                                                <option {{ old('teacher_id') == Qs::hash($t->id) ? 'selected' : '' }} value="{{ Qs::hash($t->id) }}">{{ $t->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @foreach($my_classes as $c)
                    <div class="tab-pane fade" id="c{{ $c->id }}">                         <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Short Name</th>
                                <th>Class</th>
                                <th>Teacher</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($subjects->where('my_class.id', $c->id) as $s)
                                <tr class="subject-row" data-school-id="{{ $s->my_class->school_id ?? '' }}" data-class-id="{{ $s->my_class->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $s->name }} </td>
                                    <td>{{ $s->slug }} </td>
                                    <td>{{ $s->my_class->name }}</td>
                                    <td>{{ $s->teacher->name }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    {{--edit--}}
                                                    @if(Qs::userIsTeamSA())
                                                        <a href="{{ route('subjects.edit', $s->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                    @endif
                                                    {{--Delete--}}
                                                    @if(Qs::userIsSuperAdmin())
                                                        <a id="{{ $s->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                        <form method="post" id="item-delete-{{ $s->id }}" action="{{ route('subjects.destroy', $s->id) }}" class="hidden">@csrf @method('delete')</form>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    {{--subject List Ends--}}

<script>
$(document).ready(function() {
    // Initialize select2 if available
    if ($.fn.select2) {
        $('.select').select2({
            minimumResultsForSearch: Infinity
        });
    }

    // Handle school selection change
    $('#school_id').on('change', function() {
        var schoolId = $(this).val();
        loadClassesBySchool(schoolId);
    });
});

function loadClassesBySchool(schoolId) {
    if (!schoolId) {
        $('#my_class_id').html('<option value="">Select School First</option>');
        return;
    }

    // Show loading
    $('#my_class_id').html('<option value="">Loading classes...</option>');

    // AJAX call to get classes by school
    $.ajax({
        url: '/ajax/get-classes-by-school/' + schoolId,
        type: 'GET',
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

            // Reinitialize select2 if available
            if ($.fn.select2) {
                $('#my_class_id').select2({
                    minimumResultsForSearch: Infinity
                });
            }
        },
        error: function() {
            $('#my_class_id').html('<option value="">Error loading classes</option>');
            showNotification('Error loading classes. Please try again.', 'error');
        }
    });
}

function filterClassesBySchool(schoolId) {
    var classFilter = $('#class-filter');
    var classOptions = classFilter.find('option');

    // Show all options first
    classOptions.show();

    if (schoolId) {
        // Hide options that don't match the selected school
        classOptions.each(function() {
            var option = $(this);
            var optionSchoolId = option.data('school-id');

            if (optionSchoolId && optionSchoolId != schoolId) {
                option.hide();
            }
        });
    }

    // Reset class filter selection
    classFilter.val('').trigger('change');
}

function filterSubjectsByClass(classId) {
    // This will be used to filter subjects display
    applySubjectFilters();
}

function applySubjectFilters() {
    var schoolFilter = $('#school-filter').val();
    var classFilter = $('#class-filter').val();

    // Filter subject rows
    $('.subject-row').each(function() {
        var row = $(this);
        var rowSchoolId = row.data('school-id');
        var rowClassId = row.data('class-id');
        var showRow = true;

        // Apply school filter
        if (schoolFilter && rowSchoolId != schoolFilter) {
            showRow = false;
        }

        // Apply class filter
        if (classFilter && rowClassId != classFilter) {
            showRow = false;
        }

        if (showRow) {
            row.show();
        } else {
            row.hide();
        }
    });

    // Filter tabs
    $('.nav-tabs .dropdown-menu .dropdown-item').each(function() {
        var tab = $(this);
        var tabClassId = tab.attr('href').replace('#c', '');
        var showTab = true;

        if (classFilter && tabClassId != classFilter) {
            showTab = false;
        }

        if (showTab) {
            tab.show();
        } else {
            tab.hide();
        }
    });

    showNotification('Filters applied successfully!', 'success');
}

function resetSubjectFilters() {
    $('#school-filter').val('').trigger('change');
    $('#class-filter').val('').trigger('change');

    // Show all rows and tabs
    $('.subject-row').show();
    $('.nav-tabs .dropdown-menu .dropdown-item').show();

    showNotification('Filters reset successfully!', 'success');
}

function showNotification(message, type) {
    // Add your notification implementation here
    console.log(type + ': ' + message);
}
</script>

@endsection
