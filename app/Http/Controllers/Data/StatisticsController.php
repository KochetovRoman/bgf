<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Exception;
use Illuminate\Support\Facades\DB;


class StatisticsController extends Controller
{
    /**
     *  Get data for a graph
     * @return array
     */
    public function getGraphData(): array
    {
        $data = DB::table('statistics')->get()->all();

        $graphData = array();

        try {
            foreach ($data as $key => $statistic) {
                $graphData[$statistic->region][$statistic->year][$statistic->detail]
                [$statistic->ageRange][$statistic->gender][] = $statistic->value;
            }
        } catch (Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'result' => $graphData);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getFilteredGraphData(Request $request): array
    {
        $request->validate([
            'filters' => 'required|array',
        ]);

        $filterCheckData = array(
            'ageRange' => array(
                'default' => 'Gesamt',
            ),
            'gender' => array(
                'default' => 'Gesamt',
            )
        );

        $filters = $request->filters;

        $statistic = DB::table('statistics');

        foreach ($filters as $column => $value) {
            if ($this->checkFiltersKeysArray($column)) {
                if (!empty($value['values']) && is_array($value['values'])) {
                    $statistic->whereIn($column, $value['values']);
                } else if (array_key_exists($column, $filterCheckData)) {
                    $statistic->where($column, '=', $filterCheckData[$column]['default']);
                }
            }
        }

        $data = $statistic->get()->all();
        $data = $this->_getFilteredGraphData($data);

        return array('status' => 'success', 'result' => $data);
    }

    /**
     * @param array $statistics
     * @return array
     */
    private function _getFilteredGraphData(array $statistics): array
    {
        $filteredResult = array();
        $resultData = array();
        foreach ($statistics as $key => $statistic) {
            if (empty($filteredResult[$statistic->detail][$statistic->year][$statistic->gender])) {
                $filteredResult[$statistic->detail][$statistic->year][$statistic->gender] = 0;
            }
            $filteredResult[$statistic->detail][$statistic->year][$statistic->gender] += $statistic->value;

        }
        $id = 0;
        foreach ($filteredResult as $detail => $data) {
            $fData = array(
                'id' => $id,
                'title' => $detail
            );
            foreach ($data as $year => $values) {
                $fData['chartData'][] = array(
                    'value' => $values,
                    'year' => $year
                );

            }
            $resultData[] = $fData;
            ++$id;
        }

        return array_values($resultData);
    }

    /**
     * @param string $key
     * @return bool
     */
    private function checkFiltersKeysArray(string $key): bool
    {
        // filter = column
        $columns = array(
            "region",
            "detail",
            "ageRange",
            "gender",
        );

        return in_array($key, $columns);
    }

}
