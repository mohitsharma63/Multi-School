
@extends('layouts.master')
@section('page_title', 'Branch Details')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Branch Details</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Branch Name:</th>
                            <td>{{ $branch->name }}</td>
                        </tr>
                        <tr>
                            <th>Branch Code:</th>
                            <td>{{ $branch->code }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $branch->address }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $branch->phone ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $branch->email ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Head Name:</th>
                            <td>{{ $branch->head_name ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Head Phone:</th>
                            <td>{{ $branch->head_phone ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Head Email:</th>
                            <td>{{ $branch->head_email ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge badge-{{ $branch->is_active ? 'success' : 'danger' }}">
                                    {{ $branch->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $branch->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $branch->updated_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    </table>

                    <div class="text-right mt-3">
                        <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-primary">
                            <i class="icon-pencil mr-2"></i> Edit Branch
                        </a>
                        <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                            <i class="icon-arrow-left7 mr-2"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
