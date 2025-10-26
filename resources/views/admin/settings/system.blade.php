@extends('admin.layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Settings (Super Admin Only)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSettingModal">
                            <i class="fas fa-plus"></i> Add New Setting
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 30%">Key</th>
                                    <th style="width: 35%">Value</th>
                                    <th style="width: 25%">Description</th>
                                    <th style="width: 10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $setting)
                                <tr>
                                    <td><code>{{ $setting->key }}</code></td>
                                    <td>
                                        @if($setting->type === 'boolean')
                                            <span class="badge badge-{{ $setting->value === '1' || $setting->value === 'true' ? 'success' : 'secondary' }}">
                                                {{ $setting->value === '1' || $setting->value === 'true' ? 'True' : 'False' }}
                                            </span>
                                        @else
                                            <span class="text-break">{{ Str::limit($setting->value, 50) }}</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $setting->description }}</small></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editSetting({{ $setting->id }}, '{{ $setting->key }}', '{{ addslashes($setting->value) }}', '{{ $setting->type }}', '{{ addslashes($setting->description) }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.settings.system.delete', $setting->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this setting?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No settings found. Add your first setting above.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Setting Modal -->
<div class="modal fade" id="addSettingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.settings.system.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Setting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="key" class="form-label">Key <span class="text-danger">*</span></label>
                        <input type="text" name="key" id="key" class="form-control" required placeholder="e.g., MAX_ADDITIONAL_MEMBERS">
                        <small class="text-muted">Use UPPERCASE_WITH_UNDERSCORES format</small>
                    </div>
                    <div class="mb-3">
                        <label for="value" class="form-label">Value</label>
                        <input type="text" name="value" id="value" class="form-control" placeholder="e.g., 2">
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="string">String</option>
                            <option value="integer">Integer</option>
                            <option value="boolean">Boolean</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="2" placeholder="Brief description of this setting"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Setting</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Setting Modal -->
<div class="modal fade" id="editSettingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.settings.system.store') }}" method="POST">
                @csrf
                <input type="hidden" name="setting_id" id="edit_setting_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Setting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_key" class="form-label">Key <span class="text-danger">*</span></label>
                        <input type="text" name="key" id="edit_key" class="form-control" required readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_value" class="form-label">Value</label>
                        <input type="text" name="value" id="edit_value" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit_type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="edit_type" class="form-control" required>
                            <option value="string">String</option>
                            <option value="integer">Integer</option>
                            <option value="boolean">Boolean</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Setting</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editSetting(id, key, value, type, description) {
    $('#edit_setting_id').val(id);
    $('#edit_key').val(key);
    $('#edit_value').val(value);
    $('#edit_type').val(type);
    $('#edit_description').val(description);
    $('#editSettingModal').modal('show');
}
</script>
@endpush
