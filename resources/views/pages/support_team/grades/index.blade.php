@extends('layouts.master')
@section('page_title', 'Manage Grades')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Grades</h6>
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
                                    <select id="school-filter" class="form-control select">
                                        <option value="">All Schools</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Filter by Grade Type:</label>
                                    <select id="grade-type-filter" class="form-control select">
                                        <option value="">All Grade Types</option>
                                        @foreach($class_types as $ct)
                                            <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                                        @endforeach
                                        <option value="not_applicable">Not Applicable</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-end">
                                        <button type="button" id="apply-filters" class="btn btn-primary mr-2">
                                            <i class="icon-filter4 mr-1"></i> Apply Filters
                                        </button>
                                        <button type="button" id="reset-filters" class="btn btn-light">
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
                <li class="nav-item"><a href="#all-grades" class="nav-link active" data-toggle="tab">Manage Grades</a></li>
                <li class="nav-item"><a href="#new-grade" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Add Grade</a></li>
            </ul>

            <div class="tab-content">
                    <div class="tab-pane fade show active" id="all-grades">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>School</th>
                                <th>Grade Type</th>
                                <th>Range</th>
                                <th>Remark</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($grades as $gr)
                                <tr class="grade-row"
                                    data-school-id="{{ $gr->school_id ?? '' }}"
                                    data-grade-type-id="{{ $gr->class_type_id ?? 'not_applicable' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $gr->name }}</td>
                                    <td>{{ $gr->school_id ? $schools->where('id', $gr->school_id)->first()->name ?? 'N/A' : 'All Schools' }}</td>
                                    <td>{{ $gr->class_type_id ? $class_types->where('id', $gr->class_type_id)->first()->name : 'Not Applicable'}}</td>
                                    <td>{{ $gr->mark_from.' - '.$gr->mark_to }}</td>
                                    <td>{{ $gr->remark }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    @if(Qs::userIsTeamSA())
                                                    {{--Edit--}}
                                                    <a href="{{ route('grades.edit', $gr->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                   @endif
                                                    @if(Qs::userIsSuperAdmin())
                                                    {{--Delete--}}
                                                    <a id="{{ $gr->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                    <form method="post" id="item-delete-{{ $gr->id }}" action="{{ route('grades.destroy', $gr->id) }}" class="hidden">@csrf @method('delete')</form>
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

                <div class="tab-pane fade" id="new-grade">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span>If The grade you are creating applies to all class types select <strong>NOT APPLICABLE.</strong> Otherwise select the Class Type That the grade applies to</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <form method="post" action="{{ route('grades.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="name" value="{{ old('name') }}" required type="text" class="form-control text-uppercase" placeholder="E.g. A1">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="school_id" class="col-lg-3 col-form-label font-weight-semibold">School</label>
                                    <div class="col-lg-9">
                                        <select class="form-control select" name="school_id" id="school_id">
                                            <option value="">All Schools</option>
                                            @foreach($schools as $school)
                                                <option {{ old('school_id') == $school->id ? 'selected' : '' }} value="{{ $school->id }}">{{ $school->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="class_type_id" class="col-lg-3 col-form-label font-weight-semibold">Grade Type</label>
                                    <div class="col-lg-9">
                                        <select class="form-control select" name="class_type_id" id="class_type_id">
                                            <option value="">Not Applicable</option>
                                         @foreach($class_types as $ct)
                                                <option {{ old('class_type_id') == $ct->id ? 'selected' : '' }} value="{{ $ct->id }}">{{ $ct->name }}</option>
                                             @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Mark From <span class="text-danger">*</span></label>
                                    <div class="col-lg-3">
                                        <input min="0" max="100" name="mark_from" value="{{ old('mark_from') }}" required type="number" class="form-control" placeholder="0">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Mark To <span class="text-danger">*</span></label>
                                    <div class="col-lg-3">
                                        <input min="0" max="100" name="mark_to" value="{{ old('mark_to') }}" required type="number" class="form-control" placeholder="0">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="remark" class="col-lg-3 col-form-label font-weight-semibold">Remark</label>
                                    <div class="col-lg-9">
                                        <select class="form-control select" name="remark" id="remark">
                                            <option value="">Select Remark...</option>
                                            @foreach(Mk::getRemarks() as $rem)
                                                <option {{ old('remark') == $rem ? 'selected' : '' }} value="{{ $rem }}">{{ $rem }}</option>
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
            </div>
        </div>
    </div>

    {{--Grade List Ends--}}

<script src="{{ asset('assets/js/grade_filters.js') }}"></script>

@endsection
