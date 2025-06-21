@extends('layouts.master')
@section('page_title', 'Manage Classes')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Classes</h6>
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
                                    @if(auth()->user()->user_type == 'admin' && auth()->user()->school_id)
                                        <!-- Admin users can only filter by their assigned school -->
                                        <select id="school-filter" class="form-control select" readonly disabled>
                                            @foreach($schools as $school)
                                                @if($school->id == auth()->user()->school_id)
                                                    <option selected value="{{ $school->id }}">{{ $school->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    @else
                                        <!-- Super admin and team members can filter by any school -->
                                        <select id="school-filter" class="form-control select">
                                            <option value="">All Schools</option>
                                            @foreach($schools as $school)
                                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label>Filter by Class Type:</label>
                                    <select id="class-type-filter" class="form-control select">
                                        <option value="">All Class Types</option>
                                        @foreach($class_types as $ct)
                                            <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" id="apply-filters" class="btn btn-primary">
                                            <i class="icon-filter4"></i> Apply Filters
                                        </button>
                                        <button type="button" id="reset-filters" class="btn btn-light ml-2">
                                            <i class="icon-reset"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-classes" class="nav-link active" data-toggle="tab">Manage Classes</a></li>
                <li class="nav-item"><a href="#new-class" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Create New Class</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="all-classes">
                    <table class="table datatable-button-html5-columns" id="classes-table">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>School</th>
                            <th>Class Type</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($my_classes as $c)
                            <tr class="class-row" data-school-id="{{ $c->school_id ?? '' }}" data-class-type-id="{{ $c->class_type_id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $c->name }}</td>
                                <td>
                                    @if($c->school)
                                        <span class="badge badge-info">{{ $c->school->name }}</span>
                                    @else
                                        <span class="text-muted">No School Assigned</span>
                                    @endif
                                </td>
                                <td>{{ $c->class_type->name }}</td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
                                                @if(Qs::userIsTeamSA())
                                                {{--Edit--}}
                                                <a href="{{ route('classes.edit', $c->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                               @endif
                                                    @if(Qs::userIsSuperAdmin())
                                                {{--Delete--}}
                                                <a id="{{ $c->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                <form method="post" id="item-delete-{{ $c->id }}" action="{{ route('classes.destroy', $c->id) }}" class="hidden">@csrf @method('delete')</form>
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

                <div class="tab-pane fade" id="new-class">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                <span>When a class is created, a Section will be automatically created for the class, you can edit it or add more sections to the class at <a target="_blank" href="{{ route('sections.index') }}">Manage Sections</a></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <form class="ajax-store" method="post" action="{{ route('classes.store') }}">
                                @csrf

                                <div class="form-group row">
                                    <label for="school_id" class="col-lg-3 col-form-label font-weight-semibold">School <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        @if(auth()->user()->user_type == 'admin' && auth()->user()->school_id)
                                            <!-- Admin users can only see their assigned school -->
                                            <select required data-placeholder="Select School" class="form-control select" name="school_id" id="school_id" readonly disabled>
                                                @foreach($schools as $school)
                                                    @if($school->id == auth()->user()->school_id)
                                                        <option selected value="{{ $school->id }}">{{ $school->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <!-- Hidden input to ensure value is submitted -->
                                            <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">
                                        @else
                                            <!-- Super admin and team members can select any school -->
                                            <select required data-placeholder="Select School" class="form-control select" name="school_id" id="school_id">
                                                <option value="">Select School</option>
                                                @foreach($schools as $school)
                                                    <option {{ old('school_id') == $school->id ? 'selected' : '' }} value="{{ $school->id }}">{{ $school->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Class Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="name" value="{{ old('name') }}" required type="text" class="form-control" placeholder="Name of Class">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="class_type_id" class="col-lg-3 col-form-label font-weight-semibold">Class Type <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select Class Type" class="form-control select" name="class_type_id" id="class_type_id">
                                            <option value="">Select Class Type</option>
                                            @foreach($class_types as $ct)
                                                <option {{ old('class_type_id') == $ct->id ? 'selected' : '' }} value="{{ $ct->id }}">{{ $ct->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button id="ajax-btn" type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/class_filters.js') }}"></script>
<script>
var classesTable;

$(document).ready(function() {
    // Check if DataTable is already initialized
    if ($.fn.DataTable.isDataTable('#classes-table')) {
        classesTable = $('#classes-table').DataTable();
    } else if ($('#classes-table').length > 0) {
        // Initialize DataTables with export functionality
        classesTable = $('#classes-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            pageLength: 25,
            order: [[1, 'asc']], // Order by name
            columnDefs: [
                { orderable: false, targets: [4] } // Disable ordering on actions column
            ],
            destroy: true // Allow reinitialization if needed
        });
    }

    // Initialize select2 for all select elements
    if ($.fn.select2) {
        $('.select').select2({
            minimumResultsForSearch: Infinity
        });
    }

    // Apply filters
    $('#apply-filters').on('click', function() {
        applyClassFilters();
    });

    // Reset filters
    $('#reset-filters').on('click', function() {
        resetClassFilters();
    });

    // Auto-filter when dropdown changes
    $('#school-filter, #class-type-filter').on('change', function() {
        applyClassFilters();
    });
});

function applyClassFilters() {
    var schoolFilter = $('#school-filter').val();
    var classTypeFilter = $('#class-type-filter').val();

    if (classesTable && $.fn.DataTable.isDataTable('#classes-table')) {
        // Clear existing search
        classesTable.search('').draw();

        // Apply custom filtering
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'classes-table') {
                return true;
            }

            var $row = $(classesTable.row(dataIndex).node());
            var rowSchoolId = $row.data('school-id');
            var rowClassTypeId = $row.data('class-type-id');

            // School filter
            if (schoolFilter && rowSchoolId != schoolFilter) {
                return false;
            }

            // Class type filter
            if (classTypeFilter && rowClassTypeId != classTypeFilter) {
                return false;
            }

            return true;
        });

        classesTable.draw();
    } else {
        // Fallback for non-DataTable implementation
        $('.class-row').each(function() {
            var row = $(this);
            var showRow = true;

            // School filter
            if (schoolFilter && row.data('school-id') != schoolFilter) {
                showRow = false;
            }

            // Class type filter
            if (classTypeFilter && row.data('class-type-id') != classTypeFilter) {
                showRow = false;
            }

            if (showRow) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    showNotification('Filters applied successfully!', 'success');
}

function resetClassFilters() {
    $('#school-filter').val('').trigger('change');
    $('#class-type-filter').val('').trigger('change');

    // Clear custom search filters
    $.fn.dataTable.ext.search.pop();

    if (classesTable && $.fn.DataTable.isDataTable('#classes-table')) {
        classesTable.search('').draw();
    }

    // Show all rows
    $('.class-row').show();

    showNotification('Filters reset successfully!', 'info');
}

function showNotification(message, type) {
    // Use your existing notification system
    if (typeof flash_msg !== 'undefined') {
        flash_msg(message, type);
    } else if (typeof PNotify !== 'undefined') {
        new PNotify({
            title: 'Filter Status',
            text: message,
            type: type
        });
    } else {
        console.log(type.toUpperCase() + ': ' + message);
    }
}
</script>
@endsection
