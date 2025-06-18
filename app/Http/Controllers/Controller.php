<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Execute a callback within a database transaction
     */
    protected function executeInTransaction(callable $callback)
    {
        return DB::transaction($callback);
    }

    /**
     * Safely rollback a transaction if one is active
     */
    protected function safeRollback()
    {
        if (DB::transactionLevel() > 0) {
            DB::rollback();
        }
    }

    /**
     * Begin transaction safely
     */
    protected function safeBeginTransaction()
    {
        DB::beginTransaction();
    }

    /**
     * Commit transaction safely
     */
    protected function safeCommit()
    {
        if (DB::transactionLevel() > 0) {
            DB::commit();
        }
    }
}
