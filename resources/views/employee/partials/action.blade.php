<div class="d-flex align-items-center gap-10 justify-content-center">
    <a href="{{ route('employee.edit', $employee->id) }}" class="btn btn-success">
        Edit
    </a>
    <button type="button" class="btn btn-danger btn-delete" data-route="{{ route('employee.destroy', $employee->id) }}"
        data-name="{{ $employee->name }}">
        Delete
    </button>
</div>
