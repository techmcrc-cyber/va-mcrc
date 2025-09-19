@extends('admin.layouts.app')

@section('title', 'Manage Retreats')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Retreats List</h3>
                    <a href="{{ route('admin.retreats.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Retreat
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="retreats-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Timings</th>
                                    <th>Seats</th>
                                    <th>Criteria</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($retreats as $retreat)
                                <tr>
                                    <td>{{ $retreat->title }}</td>
                                    <td>
                                        {{ $retreat->start_date->format('M d, Y') }} - 
                                        {{ $retreat->end_date->format('M d, Y') }}
                                    </td>
                                    <td>{{ $retreat->timings }}</td>
                                    <td>{{ $retreat->seats }}</td>
                                    <td>
                                        @php
                                            $criteriaLabels = [
                                                'male_only' => 'Male Only',
                                                'female_only' => 'Female Only',
                                                'priests_only' => 'Priests Only',
                                                'sisters_only' => 'Sisters Only',
                                                'youth_only' => 'Youth Only',
                                                'children' => 'Children',
                                                'no_criteria' => 'No Criteria'
                                            ];
                                        @endphp
                                        {{ $criteriaLabels[$retreat->criteria] ?? $retreat->criteria }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $retreat->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $retreat->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.retreats.show', $retreat) }}" class="btn btn-info btn-sm" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.retreats.edit', $retreat) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.retreats.destroy', $retreat) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this retreat?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $retreats->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#retreats-table').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "responsive": true,
        });
    });
</script>
@endpush
