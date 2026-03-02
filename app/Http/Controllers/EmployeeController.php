<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Traits\UploadTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    use UploadTraits;
    protected $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return view('employee.index');
        } catch (\Throwable $th) {
            info($th);

            abort(500);
        }
    }

    public function list(Request $request)
    {
        try {
            $data = Employee::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('photo', function (Employee $employee) {
                    return view('employee.partials.photo', [
                        'photo' => $employee->photoUrl()
                    ]);
                })
                ->editColumn('birth_date', function ($row) {
                    return $row->birth_date ? Carbon::parse($row->birth_date)->format('d F Y') : null;
                })
                ->addColumn('action', function ($row) {
                    return view('employee.partials.action', [
                        'employee' => $row
                    ]);
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search != '') {
                        $query->where(function ($q) use ($request) {
                            $q->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%')
                                ->orWhere('phone', 'like', '%' . $request->search . '%');
                        });
                    }

                    if ($request->has('position') && $request->position != 'all') {
                        $query->where(function ($q) use ($request) {
                            $q->where('position', $request->position);
                        });
                    }

                    if (
                        $request->has('start_date') && $request->start_date != ''
                        && $request->has('end_date') && $request->end_date != ''
                    ) {
                        $query->where(function ($q) use ($request) {
                            $q->whereBetween('birth_date', [$request->start_date, $request->end_date]);
                        });
                    }
                })
                ->order(function ($query) use ($request) {
                    $order = $request->input('order')[0];

                    if ($order['column'] == 0) {
                        $query->orderBy('created_at', 'desc');
                    } elseif ($order['column'] == 1) {
                        $query->orderBy('name', $order['dir']);
                    } elseif ($order['column'] == 2) {
                        $query->orderBy('email', $order['dir']);
                    } elseif ($order['column'] == 3) {
                        $query->orderBy('phone', $order['dir']);
                    } elseif ($order['column'] == 4) {
                        $query->orderBy('position', $order['dir']);
                    } elseif ($order['column'] == 5) {
                        $query->orderBy('birth_date', $order['dir']);
                    }
                })
                ->rawColumns(['photo', 'action'])
                ->make(true);
        } catch (\Throwable $th) {
            info($th);

            abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('employee.create');
        } catch (\Throwable $th) {
            info($th);

            abort(500);
        }
    }

    public function uploadPhoto(Request $request)
    {
        try {
            if ($request->hasFile('photo')) {
                $file = $this->uploadFile($request->file('photo'));

                return response()->json([
                    'file' => $file
                ]);
            }

            return response()->json([
                'error' => 'Upload gagal'
            ], 400);
        } catch (\Throwable $th) {
            info($th);

            abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            $data = $request->validated();

            $data['photo'] = $request->photo;

            $this->service->create($data);

            return redirect()->route('employee.index')
                ->with('success', 'Employee created successfully.');
        } catch (\Throwable $th) {
            info($th);

            abort(500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return response()->json($employee);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        try {
            return view('employee.edit', [
                'employee' => $employee
            ]);
        } catch (\Throwable $th) {
            info($th);

            abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $data = $request->validated();

            $data['photo'] = $employee->photo;
            if ($request->photo && $request->photo != $employee->photo) {
                if ($employee->photo) {
                    $this->deleteFile($employee->photo);
                }

                $data['photo'] = $request->photo;
            }

            $this->service->update($data, $employee->id);

            return redirect()->route('employee.index')
                ->with('success', 'Employee updated successfully.');
        } catch (\Throwable $th) {
            info($th);

            abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            $this->service->delete($employee->id);

            return response()->json([
                'message' => 'Employee deleted successfully.'
            ]);
        } catch (\Throwable $th) {
            info($th);

            abort(500);
        }
    }
}
