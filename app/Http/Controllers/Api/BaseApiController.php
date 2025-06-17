<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    protected function applyBranchFilter($query, Request $request)
    {
        $user = auth('api')->user();

        if (!$user->isSuperAdmin()) {
            // Non-super admin users can only access their branch data
            if (method_exists($query->getModel(), 'getTable')) {
                $table = $query->getModel()->getTable();
                if (\Schema::hasColumn($table, 'branch_id')) {
                    $query->where('branch_id', $user->branch_id);
                }
                if (\Schema::hasColumn($table, 'school_id')) {
                    $query->where('school_id', $user->school_id);
                }
            }
        } else {
            // Super admin can filter by specific branch/school if provided
            if ($request->has('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }
            if ($request->has('school_id')) {
                $query->where('school_id', $request->school_id);
            }
        }

        return $query;
    }

    protected function successResponse($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message = 'Error', $code = 400, $data = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
