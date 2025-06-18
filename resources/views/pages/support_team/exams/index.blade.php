@extends('layouts.master')
@section('page_title', 'Manage Exams')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Exams</h6>
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
                                <div class="col-md-6">
                                    <label>Filter by School:</label>
                                    <select id="school-filter" class="form-control select" onchange="filterExamsBySchool(this.value)">
                                        <option value="">All Schools</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-end">
                                        <button type="button" class="btn btn-primary mr-2" onclick="applyExamFilters()">
                                            <i class="icon-filter4 mr-1"></i> Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-light" onclick="resetExamFilters()">
                                            <i class="icon-reload-alt mr-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-exams" class="nav-link active" data-toggle="tab">Manage Exam</a></li>
                <li class="nav-item"><a href="#new-exam" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Add Exam</a></li>
            </ul>

            <div class="tab-content">
                    <div class="tab-pane fade show active" id="all-exams">
                        <table class="table datatable-button-html5-columns" id="exams-table">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>School</th>
                                <th>Term</th>
                                <th>Session</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($exams as $ex)
                                <tr class="exam-row" data-school-id="{{ $ex->school_id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $ex->name }}</td>
                                    <td>
                                        @if($ex->school_id)
                                            {{ $schools->where('id', $ex->school_id)->first()->name ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ 'Term '.$ex->term }}</td>
                                    <td>{{ $ex->year }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    @if(Qs::userIsTeamSA())
                                                    {{--Edit--}}
                                                    <a href="{{ route('exams.edit', $ex->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                   @endif
                                                    @if(Qs::userIsSuperAdmin())
                                                    {{--Delete--}}
                                                    <a id="{{ $ex->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                    <form method="post" id="item-delete-{{ $ex->id }}" action="{{ route('exams.destroy', $ex->id) }}" class="hidden">@csrf @method('delete')</form>
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

                <div class="tab-pane fade" id="new-exam">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span>You are creating an Exam for the Current Session <strong>{{ Qs::getSetting('current_session') }}</strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <form method="post" action="{{ route('exams.store') }}">
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
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="name" value="{{ old('name') }}" required type="text" class="form-control" placeholder="Name of Exam">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="term" class="col-lg-3 col-form-label font-weight-semibold">Term</label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="Select Term" class="form-control select-search" name="term" id="term">
                                            <option {{ old('term') == 1 ? 'selected' : '' }} value="1">First Term</option>
                                            <option {{ old('term') == 2 ? 'selected' : '' }} value="2">Second Term</option>
                                            <option {{ old('term') == 3 ? 'selected' : '' }} value="3">Third Term</option>
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
            </div>
        </div>
    </div>

<script>
$(document).ready(function() {
    // Initialize select2 if available
    if ($.fn.select2) {
        $('.select').select2({
            minimumResultsForSearch: Infinity
        });
    }
});

function filterExamsBySchool(schoolId) {
    var examFilter = $('#school-filter');

    // Apply filter will be called automatically
    applyExamFilters();
}

function applyExamFilters() {
    var schoolFilter = $('#school-filter').val();
    var visibleCount = 0;

    // Check if DataTable exists and is initialized
    if (typeof examsTable !== 'undefined' && examsTable && $.fn.DataTable.isDataTable('#exams-table')) {
        // Use DataTable's built-in filtering
        examsTable.draw();
    } else {
        // Fallback to manual row filtering
        $('.exam-row').each(function() {
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

    showNotification('Filters applied successfully.', 'success');
}

function resetExamFilters() {
    // Reset select values
    $('#school-filter').val('').trigger('change');

    // Clear DataTable filters if exists
    if (typeof examsTable !== 'undefined' && examsTable && $.fn.DataTable.isDataTable('#exams-table')) {
        examsTable.search('').draw();
    }

    // Show all rows
    $('.exam-row').show();

    showNotification('All filters cleared.', 'info');
}

function showNotification(message, type) {
    // Use your existing notification system here
    // This is a placeholder - replace with your actual notification function
    if (typeof PNotify !== 'undefined') {
        new PNotify({
            title: 'Filter Status',
            text: message,
            type: type,
            styling: 'bootstrap3'
        });
    }
}
</script>

@endsection
