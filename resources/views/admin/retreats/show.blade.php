@extends('admin.layouts.app')

@section('title', 'View Retreat: ' . $retreat->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Retreat Details: {{ $retreat->title }}</h3>
                    <div class="card-tools">
                        @can('edit-retreats')
                        <a href="{{ route('admin.retreats.edit', $retreat) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endcan
                        <a href="{{ route('admin.retreats.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h4>Description</h4>
                                <div class="border rounded p-3 bg-light">
                                    {!! $retreat->description !!}
                                </div>
                            </div>

                            @if($retreat->instructions)
                            <div class="mb-4">
                                <h4>Instructions & Guidelines</h4>
                                <div class="border rounded p-3 bg-light">
                                    {!! $retreat->instructions !!}
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Retreat Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge {{ $retreat->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $retreat->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                @if($retreat->is_featured)
                                                    <span class="badge bg-info ms-1">Featured</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>
                                                {{ $retreat->start_date->format('M d, Y') }} - 
                                                {{ $retreat->end_date->format('M d, Y') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Timings</th>
                                            <td>{{ $retreat->timings }}</td>
                                        </tr>
                                        <tr>
                                            <th>Available Seats</th>
                                            <td>{{ $retreat->seats }}</td>
                                        </tr>
                                        <tr>
                                            <th>Eligibility Criteria</th>
                                            <td>
                                                {{ $retreat->criteriaRelation ? $retreat->criteriaRelation->name : 'No Criteria' }}
                                            </td>
                                        </tr>
                                        @if($retreat->special_remarks)
                                        <tr>
                                            <th>Special Remarks</th>
                                            <td>{{ $retreat->special_remarks }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Created</th>
                                            <td>{{ $retreat->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated</th>
                                            <td>{{ $retreat->updated_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    </table>

                                    <div class="mt-3 d-flex justify-content-between">
                                        <form action="{{ route('admin.retreats.destroy', $retreat) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this retreat?')">
                                            @csrf
                                            @can('delete-retreats')
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> Delete Retreat
                                            </button>
                                            @endcan
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header {
        background-color: #f8f9fa;
    }
    .card-title {
        margin-bottom: 0;
    }
    .table th {
        width: 30%;
    }
</style>
@endpush
