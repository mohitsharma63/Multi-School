@extends('layouts.master')
@section('page_title', 'Student Promotion')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title font-weight-bold">
            <i class="icon-stairs-up mr-2"></i>
            Student Promotion From <span class="text-danger">{{ $old_year }}</span> TO <span class="text-success">{{ $new_year }}</span> Session
        </h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        @if(Qs::userIsSuperAdmin() && $schools->count() > 1)
        <div class="alert alert-info">
            <i class="icon-info22 mr-2"></i>
            <strong>Multi-School System:</strong> You can promote students within the same school. Cross-school promotions are not allowed.
        </div>
        @endif

        @include('pages.support_team.students.promotion.selector')
    </div>
</div>

@if($selected)
<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title font-weight-bold">
            <i class="icon-users mr-2"></i>
            Promote Students From
            <span class="badge badge-primary">{{ $from_class->name }} - {{ $from_section->name }}</span>
            TO
            <span class="badge badge-success">{{ $to_class->name }} - {{ $to_section->name }}</span>
            @if(Qs::userIsSuperAdmin())
                <small class="text-muted">({{ $from_class->school->name ?? 'N/A' }})</small>
            @endif
        </h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <!-- Student Count Info -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $students->count() }}</h3>
                        <p class="mb-0">Students Available for Promotion</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $old_year }}</h3>
                        <p class="mb-0">Current Session</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $new_year }}</h3>
                        <p class="mb-0">Target Session</p>
                    </div>
                </div>
            </div>
        </div>

        @include('pages.support_team.students.promotion.promote')
    </div>
</div>

<!-- Quick Actions Card -->
<div class="card">
    <div class="card-header">
        <h6 class="card-title">
            <i class="icon-cog mr-2"></i>
            Quick Actions
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <a href="{{ route('students.promotion_manage') }}" class="btn btn-outline-primary btn-block">
                    <i class="icon-history mr-2"></i>
                    Manage Previous Promotions
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('students.graduated') }}" class="btn btn-outline-info btn-block">
                    <i class="icon-graduation mr-2"></i>
                    View Graduated Students
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{--Student Promotion End--}}

@endsection
