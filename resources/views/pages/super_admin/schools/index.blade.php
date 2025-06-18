@extends('layouts.master')
@section('page_title', 'Manage Schools')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Schools</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight" data-type="nav">
                <li class="nav-item"><a href="#new-school" class="nav-link active" data-toggle="tab">Add New School</a></li>
                <li class="nav-item"><a href="#all-schools" class="nav-link" data-toggle="tab">All Schools</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="new-school">
                    <div class="form-modern">
                        <div class="row">
                            <div class="col-md-12">
                                <form class="ajax-store" data-reload="#page-header" method="post" action="{{ route('schools.store') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="font-weight-semibold text-dark mb-2">School Name <span class="text-danger">*</span></label>
                                                <input name="name" value="{{ old('name') }}" required type="text" class="form-control form-control-modern" placeholder="Enter school name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="font-weight-semibold text-dark mb-2">School Acronym</label>
                                                <input name="acronym" value="{{ old('acronym') }}" type="text" class="form-control form-control-modern" placeholder="School acronym (e.g., ABC)">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="font-weight-semibold text-dark mb-2">Email</label>
                                                <input name="email" value="{{ old('email') }}" type="email" class="form-control form-control-modern" placeholder="School email address">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="font-weight-semibold text-dark mb-2">Phone</label>
                                                <input name="phone" value="{{ old('phone') }}" type="text" class="form-control form-control-modern" placeholder="School phone number">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group-modern">
                                        <label class="font-weight-semibold text-dark mb-2">Address <span class="text-danger">*</span></label>
                                        <textarea name="address" required class="form-control form-control-modern" placeholder="Enter school address" rows="3">{{ old('address') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="current_session" class="col-lg-3 col-form-label font-weight-semibold">Current Session</label>
                                        <div class="col-lg-9">
                                            <select name="current_session" id="current_session" class="select-search form-control">
                                                <option value="">Select Session</option>
                                                @for($y=date('Y', strtotime('- 3 years')); $y<=date('Y', strtotime('+ 1 years')); $y++)
                                                    <option value="{{ ($y-=1).'-'.($y+=1) }}">{{ ($y-=1).'-'.($y+=1) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label font-weight-semibold">Term Ends</label>
                                        <div class="col-lg-9">
                                            <input name="term_ends" value="{{ old('term_ends') }}" type="text" class="form-control date-pick" placeholder="Date Term Ends">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label font-weight-semibold">Next Term Begins</label>
                                        <div class="col-lg-9">
                                            <input name="term_begins" value="{{ old('term_begins') }}" type="text" class="form-control date-pick" placeholder="Date Term Begins">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="lock_exam" class="col-lg-3 col-form-label font-weight-semibold">Lock Exam</label>
                                        <div class="col-lg-9">
                                            <select class="form-control select" name="lock_exam" id="lock_exam">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="is_active" class="col-lg-3 col-form-label font-weight-semibold">Status</label>
                                        <div class="col-lg-9">
                                            <select class="form-control select" name="is_active" id="is_active">
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label font-weight-semibold">Logo</label>
                                        <div class="col-lg-9">
                                            <input name="logo" accept="image/*" type="file" class="file-input" data-show-caption="false" data-show-upload="false" data-fouc>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Add School <i class="icon-plus ml-2"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="tab-pane fade" id="all-schools">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Acronym</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Session</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($schools as $s)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $s->name }}</td>
                                <td>{{ $s->acronym }}</td>
                                <td>{{ $s->email }}</td>
                                <td>{{ $s->phone }}</td>
                                <td>{{ $s->current_session }}</td>
                                <td>
                                    @if($s->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
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
            </div>
        </div>
    </div>

@endsection
