
@extends('layouts.master')
@section('page_title', 'Edit School - '.$school->name)
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Edit School - {{ $school->name }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <form class="ajax-update" method="post" action="{{ route('schools.update', $school->id) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">School Name <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="name" value="{{ $school->name }}" required type="text" class="form-control" placeholder="Name of School">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">School Acronym</label>
                            <div class="col-lg-9">
                                <input name="acronym" value="{{ $school->acronym }}" type="text" class="form-control" placeholder="School Acronym">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Email</label>
                            <div class="col-lg-9">
                                <input name="email" value="{{ $school->email }}" type="email" class="form-control" placeholder="School Email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Phone</label>
                            <div class="col-lg-9">
                                <input name="phone" value="{{ $school->phone }}" type="text" class="form-control" placeholder="Phone">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Address <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <textarea name="address" required class="form-control" placeholder="School Address" rows="3">{{ $school->address }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="current_session" class="col-lg-3 col-form-label font-weight-semibold">Current Session</label>
                            <div class="col-lg-9">
                                <select name="current_session" id="current_session" class="select-search form-control">
                                    <option value="">Select Session</option>
                                    @for($y=date('Y', strtotime('- 3 years')); $y<=date('Y', strtotime('+ 1 years')); $y++)
                                        <option {{ ($school->current_session == (($y-=1).'-'.($y+=1))) ? 'selected' : '' }} value="{{ ($y-=1).'-'.($y+=1) }}">{{ ($y-=1).'-'.($y+=1) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Term Ends</label>
                            <div class="col-lg-9">
                                <input name="term_ends" value="{{ $school->term_ends ? $school->term_ends->format('m/d/Y') : '' }}" type="text" class="form-control date-pick" placeholder="Date Term Ends">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Next Term Begins</label>
                            <div class="col-lg-9">
                                <input name="term_begins" value="{{ $school->term_begins ? $school->term_begins->format('m/d/Y') : '' }}" type="text" class="form-control date-pick" placeholder="Date Term Begins">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="lock_exam" class="col-lg-3 col-form-label font-weight-semibold">Lock Exam</label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="lock_exam" id="lock_exam">
                                    <option {{ $school->lock_exam ? 'selected' : '' }} value="1">Yes</option>
                                    <option {{ !$school->lock_exam ? 'selected' : '' }} value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="is_active" class="col-lg-3 col-form-label font-weight-semibold">Status</label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="is_active" id="is_active">
                                    <option {{ $school->is_active ? 'selected' : '' }} value="1">Active</option>
                                    <option {{ !$school->is_active ? 'selected' : '' }} value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Logo</label>
                            <div class="col-lg-9">
                                @if($school->logo)
                                    <div class="mb-3">
                                        <img style="width: 100px" height="100px" src="{{ $school->logo }}" alt="School Logo">
                                    </div>
                                @endif
                                <input name="logo" accept="image/*" type="file" class="file-input" data-show-caption="false" data-show-upload="false" data-fouc>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Update School <i class="icon-paperplane ml-2"></i></button>
                </div>
            </form>
        </div>
    </div>

@endsection
