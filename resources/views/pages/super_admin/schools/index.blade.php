
@extends('layouts.master')
@section('page_title', 'Manage Schools')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Manage Schools</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#all-schools" class="nav-link active" data-toggle="tab">All Schools</a></li>
            <li class="nav-item"><a href="#new-school" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Create New School</a></li>
        </ul>

        <div class="tab-content">
            {{-- All Schools --}}
            <div class="tab-pane fade show active" id="all-schools">
                <table class="table datatable-button-html5-columns">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Branches</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($schools as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->code }}</td>
                            <td>{{ $s->address }}</td>
                            <td>{{ $s->phone }}</td>
                            <td>{{ $s->email }}</td>
                            <td>{{ $s->branches->count() }}</td>
                            <td>
                                <span class="badge badge-{{ $s->is_active ? 'success' : 'danger' }}">
                                    {{ $s->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="list-icons">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-left">
                                            <a href="{{ route('schools.show', $s->id) }}" class="dropdown-item"><i class="icon-eye"></i> View</a>
                                            <a href="{{ route('schools.edit', $s->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                            <a id="{{ $s->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                            <form method="post" id="item-delete-{{ $s->id }}" action="{{ route('schools.destroy', $s->id) }}" class="hidden">@csrf @method('delete')</form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Add New School --}}
            <div class="tab-pane fade" id="new-school">
                <div class="row">
                    <div class="col-md-8">
                        <form method="post" action="{{ route('schools.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">School Name <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input name="name" value="{{ old('name') }}" required type="text" class="form-control" placeholder="School Name">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">School Code <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input name="code" value="{{ old('code') }}" required type="text" class="form-control" placeholder="e.g. SCH001" maxlength="10">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">School Title</label>
                                <div class="col-lg-9">
                                    <input name="system_title" value="{{ old('system_title') }}" type="text" class="form-control" placeholder="School Acronym">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">Address <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <textarea name="address" required class="form-control" rows="3" placeholder="School Address">{{ old('address') }}</textarea>
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
                                <label class="col-lg-3 col-form-label font-weight-semibold">Current Session</label>
                                <div class="col-lg-9">
                                    <select name="current_session" class="form-control select2">
                                        <option value="">Select Session</option>
                                        @for($y=date('Y', strtotime('- 3 years')); $y<=date('Y', strtotime('+ 1 years')); $y++)
                                            <option value="{{ ($y-=1).'-'.($y+=1) }}">{{ ($y-=1).'-'.($y+=1) }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">Logo</label>
                                <div class="col-lg-9">
                                    <input name="logo" accept="image/*" type="file" class="file-input" data-show-caption="false" data-show-upload="false" data-fouc>
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
