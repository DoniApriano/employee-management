<?php
namespace App\Services;

use App\Models\Employee;
use App\Traits\UploadTraits;

class EmployeeService
{
    use UploadTraits;

    public function getAll($filters = [])
    {
        $data = Employee::query();
        if (isset($filters['position']) && $filters['position'] != 'all') {
            $data->where('position', $filters['position']);
        }

        if (
            isset($filters['start_date']) && $filters['start_date'] != null 
            && isset($filters['end_date']) && $filters['end_date'] != null
        ) {
            $data->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['search']) && $filters['search'] != '') {
            $data->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $data->orderBy('created_at', 'desc')->get();
    }

    public function create($data)
    {
        return Employee::create($data);
    }

    public function update($data, $id)
    {
        $employee = Employee::find($id);
        return $employee->update($data);
    }

    public function delete($id)
    {
        $employee = Employee::find($id);
        if ($employee->photo) {
            $this->deleteFile($employee->photo);
        }
        return $employee->delete();
    }
}