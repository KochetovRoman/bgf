<?php

namespace App\Http\Controllers\Formatters;

use App\Interfaces\FormatterInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FormatterRegions extends Controller implements FormatterInterface
{

    public function formatterData(string $file = null)
    {
        $valuesMap = array(
            "3.0",
            "3.25",
            "3.5",
            "3.75",
            "4",
            "4.25",
            "4.5",
            "5",
            "5.25",
            "5.5",
            "5.75",
            "6.5",
        );

        $helper = array(
            'delete' => array(
                'Rheinland/HH'
            ),
            'region' => 'Rheinland'
        );



        try {
            $getDataColumn = $this->_getUniqueDataColumn('statistics', 'region');

            $importSql = array();
            $resultDataFormatter = array();
            foreach ($getDataColumn as $data) {
                if (!in_array($data->region, $helper['delete'])) {
//                $importSql['land'] = $helper['region'];
                    $importSql['region'] = $data->region;
                    $importSql['value'] = self::randLandValue($valuesMap);
                    $resultDataFormatter[] = $importSql;
                }
            }
        } catch (\Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'result' => $resultDataFormatter);

    }

    private static function randLandValue($valuesMap)
    {
        static $vMap;
        if (empty($vMap)) {
            $vMap = $valuesMap;
        }
        $count = count($vMap) - 1;
        $randKey = rand(0, $count);
        $value = $vMap[$randKey];
        unset($vMap[$randKey]);
        $vMap = array_values($vMap);
        return $value;
    }
}
