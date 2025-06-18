
@extends('layouts.master')
@section('page_title', 'Manage TimeTables')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Branch Filter</h6>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            @include('partials.school_branch_selector')
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage TimeTables</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-ttr" class="nav-link active" data-toggle="tab">TimeTable Records</a></li>
                <li class="nav-item"><a href="#new-ttr" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Create TimeTable Record</a></li>
            </ul>

            <div class="tab-content">
                {{-- All TimeTable Records --}}
                <div class="tab-pane fade show active" id="all-ttr">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Exam</th>
                            <th>Year</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tt_records as $ttr)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ttr->name }}</td>
                                <td>{{ $ttr->my_class->name }}</td>
                                <td>{{ $ttr->exam->name ?? 'Class TimeTable' }}</td>
                                <td>{{ $ttr->year }}</td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
                                                <a href="{{ route('ttr.show', $ttr->id) }}" class="dropdown-item"><i class="icon-eye"></i> View</a>
                                                <a href="{{ route('ttr.manage', $ttr->id) }}" class="dropdown-item"><i class="icon-plus22"></i> Manage</a>
                                                <a href="{{ route('ttr.edit', $ttr->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                <a href="{{ route('ttr.print', $ttr->id) }}" target="_blank" class="dropdown-item"><i class="icon-printer"></i> Print</a>
                                                {{--Delete--}}
                                                <a id="{{ $ttr->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                <form method="post" id="item-delete-{{ $ttr->id }}" action="{{ route('ttr.destroy', $ttr->id) }}" class="hidden">@csrf @method('delete')</form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Add TimeTable Record --}}
                <div class="tab-pane fade" id="new-ttr">
                    <div class="row">
                        <div class="col-md-8">
                            <form class="ajax-store" data-reload="#page-header" method="post" action="{{ route('ttr.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="name" value="{{ old('name') }}" required type="text" class="form-control" placeholder="Name of TimeTable">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="my_class_id" class="col-lg-3 col-form-label font-weight-semibold">Select Class <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select Class" class="form-control select" name="my_class_id" id="my_class_id">
                                            @foreach($my_classes as $c)
                                                <option {{ old('my_class_id') == $c->id ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Exam (Optional)</label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="Select Exam" class="form-control select" name="exam_id" id="exam_id">
                                            <option value="">Select Exam</option>
                                            @foreach($exams as $ex)
                                                <option {{ old('exam_id') == $ex->id ? 'selected' : '' }} value="{{ $ex->id }}">{{ $ex->name }}</option>
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

    {{--TimeTable Records Ends--}}

@endsection
