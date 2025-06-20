@extends('layouts.master')
@section('page_title', 'Tabulation Sheet')
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-books mr-2"></i> Tabulation Sheet</h5>
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
                                    <label>Filter by Exam:</label>
                                    <select id="exam-filter" class="form-control select">
                                        <option value="">All Exams</option>
                                        @foreach($exams as $ex)
                                            <option value="{{ $ex->id }}">{{ $ex->name }} - {{ $ex->year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mt-4">
                                        <button type="button" class="btn btn-primary" onclick="applyTabulationFilters()">
                                            <i class="icon-filter4 mr-2"></i>Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-light ml-2" onclick="resetTabulationFilters()">
                                            <i class="icon-reload-alt mr-2"></i>Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form method="post" action="{{ route('marks.tabulation_select') }}">
                @csrf
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="exam_id" class="col-form-label font-weight-bold">Exam:</label>
                            <select required id="exam_id" name="exam_id" class="form-control select" data-placeholder="Select Exam">
                                @foreach($exams as $exm)
                                    <option {{ ($selected && $exam_id == $exm->id) ? 'selected' : '' }} value="{{ $exm->id }}" data-school-id="{{ $exm->school_id ?? '' }}">{{ $exm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="my_class_id" class="col-form-label font-weight-bold">Class:</label>
                            <select onchange="getClassSections(this.value)" required id="my_class_id" name="my_class_id" class="form-control select" data-placeholder="Select Class">
                                <option value=""></option>
                                @foreach($my_classes as $c)
                                    <option {{ ($selected && $my_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}" data-school-id="{{ $c->school_id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="section_id" class="col-form-label font-weight-bold">Section:</label>
                            <select required id="section_id" name="section_id" data-placeholder="Select Class First" class="form-control select">
                                @if($selected)
                                    @foreach($sections->where('my_class_id', $my_class_id) as $s)
                                        <option {{ $section_id == $s->id ? 'selected' : '' }} value="{{ $s->id }}" data-class-id="{{ $s->my_class_id }}">{{ $s->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>


                    <div class="col-md-2 mt-4">
                        <div class="text-right mt-1">
                            <button type="submit" class="btn btn-primary">View Sheet <i class="icon-paperplane ml-2"></i></button>
                        </div>
                    </div>

                </div>

            </form>
        </div>
    </div>

    {{--if Selction Has Been Made --}}

    @if($selected)
        <div class="card">
            <div class="card-header">
                <h6 class="card-title font-weight-bold">Tabulation Sheet for {{ $my_class->name.' '.$section->name.' - '.$ex->name.' ('.$year.')' }}</h6>
            </div>
            <div class="card-body">
                <table class="table table-responsive table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>NAMES_OF_STUDENTS_IN_CLASS</th>
                       @foreach($subjects as $sub)
                       <th title="{{ $sub->name }}" rowspan="2">{{ strtoupper($sub->slug ?: $sub->name) }}</th>
                       @endforeach
                        {{--@if($ex->term == 3)
                        <th>1ST TERM TOTAL</th>
                        <th>2ND TERM TOTAL</th>
                        <th>3RD TERM TOTAL</th>
                        <th style="color: darkred">CUM Total</th>
                        <th style="color: darkblue">CUM Average</th>
                        @endif--}}
                        <th style="color: darkred">Total</th>
                        <th style="color: darkblue">Average</th>
                        <th style="color: darkgreen">Position</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="text-align: center">{{ $s->user->name }}</td>
                            @foreach($subjects as $sub)
                            <td>{{ $marks->where('student_id', $s->user_id)->where('subject_id', $sub->id)->first()->$tex ?? '-' ?: '-' }}</td>
                            @endforeach

                            {{--@if($ex->term == 3)
                                --}}{{--1st term Total--}}{{--
                            <td>{{ Mk::getTermTotal($s->user_id, 1, $year) ?? '-' }}</td>
                            --}}{{--2nd Term Total--}}{{--
                            <td>{{ Mk::getTermTotal($s->user_id, 2, $year) ?? '-' }}</td>
                            --}}{{--3rd Term total--}}{{--
                            <td>{{ Mk::getTermTotal($s->user_id, 3, $year) ?? '-' }}</td>
                            @endif--}}

                            <td style="color: darkred">{{ $exr->where('student_id', $s->user_id)->first()->total ?: '-' }}</td>
                            <td style="color: darkblue">{{ $exr->where('student_id', $s->user_id)->first()->ave ?: '-' }}</td>
                            <td style="color: darkgreen">{!! Mk::getSuffix($exr->where('student_id', $s->user_id)->first()->pos) ?: '-' !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--Print Button--}}
                <div class="text-center mt-4">
                    <a target="_blank" href="{{  route('marks.print_tabulation', [$exam_id, $my_class_id, $section_id]) }}" class="btn btn-danger btn-lg"><i class="icon-printer mr-2"></i> Print Tabulation Sheet</a>
                </div>
            </div>
        </div>
    @endif
@endsection
