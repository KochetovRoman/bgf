<?php

namespace App\Http\Controllers\Formatters;

use App\Http\Controllers\Controller;
use \Exception;
use App\Interfaces\FormatterInterface;

class FormatterDashboardToDb extends Controller implements FormatterInterface
{

    /**
     * formatter data excel to db
     *
     * @param string|null $file
     * @return array
     */
    public function formatterData(string $file = null): array
    {
        $importData = $this->readExcel($file);

        if (!empty($importData['status']) && $importData['status'] == 'error') {
            return $importData;
        }

        unset($importData[0], $importData[1]);
        $importData = array_values($importData);

        try {
            $result = $this->_formatterData($importData);
        } catch (Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'result' => $result);
    }

    /**
     * helper for formatter excel
     *
     * @param array $data
     * @return array
     */
    private function _formatterData(array $data): array
    {
        // TODO config('app.name')
        $help = array(
            'ageRangeAndDetail' => array(
                'delimiter' => 'Jahre',
            ),
            'ageRange' => array(
                'key' => 0,
                'explodeKey' => 0,
            ),
            'detail' => array(
                'key' => 0,
                'explodeKey' => 1,
            ),
            'regions' => array(
                'key' => 0,
            ),
            'yearsAndGenders' => array(
                'key' => 1,
                'delimiter' => "\n"
            ),
            'year' => array(
                'explodeKey' => 0,
            ),
            'gender' => array(
                'explodeKey' => 1,
            ),
            'percent' => array(
                'key' => 1,
                'value' => '%'
            ),
        );
        $detailRequired = array(
            'AU-Fälle je 100 VJ',
            'AU-Tage je 100 VJ',
            'AU-Dauer je Bescheinigung',
            'Krankenstand Gesamt',
        );
//---Test---
        $importData = $data;//array_slice($data, 0, 10);
//---Test---

        $regions = $importData[$help['regions']['key']];
        $yearsAndGenders = $importData[$help['yearsAndGenders']['key']];

        unset($importData[$help['regions']['key']], $importData[$help['yearsAndGenders']['key']]);
        $importData = array_values($importData);

        $resultDataFormatter = array();
        foreach ($importData as $key => $row) {
            $importSql = array();
            foreach ($row as $k => $value) {
                if ($k == $help['ageRange']['key'] || $k == $help['detail']['key']) {
                    $explodeValue = explode($help['ageRangeAndDetail']['delimiter'], $value);

                    if (count($explodeValue) < 2) {
                        $explodeValue = explode(' ', $value);
                        if (count($explodeValue) < 2) {
                            break;
                        }

                        $importSql['ageRange'] = trim($explodeValue[0]);
                        unset($explodeValue[0]);
                        $importSql['detail'] = trim(implode(' ', $explodeValue));
                    } else {
                        $ageRange = str_replace('-', '- ', $explodeValue[$help['ageRange']['explodeKey']]);
                        $ageRange = preg_replace('/\s+/', ' ', $ageRange);
                        $importSql['ageRange'] = trim($ageRange) .
                            ' ' . $help['ageRangeAndDetail']['delimiter'];
                        $importSql['detail'] = trim($explodeValue[$help['detail']['explodeKey']]);
                    }

                    if (!in_array($importSql['detail'], $detailRequired)) {
                        break;
                    }
                } else if ($k == $help['percent']['key']) {
                    $importSql['percent'] = ($value == $help['percent']['value']);
                } else {
                    $importSql['region'] = trim(mb_substr($regions[$k], mb_strpos($regions[$k], ' ')));
                    $explodeValue = explode($help['yearsAndGenders']['delimiter'], $yearsAndGenders[$k]);
                    $importSql['year'] = trim($explodeValue[$help['year']['explodeKey']]);
                    $importSql['gender'] =  ucfirst(trim($explodeValue[$help['gender']['explodeKey']]));
                    $importSql['value'] = $value;
                    $resultDataFormatter[] = $importSql;
                }
            }
        }

        return $resultDataFormatter;
    }

}
