<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }

    public function list(Request $request)
    {
        try {
            $filters = $request->all();
            $data = $this->service->getAll($filters);

            return response()->json([
                'status' => true,
                'data' => EmployeeResource::collection($data)
            ]);
        } catch (\Throwable $th) {
            info($th);

            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}
