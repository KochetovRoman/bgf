<?php

namespace App\Http\Controllers\Data;

use \Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RegionsController extends Controller
{
    //
    /**
     * @return array
     */
    public function getRegionsToMap(): array
    {
        $data = DB::table('regions')->get()->all();

        $regionsData = array();

        try {
            foreach ($data as $key => $row) {
                $regionsData[$row->region] = $row->value;
            }
        } catch (Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'result' => $regionsData);

    }
}
