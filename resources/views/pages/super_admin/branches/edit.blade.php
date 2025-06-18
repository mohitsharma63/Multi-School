
@extends('layouts.master')
@section('page_title', 'Edit Branch')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Edit Branch</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <form method="post" action="{{ route('branches.update', $branch->id) }}">
                        @csrf @method('PUT')
                        
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Branch Name <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="name" value="{{ $branch->name }}" required type="text" class="form-control" placeholder="Branch Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Branch Code <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="code" value="{{ $branch->code }}" required type="text" class="form-control" placeholder="e.g. BR001" maxlength="10">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Address <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <textarea name="address" required class="form-control" rows="3" placeholder="Branch Address">{{ $branch->address }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Phone</label>
                            <div class="col-lg-9">
                                <input name="phone" value="{{ $branch->phone }}" type="text" class="form-control" placeholder="Phone Number">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Email</label>
                            <div class="col-lg-9">
                                <input name="email" value="{{ $branch->email }}" type="email" class="form-control" placeholder="Email Address">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Head Name</label>
                            <div class="col-lg-9">
                                <input name="head_name" value="{{ $branch->head_name }}" type="text" class="form-control" placeholder="Branch Head Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Head Phone</label>
                            <div class="col-lg-9">
                                <input name="head_phone" value="{{ $branch->head_phone }}" type="text" class="form-control" placeholder="Branch Head Phone">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Head Email</label>
                            <div class="col-lg-9">
                                <input name="head_email" value="{{ $branch->head_email }}" type="email" class="form-control" placeholder="Branch Head Email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Status <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="is_active" required>
                                    <option value="1" {{ $branch->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$branch->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Update Branch <i class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
