@extends('layouts.master')
@section('page_title', 'Student Promotion')
@section('content')

    <!-- Advanced Promotion Filters -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h6 class="card-title">Advanced Promotion Filters</h6>
            <div class="header-elements">
                <button type="button" class="btn btn-light btn-sm" data-toggle="collapse" data-target="#promotionFilters">
                    <i class="icon-filter4"></i> Toggle Filters
                </button>
            </div>
        </div>
        <div class="card-body collapse" id="promotionFilters">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Filter by Current Class:</label>
                        <select class="form-control select" id="promotion_filter_from_class">
                            <option value="">All Classes</option>
                            @foreach($my_classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Filter by Target Class:</label>
                        <select class="form-control select" id="promotion_filter_to_class">
                            <option value="">All Classes</option>
                            @foreach($my_classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Promotion Status:</label>
                        <select class="form-control select" id="promotion_filter_status">
                            <option value="">All Students</option>
                            <option value="eligible">Promotion Eligible</option>
                            <option value="repeat">Need to Repeat</option>
                            <option value="graduate">Ready to Graduate</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Academic Performance:</label>
                        <select class="form-control select" id="promotion_filter_performance">
                            <option value="">All Performance</option>
                            <option value="excellent">Excellent (90%+)</option>
                            <option value="good">Good (70-89%)</option>
                            <option value="average">Average (50-69%)</option>
                            <option value="below">Below Average (<50%)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Filter by School:</label>
                        <select class="form-control select" id="promotion_filter_school">
                            <option value="">All Schools</option>
                            @if(Qs::userIsSuperAdmin())
                                @foreach($schools ?? [] as $school)
                                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                                @endforeach
                            @else
                                <option value="{{ Qs::getSetting('current_school_id') }}" selected>{{ Qs::getSetting('system_name') }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Bulk Actions:</label>
                        <div class="btn-group d-block">
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="select_all_students">
                            <label class="form-check-label" for="select_all_students">
                                Select All Visible Students
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="button" class="btn btn-primary" onclick="applyPromotionFilters()">
                    <i class="icon-filter4"></i> Apply Filters
                </button>
                <button type="button" class="btn btn-secondary" onclick="clearPromotionFilters()">
                    <i class="icon-reload-alt"></i> Clear Filters
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title font-weight-bold">Student Promotion From <span class="text-danger">{{ $old_year }}</span> TO <span class="text-success">{{ $new_year }}</span> Session</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @include('pages.support_team.students.promotion.selector')
        </div>
    </div>

    @if($selected)
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title font-weight-bold">Promote Students From <span class="text-teal">{{ $my_classes->where('id', $fc)->first()->name.' '.$sections->where('id', $fs)->first()->name }}</span> TO <span class="text-purple">{{ $my_classes->where('id', $tc)->first()->name.' '.$sections->where('id', $ts)->first()->name }}</span> </h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @include('pages.support_team.students.promotion.promote')
        </div>
    </div>
    @endif


    {{--Student Promotion End--}}

@endsection
