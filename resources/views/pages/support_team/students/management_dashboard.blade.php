
@extends('layouts.master')
@section('page_title', 'Student Management Dashboard')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title font-weight-bold">
            <i class="icon-users mr-2"></i>
            Student Management Dashboard
            @if(!Qs::userIsSuperAdmin())
                <small class="text-muted"> - {{ Qs::getSetting('system_name') }}</small>
            @endif
        </h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <!-- Current Session Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4 class="mb-1">{{ Qs::getCurrentSession() }}</h4>
                        <p class="mb-0">Current Academic Session</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4 class="mb-1">{{ Qs::getNextSession() }}</h4>
                        <p class="mb-0">Next Academic Session</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Actions -->
        <div class="row">
            <!-- Student Promotion -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="icon-stairs-up text-primary mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Student Promotion</h5>
                        <p class="card-text">Promote students from current session to next session</p>
                        <a href="{{ route('students.promotion') }}" class="btn btn-primary">
                            <i class="icon-stairs-up mr-2"></i>Start Promotion
                        </a>
                    </div>
                </div>
            </div>

            <!-- Manage Promotions -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="icon-history text-warning mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Manage Promotions</h5>
                        <p class="card-text">Review and reset student promotions</p>
                        <a href="{{ route('students.promotion_manage') }}" class="btn btn-warning">
                            <i class="icon-history mr-2"></i>Manage
                        </a>
                    </div>
                </div>
            </div>

            <!-- Graduated Students -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="icon-graduation text-info mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Graduated Students</h5>
                        <p class="card-text">View and manage graduated students</p>
                        <a href="{{ route('students.graduated') }}" class="btn btn-info">
                            <i class="icon-graduation mr-2"></i>View Graduates
                        </a>
                    </div>
                </div>
            </div>

            <!-- Student Records -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="icon-users text-success mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Student Records</h5>
                        <p class="card-text">View and manage all student records</p>
                        <a href="{{ route('students.list', ['class_id' => '']) }}" class="btn btn-success">
                            <i class="icon-users mr-2"></i>View Students
                        </a>
                    </div>
                </div>
            </div>

            <!-- Add Students -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="icon-user-plus text-purple mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Add Students</h5>
                        <p class="card-text">Register new students to the system</p>
                        <a href="{{ route('students.create') }}" class="btn btn-purple">
                            <i class="icon-user-plus mr-2"></i>Add Student
                        </a>
                    </div>
                </div>
            </div>

            <!-- Student Analytics -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="icon-stats-bars text-teal mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Student Analytics</h5>
                        <p class="card-text">View student statistics and reports</p>
                        <a href="#" class="btn btn-teal" onclick="showStudentStats()">
                            <i class="icon-stats-bars mr-2"></i>View Stats
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workflow Guidelines -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title">
                    <i class="icon-info22 mr-2"></i>
                    Student Management Workflow
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Promotion Process:</h6>
                        <ol class="list-unstyled">
                            <li><i class="icon-arrow-right8 text-primary mr-2"></i>Select source class and section</li>
                            <li><i class="icon-arrow-right8 text-primary mr-2"></i>Select destination class and section</li>
                            <li><i class="icon-arrow-right8 text-primary mr-2"></i>Review students for promotion</li>
                            <li><i class="icon-arrow-right8 text-primary mr-2"></i>Choose action: Promote, Repeat, or Graduate</li>
                            <li><i class="icon-arrow-right8 text-primary mr-2"></i>Process promotions</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">Best Practices:</h6>
                        <ul class="list-unstyled">
                            <li><i class="icon-checkmark3 text-success mr-2"></i>Promote students within the same school</li>
                            <li><i class="icon-checkmark3 text-success mr-2"></i>Review academic performance before promotion</li>
                            <li><i class="icon-checkmark3 text-success mr-2"></i>Keep backup before bulk operations</li>
                            <li><i class="icon-checkmark3 text-success mr-2"></i>Verify graduation requirements</li>
                            <li><i class="icon-checkmark3 text-success mr-2"></i>Use manage promotions to undo if needed</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showStudentStats() {
    // This would show a modal with student statistics
    alert('Student analytics feature will be implemented in the next update.');
}
</script>

@endsection
