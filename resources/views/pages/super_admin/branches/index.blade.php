
@extends('layouts.master')
@section('page_title', 'Manage Branches')
@section('content')

@php
use Illuminate\Support\Str;
@endphp

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Branches</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-branches" class="nav-link active" data-toggle="tab">All Branches</a></li>
                <li class="nav-item"><a href="#new-branch" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Add New Branch</a></li>
            </ul>

            <div class="tab-content">
                {{-- All Branches --}}
                <div class="tab-pane fade show active" id="all-branches">
                    <div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Head Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($branches as $b)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $b->name }}</td>
                                    <td>{{ $b->code }}</td>
                                    <td>{{ Str::limit($b->address, 50) }}</td>
                                    <td>{{ $b->phone }}</td>
                                    <td>{{ $b->email }}</td>
                                    <td>{{ $b->head_name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $b->is_active ? 'success' : 'danger' }}">
                                            {{ $b->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
                                                {{--Edit--}}
                                                <a href="{{ route('branches.edit', $b->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                {{--Show--}}
                                                <a href="{{ route('branches.show', $b->id) }}" class="dropdown-item"><i class="icon-eye"></i> View</a>
                                                {{--Delete--}}
                                                <a id="{{ $b->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                <form method="post" id="item-delete-{{ $b->id }}" action="{{ route('branches.destroy', $b->id) }}" class="hidden">@csrf @method('delete')</form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Add New Branch --}}
                <div class="tab-pane fade" id="new-branch">
                    <div class="row">
                        <div class="col-md-8">
                            <form method="post" action="{{ route('branches.store') }}">
                                @csrf

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Branch Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="name" value="{{ old('name') }}" required type="text" class="form-control" placeholder="Branch Name">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Branch Code <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="code" value="{{ old('code') }}" required type="text" class="form-control" placeholder="e.g. BR001" maxlength="10">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Address <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <textarea name="address" required class="form-control" rows="3" placeholder="Branch Address">{{ old('address') }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Phone</label>
                                    <div class="col-lg-9">
                                        <input name="phone" value="{{ old('phone') }}" type="text" class="form-control" placeholder="Phone Number">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Email</label>
                                    <div class="col-lg-9">
                                        <input name="email" value="{{ old('email') }}" type="email" class="form-control" placeholder="Email Address">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Head Name</label>
                                    <div class="col-lg-9">
                                        <input name="head_name" value="{{ old('head_name') }}" type="text" class="form-control" placeholder="Branch Head Name">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Head Phone</label>
                                    <div class="col-lg-9">
                                        <input name="head_phone" value="{{ old('head_phone') }}" type="text" class="form-control" placeholder="Branch Head Phone">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Head Email</label>
                                    <div class="col-lg-9">
                                        <input name="head_email" value="{{ old('head_email') }}" type="email" class="form-control" placeholder="Branch Head Email">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Status <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select class="form-control select" name="is_active" required>
                                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
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
        </div>
    </div>

@endsection
