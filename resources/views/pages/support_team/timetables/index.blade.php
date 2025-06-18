@extends('layouts.master')
@section('page_title', 'Manage TimeTables')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage TimeTables</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <!-- Advanced Filter Section -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Advanced Filters</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Filter by School:</label>
                                    <select id="school-filter" class="form-control select">
                                        <option value="">All Schools</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Filter by Class:</label>
                                    <select id="class-filter" class="form-control select">
                                        <option value="">All Classes</option>
                                        @foreach($my_classes as $mc)
                                            <option value="{{ $mc->id }}">{{ $mc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Filter by Type:</label>
                                    <select id="type-filter" class="form-control select">
                                        <option value="">All Types</option>
                                        <option value="class">Class Timetable</option>
                                        <option value="exam">Exam Timetable</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Filter by Year:</label>
                                    <select id="year-filter" class="form-control select">
                                        <option value="">All Years</option>
                                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                        <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>
                                        <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <button type="button" id="apply-filters" class="btn btn-primary">Apply Filters</button>
                                    <button type="button" id="reset-filters" class="btn btn-secondary">Reset Filters</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs nav-tabs-highlight">
                @if(Qs::userIsTeamSA())
                <li class="nav-item"><a href="#add-tt" class="nav-link active" data-toggle="tab">Create Timetable</a></li>
                @endif
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Show TimeTables</a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-scrollable">
                        <div class="dropdown-header">Select Class</div>
                        <div class="dropdown-divider"></div>
                        @foreach($my_classes->groupBy('school_id') as $schoolId => $classes)
                            @if($classes->first()->school)
                                <div class="dropdown-header text-primary">{{ $classes->first()->school->name ?? 'Unknown School' }}</div>
                            @endif
                            @foreach($classes as $mc)
                                <a href="#ttr{{ $mc->id }}" class="dropdown-item class-tab" data-class-id="{{ $mc->id }}" data-school-id="{{ $mc->school_id ?? '' }}" data-toggle="tab">
                                    <i class="icon-book mr-2"></i>{{ $mc->name }}
                                </a>
                            @endforeach
                            @if(!$loop->last)
                                <div class="dropdown-divider"></div>
                            @endif
                        @endforeach
                    </div>
                </li>
            </ul>


            <div class="tab-content">

                @if(Qs::userIsTeamSA())
                <div class="tab-pane fade show active" id="add-tt">
                   <div class="col-md-10">
                       <form class="ajax-store" method="post" action="{{ route('ttr.store') }}">
                           @csrf
                           <div class="form-group row">
                               <label class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input name="name" value="{{ old('name') }}" required type="text" class="form-control" placeholder="Name of TimeTable">
                               </div>
                           </div>

                           <div class="form-group row">
                               <label for="school_id" class="col-lg-3 col-form-label font-weight-semibold">School <span class="text-danger">*</span></label>
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
                               <label for="my_class_id" class="col-lg-3 col-form-label font-weight-semibold">Class <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <select required data-placeholder="Select Class" class="form-control select" name="my_class_id" id="my_class_id">
                                       <option value="">Select School First</option>
                                   </select>
                               </div>
                           </div>

                           <div class="form-group row">
                               <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Type (Class or Exam)</label>
                               <div class="col-lg-9">
                                   <select class="select form-control" name="exam_id" id="exam_id">
                                       <option value="">Class Timetable</option>
                                       @foreach($exams as $ex)
                                           <option {{ old('exam_id') == $ex->id ? 'selected' : '' }} value="{{ $ex->id }}">{{ $ex->name }}</option>
                                       @endforeach
                                   </select>
                               </div>
                           </div>

                           <div class="form-group row">
                               <label class="col-lg-3 col-form-label font-weight-semibold">Description</label>
                               <div class="col-lg-9">
                                   <textarea name="description" class="form-control" rows="3" placeholder="Optional description for the timetable">{{ old('description') }}</textarea>
                               </div>
                           </div>

                           <div class="text-right">
                               <button id="ajax-btn" type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                           </div>
                       </form>
                   </div>

                </div>
                @endif

                @foreach($my_classes as $mc)
                    <div class="tab-pane fade class-tab-content" id="ttr{{ $mc->id }}" data-class-id="{{ $mc->id }}" data-school-id="{{ $mc->school_id ?? '' }}">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">
                                    <i class="icon-book mr-2"></i>{{ $mc->name }} Timetables
                                    @if($mc->school)
                                        <small class="text-muted ml-2">{{ $mc->school->name }}</small>
                                    @endif
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table datatable-button-html5-columns timetable-table">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>School</th>
                                        <th>Type</th>
                                        <th>Year</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($tt_records->where('my_class_id', $mc->id) as $ttr)
                                        <tr class="timetable-row"
                                            data-class-id="{{ $ttr->my_class_id }}"
                                            data-school-id="{{ $ttr->school_id ?? '' }}"
                                            data-type="{{ $ttr->exam_id ? 'exam' : 'class' }}"
                                            data-year="{{ $ttr->year }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $ttr->name }}</strong>
                                                @if($ttr->description)
                                                    <br><small class="text-muted">{{ Str::limit($ttr->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $ttr->my_class->name }}</span>
                                            </td>
                                            <td>
                                                @if($ttr->school)
                                                    <span class="badge badge-primary">{{ $ttr->school->name }}</span>
                                                @else
                                                    <span class="badge badge-secondary">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ttr->exam_id)
                                                    <span class="badge badge-warning">{{ $ttr->exam->name }}</span>
                                                @else
                                                    <span class="badge badge-success">Class TimeTable</span>
                                                @endif
                                            </td>
                                            <td>{{ $ttr->year }}</td>
                                            <td>
                                                <span class="badge badge-{{ $ttr->is_active ? 'success' : 'danger' }}">
                                                    {{ $ttr->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $ttr->created_at->format('M d, Y') }}</td>
                                            <td class="text-center">
                                                <div class="list-icons">
                                                    <div class="dropdown">
                                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                            <i class="icon-menu9"></i>
                                                        </a>

                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            {{--View--}}
                                                            <a href="{{ route('ttr.show', $ttr->id) }}" class="dropdown-item">
                                                                <i class="icon-eye"></i> View
                                                            </a>
                                                            {{--Print--}}
                                                            <a href="{{ route('ttr.print', $ttr->id) }}" target="_blank" class="dropdown-item">
                                                                <i class="icon-printer"></i> Print
                                                            </a>

                                                            @if(Qs::userIsTeamSA())
                                                            {{--Manage--}}
                                                            <a href="{{ route('ttr.manage', $ttr->id) }}" class="dropdown-item">
                                                                <i class="icon-plus-circle2"></i> Manage
                                                            </a>
                                                            {{--Edit--}}
                                                            <a href="{{ route('ttr.edit', $ttr->id) }}" class="dropdown-item">
                                                                <i class="icon-pencil"></i> Edit
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            @endif

                                                            {{--Delete--}}
                                                            @if(Qs::userIsSuperAdmin())
                                                                <a id="{{ $ttr->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item text-danger">
                                                                    <i class="icon-trash"></i> Delete
                                                                </a>
                                                                <form method="post" id="item-delete-{{ $ttr->id }}" action="{{ route('ttr.destroy', $ttr->id) }}" class="hidden">
                                                                    @csrf
                                                                    @method('delete')
                                                                </form>
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
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    {{--TimeTable Ends--}}

@endsection

@section('scripts')
<script src="{{ asset('assets/js/timetable_filters.js') }}"></script>
<script>
$(document).ready(function() {
    // Add custom styling for dropdown
    $('.dropdown-menu-scrollable').css({
        'max-height': '400px',
        'overflow-y': 'auto'
    });

    // Handle tab changes to prevent DataTable conflicts
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Adjust DataTable columns when tab is shown
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});
</script>
@endsection
