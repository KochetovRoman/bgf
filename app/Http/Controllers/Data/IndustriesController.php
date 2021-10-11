<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IndustriesController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    public function getFilteredIndustriesData(Request $request): array
    {
        $request->validate([
            'filters' => 'required|array',
        ]);

        $filters = $request->filters;

        $industries = DB::table('industries');

        foreach ($filters as $column => $value) {
            $industries->where($column, $value['value']);
        }

        $data = $industries->get()->all();

        return array('status' => 'success', 'result' => $data);
    }
}
