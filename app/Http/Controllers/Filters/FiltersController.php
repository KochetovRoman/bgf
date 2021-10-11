<?php

namespace App\Http\Controllers\Filters;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FiltersController extends Controller
{
    /**
     * Get filters from the list statistics
     *
     * @return array
     */
    public function getFiltersStatistics(): array
    {
        $getColumnFilters = array(
            'statistics' => array(
                'year' => array(),
                'ageRange' => array(
                    'exceptColumn' => 'Gesamt'
                ),
                'region' => array(),
                'detail' => array(),
                'gender' => array(
                    'exceptColumn' => 'Gesamt'
                )
            ),
        );

        try {
            $filterData = $this->getUniqueDataColumn($getColumnFilters);
        } catch (\Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'result' => $filterData);
    }

    /**
     *  Get filters industries groups name
     * @return array
     */
    public function getFiltersIndustries(): array
    {
        $getColumnFilters = array(
            'industries' => array(
                'industryGroup' => array(),
            ),
        );

        try {
            $filterData = $this->getUniqueDataColumn($getColumnFilters);
        } catch (\Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'result' => $filterData);
    }

    /**
     * @param $getColumnFilters
     * @return array
     */
    private function getUniqueDataColumn($getColumnFilters): array
    {
        $filterData = array();

        foreach ($getColumnFilters as $table => $columns) {
            foreach ($columns as $column => $rules) {
                if (!empty($rules)) {
                    $getDataColumn = $this->_getUniqueDataColumn($table, $column,$rules['exceptColumn']);
                }else {
                    $getDataColumn = $this->_getUniqueDataColumn($table, $column);
                }

                $filterData[$column] = array_values($getDataColumn);

            }
        }
        return $filterData;
    }

}
