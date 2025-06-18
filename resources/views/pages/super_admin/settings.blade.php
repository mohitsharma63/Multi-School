@extends('layouts.master')
@section('page_title', 'Manage System Settings')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">School & Branch Filter</h6>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            @include('partials.school_branch_selector')
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title font-weight-semibold">
                @if(request('school_id'))
                    <i class="icon-cog mr-2"></i>Update School Settings
                @else
                    <i class="icon-plus22 mr-2"></i>Create New School
                @endif
            </h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @if(!request('school_id'))
                <div class="alert alert-primary border-0">
                    <div class="d-flex align-items-center">
                        <div class="alert-icon mr-3">
                            <i class="icon-school text-primary-600" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="alert-heading mb-1">Create New School</h6>
                            <p class="mb-0">Fill in the form below to create a new school and configure its initial settings.</p>
                        </div>
                    </div>
                </div>
            @endif

            <form enctype="multipart/form-data" method="post" action="{{ route('settings.update') }}{{ request('school_id') ? '?school_id='.request('school_id') : '' }}{{ request('branch_id') ? '&branch_id='.request('branch_id') : '' }}">
                @csrf @method('PUT')
                @if(request('school_id'))
                    <input type="hidden" name="school_id" value="{{ request('school_id') }}">
                @endif
                @if(request('branch_id'))
                    <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                @endif

                <div class="row">
                    <!-- School Information Section -->
                    <div class="col-md-8">
                        <div class="card border-left-3 border-left-primary">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="icon-info22 mr-2"></i>School Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label font-weight-semibold">School Name <span class="text-danger">*</span></label>
                                            <input name="system_name" value="{{ $s['system_name'] }}" required type="text" class="form-control form-control-lg" placeholder="Enter school name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label font-weight-semibold">School Acronym</label>
                                            <input name="system_title" value="{{ $s['system_title'] }}" type="text" class="form-control" placeholder="e.g. ABC High School">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label font-weight-semibold">School Address <span class="text-danger">*</span></label>
                                    <textarea required name="address" class="form-control" rows="2" placeholder="Enter complete school address">{{ $s['address'] }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label font-weight-semibold">Phone Number</label>
                                            <input name="phone" value="{{ $s['phone'] }}" type="text" class="form-control" placeholder="School contact number">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label font-weight-semibold">School Email</label>
                                            <input name="system_email" value="{{ $s['system_email'] }}" type="email" class="form-control" placeholder="school@example.com">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Session Section -->
                        <div class="card border-left-3 border-left-success mt-3">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="icon-calendar22 mr-2"></i>Academic Session
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="current_session" class="form-label font-weight-semibold">Current Session <span class="text-danger">*</span></label>
                                            <select data-placeholder="Choose..." required name="current_session" id="current_session" class="form-control select-search">
                                                <option value=""></option>
                                                @for($y=date('Y', strtotime('- 3 years')); $y<=date('Y', strtotime('+ 1 years')); $y++)
                                                    <option {{ ($s['current_session'] == (($y-=1).'-'.($y+=1))) ? 'selected' : '' }}>{{ ($y-=1).'-'.($y+=1) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Term Begins</label>
                                    <div class="col-lg-9">
                                        <input name="term_begins" value="{{ $s['term_begins'] ? date('Y-m-d', strtotime($s['term_begins'])) : '' }}" type="date" class="form-control" placeholder="Date Term Begins">

                                    </div>
                                </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Term Ends</label>
                                    <div class="col-lg-9">
                                        <input name="term_ends" value="{{ $s['term_ends'] ? date('Y-m-d', strtotime($s['term_ends'])) : '' }}" type="date" class="form-control" placeholder="Date Term Ends">

                                    </div>
                                </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="lock_exam" class="form-label font-weight-semibold">Exam Lock Status</label>
                                    <select class="form-control select" name="lock_exam" id="lock_exam">
                                        <option {{ $s['lock_exam'] ? 'selected' : '' }} value="1">Lock Exams</option>
                                        <option {{ $s['lock_exam'] ?: 'selected' }} value="0">Allow Exam Access</option>
                                    </select>
                                    <small class="form-text text-muted">{{ __('msg.lock_exam') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logo and Fees Section -->
                    <div class="col-md-4">
                        <!-- Logo Upload -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="icon-image2 mr-2"></i>School Logo
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <img style="width: 120px; height: 120px; object-fit: cover;"
                                         src="{{ $s['logo'] ?? asset('global_assets/images/placeholders/placeholder.jpg') }}"
                                         alt="School Logo"
                                         class="img-thumbnail">
                                </div>
                                <input name="logo" accept="image/*" type="file" class="file-input"
                                       data-show-caption="false" data-show-upload="false" data-fouc>
                                <small class="form-text text-muted">Upload school logo (Max: 2MB)</small>
                            </div>
                        </div>

                        <!-- Fees Section -->
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="icon-cash mr-2"></i>Term Fees
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach($class_types as $ct)
                                    <div class="form-group">
                                        <label class="form-label font-weight-semibold">{{ $ct->name }}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₦</span>
                                            </div>
                                            <input class="form-control"
                                                   value="{{ $s['next_term_fees_'.strtolower($ct->code)] }}"
                                                   name="next_term_fees_{{ strtolower($ct->code) }}"
                                                   placeholder="0.00"
                                                   type="number"
                                                   step="0.01">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if(!request('school_id'))
                            <span class="text-muted">
                                <i class="icon-info22 mr-1"></i>
                                A unique school code will be generated automatically
                            </span>
                        @endif
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            @if(request('school_id'))
                                <i class="icon-checkmark3 mr-2"></i>Update Settings
                            @else
                                <i class="icon-plus22 mr-2"></i>Create School
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
