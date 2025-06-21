@extends('layouts.master')
@section('page_title', 'Graduated Students')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">
            <i class="icon-graduation mr-2"></i>
            Graduated Students
            @if(!Qs::userIsSuperAdmin())
                <small class="text-muted"> - {{ Qs::getSetting('system_name') }}</small>
            @endif
        </h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <!-- Filter Panel -->
        @if(Qs::userIsSuperAdmin() && $schools->count() > 0)
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="graduated_school_filter" class="col-form-label font-weight-bold">Filter by School:</label>
                    <select id="graduated_school_filter" class="form-control select">
                        <option value="">All Schools</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="graduated_class_filter" class="col-form-label font-weight-bold">Filter by Class:</label>
                    <select id="graduated_class_filter" class="form-control select">
                        <option value="">All Classes</option>
                        @foreach($my_classes as $class)
                            <option value="{{ $class->id }}" data-school-id="{{ $class->school_id }}">
                                {{ $class->name }}
                                @if(Qs::userIsSuperAdmin())
                                    <small>({{ $class->school->name ?? 'N/A' }})</small>
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="graduated_year_filter" class="col-form-label font-weight-bold">Filter by Year:</label>
                    <select id="graduated_year_filter" class="form-control select">
                        <option value="">All Years</option>
                        @php
                            $years = $students->pluck('grad_date')->unique()->sort()->reverse();
                        @endphp
                        @foreach($years as $year)
                            @if($year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @endif

        <!-- Statistics Panel -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4 id="total-graduated">{{ $students->count() }}</h4>
                        <p class="mb-0">Total Graduated</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4 id="current-year-graduated">
                            {{ $students->where('grad_date', Qs::getCurrentSession())->count() }}
                        </h4>
                        <p class="mb-0">This Session</p>
                    </div>
                </div>
            </div>
            @if(Qs::userIsSuperAdmin())
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4 id="schools-count">{{ $schools->count() }}</h4>
                        <p class="mb-0">Schools</p>
                    </div>
                </div>
            </div>
            @endif
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h4 id="classes-count">{{ $my_classes->count() }}</h4>
                        <p class="mb-0">Classes</p>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#all-students" class="nav-link active" data-toggle="tab">All Graduated Students</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="all-students">
                <div class="table-responsive">
                    <table class="table table-striped" id="graduated-students-table">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>ADM_NO</th>
                            @if(Qs::userIsSuperAdmin())
                            <th>School</th>
                            @endif
                            <th>Class & Section</th>
                            <th>Graduation Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($students as $s)
                            <tr data-school-id="{{ $s->my_class->school_id ?? '' }}"
                                data-class-id="{{ $s->my_class_id }}"
                                data-grad-year="{{ $s->grad_date }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                                <td>
                                    <strong>{{ $s->user->name }}</strong>
                                    <br><small class="text-muted">{{ $s->user->email }}</small>
                                </td>
                                <td>{{ $s->adm_no ?? 'N/A' }}</td>
                                @if(Qs::userIsSuperAdmin())
                                <td>
                                    <span class="badge badge-secondary">{{ $s->my_class->school->name ?? 'N/A' }}</span>
                                </td>
                                @endif
                                <td>
                                    <strong>{{ $s->my_class->name ?? 'N/A' }}</strong>
                                    <br><small>{{ $s->section->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $s->grad_date ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
                                                <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="dropdown-item">
                                                    <i class="icon-eye"></i> View Profile
                                                </a>
                                                @if(Qs::userIsTeamSA())
                                                    <a href="{{ route('students.edit', Qs::hash($s->id)) }}" class="dropdown-item">
                                                        <i class="icon-pencil"></i> Edit
                                                    </a>
                                                    <a href="{{ route('students.destroy', Qs::hash($s->id)) }}" onclick="confirmDelete(this)" class="dropdown-item">
                                                        <i class="icon-trash"></i> Delete
                                                    </a>
                                                    <form method="post" id="item-delete-{{ Qs::hash($s->id) }}" action="{{ route('students.destroy', Qs::hash($s->id)) }}" class="hidden">@csrf @method('delete')</form>
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

                @if($students->count() == 0)
                <div class="text-center py-4">
                    <div class="content-group">
                        <i class="icon-graduation text-muted mb-3" style="font-size: 4rem;"></i>
                        <h6 class="text-muted">No graduated students found</h6>
                        <p class="text-muted">Students who complete their education will appear here.</p>
                        <a href="{{ route('students.promotion') }}" class="btn btn-primary">
                            <i class="icon-stairs-up mr-2"></i> Start Student Promotion
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // School filter functionality
    $('#graduated_school_filter').on('change', function() {
        filterGraduatedStudents();
    });

    // Class filter functionality
    $('#graduated_class_filter').on('change', function() {
        filterGraduatedStudents();
    });

    // Year filter functionality
    $('#graduated_year_filter').on('change', function() {
        filterGraduatedStudents();
    });

    function filterGraduatedStudents() {
        var schoolId = $('#graduated_school_filter').val();
        var classId = $('#graduated_class_filter').val();
        var gradYear = $('#graduated_year_filter').val();

        $('#graduated-students-table tbody tr').each(function() {
            var row = $(this);
            var rowSchoolId = row.data('school-id');
            var rowClassId = row.data('class-id');
            var rowGradYear = row.data('grad-year');

            var showRow = true;

            // School filter
            if (schoolId && rowSchoolId != schoolId) {
                showRow = false;
            }

            // Class filter
            if (classId && rowClassId != classId) {
                showRow = false;
            }

            // Year filter
            if (gradYear && rowGradYear != gradYear) {
                showRow = false;
            }

            if (showRow) {
                row.show();
            } else {
                row.hide();
            }
        });

        updateGraduatedCounts();
    }

    function updateGraduatedCounts() {
        var visibleRows = $('#graduated-students-table tbody tr:visible');
        var total = visibleRows.length;

        $('#total-graduated').text(total);
    }

    // Filter classes by school
    $('#graduated_school_filter').on('change', function() {
        var schoolId = $(this).val();

        $('#graduated_class_filter option').each(function() {
            var optionSchoolId = $(this).data('school-id');
            if (!schoolId || !optionSchoolId || optionSchoolId == schoolId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        $('#graduated_class_filter').val('');
    });
});
</script>

{{--Student List Ends--}}

@endsection
