<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use SimpleXLSX;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param string $file
     * @return array
     */
    public function readExcel(string $file): array
    {
        $file = app()->basePath('storage/app/public/uploads/') . $file;
        if (!file_exists($file)) {
            return array('status' => 'error', 'message' => 'File is not exists.');
        }
        $excel = new SimpleXLSX($file);
        return $excel->rows();
    }

    /**
     * @param $table
     * @param $column
     * @param null $exceptColumn
     * @return array
     */
    public function _getUniqueDataColumn($table, $column, $exceptColumn = null): array
    {
        $select = DB::table($table)->select($column);
        if (!empty($exceptColumn)) {
            $select->where($column, '!=', $exceptColumn);
        }
        return $select->get()->unique()->all();
    }
}
