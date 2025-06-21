@extends('layouts.master')
@section('page_title', 'Edit Dorm - '.$dorm->name)
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Edit Dorm</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-update" data-reload="#page-header" method="post" action="{{ route('dorms.update', $dorm->id) }}">
                        @csrf @method('PUT')

                        @if(Qs::userIsSuperAdmin())
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">School <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="Choose School..." required name="school_id" class="form-control select-search">
                                            @foreach($schools as $s)
                                                <option {{ ($dorm->school_id == $s->id) ? 'selected' : '' }} value="{{ $s->id }}">{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @else
                                {{-- For admin users, show school name but make it non-editable --}}
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">School</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" value="{{ $dorm->school->name ?? 'No School Assigned' }}" readonly>
                                        <small class="form-text text-muted">School assigned by Super Admin (cannot be changed)</small>
                                    </div>
                                </div>
                                @endif

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="name" value="{{ $dorm->name }}" required type="text" class="form-control" placeholder="Name of Dormitory">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Description</label>
                            <div class="col-lg-9">
                                <input name="description" value="{{ $dorm->description }}"  type="text" class="form-control" placeholder="Description of Dormitory">
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

    {{--Class Edit Ends--}}

@endsection
