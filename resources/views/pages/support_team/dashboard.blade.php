
@extends('layouts.master')
@section('page_title', 'Dashboard')

@section('content')
<div class="content">
    <!-- Branch Selector (for Super Admin and School Admin) -->
    @if(isset($branches) && $branches->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Branch Selection</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.switch-branch') }}" class="form-inline">
                @csrf
                <div class="form-group mr-3">
                    <label for="branch_id" class="mr-2">Select Branch:</label>
                    <select name="branch_id" id="branch_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $current_branch == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }} ({{ $branch->school->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($selected_branch)
    <!-- Current Branch Info -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0">
                <i class="icon-office mr-2"></i>
                {{ $selected_branch->name }} - {{ $selected_branch->school->name }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Location:</strong> {{ $selected_branch->location }}
                </div>
                <div class="col-md-3">
                    <strong>Phone:</strong> {{ $selected_branch->phone }}
                </div>
                <div class="col-md-3">
                    <strong>Email:</strong> {{ $selected_branch->email }}
                </div>
                <div class="col-md-3">
                    <strong>Code:</strong> {{ $selected_branch->code }}
                </div>
            </div>
        </div>
    </div>

    <!-- Branch Statistics -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="icon-object border-success text-success mb-3">
                        <i class="icon-users4"></i>
                    </div>
                    <h3 class="text-success counter">{{ $students_count ?? 0 }}</h3>
                    <span class="text-muted">Students</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="icon-object border-info text-info mb-3">
                        <i class="icon-user-tie"></i>
                    </div>
                    <h3 class="text-info counter">{{ $staff_count ?? 0 }}</h3>
                    <span class="text-muted">Staff Members</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="icon-object border-warning text-warning mb-3">
                        <i class="icon-library2"></i>
                    </div>
                    <h3 class="text-warning counter">{{ $classes_count ?? 0 }}</h3>
                    <span class="text-muted">Classes</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="icon-object border-danger text-danger mb-3">
                        <i class="icon-cash3"></i>
                    </div>
                    <h3 class="text-danger counter">{{ $pending_payments ?? 0 }}</h3>
                    <span class="text-muted">Pending Payments</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Branch Specific Tabs -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Branch Management</h5>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item">
                    <a href="#students-tab" class="nav-link active" data-toggle="tab">
                        <i class="icon-users4 mr-2"></i>Students
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#staff-tab" class="nav-link" data-toggle="tab">
                        <i class="icon-user-tie mr-2"></i>Staff
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#academics-tab" class="nav-link" data-toggle="tab">
                        <i class="icon-book mr-2"></i>Academics
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#payments-tab" class="nav-link" data-toggle="tab">
                        <i class="icon-cash3 mr-2"></i>Payments
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Students Tab -->
                <div class="tab-pane fade show active" id="students-tab">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Recent Students</h6>
                                </div>
                                <div class="card-body">
                                    @if(isset($recent_students) && $recent_students->count() > 0)
                                        @foreach($recent_students as $student)
                                        <div class="media mb-3">
                                            <img src="{{ $student->user->photo }}" alt="student" class="rounded-circle mr-3" width="40">
                                            <div class="media-body">
                                                <h6 class="media-title mb-0">{{ $student->user->name }}</h6>
                                                <span class="text-muted">{{ $student->adm_no }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No students found for this branch.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('students.create') }}" class="btn btn-primary btn-block mb-2">
                                        <i class="icon-plus mr-2"></i>Add New Student
                                    </a>
                                    <a href="{{ route('students.list', $selected_branch->id) }}" class="btn btn-info btn-block mb-2">
                                        <i class="icon-list mr-2"></i>View All Students
                                    </a>
                                    <a href="{{ route('students.promotion') }}" class="btn btn-warning btn-block">
                                        <i class="icon-arrow-up8 mr-2"></i>Student Promotion
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Staff Tab -->
                <div class="tab-pane fade" id="staff-tab">
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Staff Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="{{ route('users.index') }}" class="btn btn-primary btn-block mb-2">
                                                <i class="icon-users mr-2"></i>All Staff
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('users.create') }}" class="btn btn-success btn-block mb-2">
                                                <i class="icon-plus mr-2"></i>Add Staff
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('users.index', ['type' => 'teacher']) }}" class="btn btn-info btn-block mb-2">
                                                <i class="icon-user-tie mr-2"></i>Teachers
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('users.index', ['type' => 'librarian']) }}" class="btn btn-warning btn-block mb-2">
                                                <i class="icon-book mr-2"></i>Librarians
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academics Tab -->
                <div class="tab-pane fade" id="academics-tab">
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Classes & Subjects</h6>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('classes.index') }}" class="btn btn-primary btn-block mb-2">
                                        <i class="icon-library2 mr-2"></i>Manage Classes
                                    </a>
                                    <a href="{{ route('subjects.index') }}" class="btn btn-info btn-block mb-2">
                                        <i class="icon-book mr-2"></i>Manage Subjects
                                    </a>
                                    <a href="{{ route('sections.index') }}" class="btn btn-warning btn-block">
                                        <i class="icon-grid mr-2"></i>Manage Sections
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Exams & Marks</h6>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('exams.index') }}" class="btn btn-primary btn-block mb-2">
                                        <i class="icon-clipboard mr-2"></i>Manage Exams
                                    </a>
                                    <a href="{{ route('marks.index') }}" class="btn btn-info btn-block mb-2">
                                        <i class="icon-calculator mr-2"></i>Manage Marks
                                    </a>
                                    <a href="{{ route('marks.tabulation') }}" class="btn btn-success btn-block">
                                        <i class="icon-table2 mr-2"></i>Tabulation Sheet
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Timetable</h6>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('timetables.index') }}" class="btn btn-primary btn-block mb-2">
                                        <i class="icon-calendar mr-2"></i>Manage Timetables
                                    </a>
                                    <a href="{{ route('timetables.create') }}" class="btn btn-success btn-block">
                                        <i class="icon-plus mr-2"></i>Create Timetable
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Tab -->
                <div class="tab-pane fade" id="payments-tab">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Payment Management</h6>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('payments.index') }}" class="btn btn-primary btn-block mb-2">
                                        <i class="icon-cash3 mr-2"></i>All Payments
                                    </a>
                                    <a href="{{ route('payments.create') }}" class="btn btn-success btn-block mb-2">
                                        <i class="icon-plus mr-2"></i>Create Payment
                                    </a>
                                    <a href="{{ route('payments.manage') }}" class="btn btn-info btn-block">
                                        <i class="icon-cog mr-2"></i>Manage Payments
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Library</h6>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('books.index') }}" class="btn btn-primary btn-block mb-2">
                                        <i class="icon-books mr-2"></i>Manage Books
                                    </a>
                                    <a href="{{ route('book_requests.index') }}" class="btn btn-info btn-block">
                                        <i class="icon-list mr-2"></i>Book Requests
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        <h4>Welcome to the School Management System</h4>
        <p>Please select a branch to view dashboard data, or contact your administrator if you don't have access to any branch.</p>
    </div>
    @endif
</div>
@endsection

@section('page_script')
<script>
    // Counter animation
    $('.counter').each(function() {
        var $this = $(this),
            countTo = $this.attr('data-count') || $this.text();
        
        $({ countNum: 0 }).animate({
            countNum: countTo
        }, {
            duration: 1000,
            easing: 'linear',
            step: function() {
                $this.text(Math.floor(this.countNum));
            },
            complete: function() {
                $this.text(this.countNum);
            }
        });
    });
</script>
@endsection
