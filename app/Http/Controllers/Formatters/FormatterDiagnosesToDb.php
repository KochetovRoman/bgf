<?php

namespace App\Http\Controllers\Formatters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\FormatterInterface;

class FormatterDiagnosesToDb extends Controller implements FormatterInterface
{

    /**
     * formatter data excel to db
     *
     * @param string|null $file
     *
     * @return array
     */
    public function formatterData(string $file = null): array
    {
        $importData = $this->readExcel($file);

        if (!empty($importData['status']) && $importData['status'] == 'error') {
            return $importData;
        }

        unset($importData[0]);
        $importData = array_values($importData);

        try {
            $result = $this->_formatterData($importData);
        } catch (\Exception $e) {
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
        $help = array(
            'detail' => array(
                'key' => 0,
                'explodeKey' => 1,
            ),
            'diagnose' => array(
                'key' => 0,
                'explodeKey' => 0,
            ),
            'regions' => array(
                'key' => 0,
                'line' => 0,
            ),
            'year' => array(
                'key' => 0,
            ),
            'value' => array(
                'key' => 1,
                'line' => 0,
            ),
        );
        $detailRequired = array(
            'AU-Fälle je 100 VJ',
            'AU-Tage je 100 VJ',
            'AU-Dauer je Bescheinigung',
            'Krankenstand Gesamt',
        );

//---Test---
        //$importData = array_slice($data, 0, 41);
//---Test---

        $importData = $data;

        //Todo test data. Now 1 region
        $regions = $importData[$help['regions']['line']][$help['regions']['key']];
        $years = $importData[$help['regions']['line']];

        unset($years[$help['regions']['key']]);
        unset($importData[0]);

        $importData = array_values($importData);

        $intermediateResultData = array();
        foreach ($importData as $key => $row) {
            $importSql = array();
            foreach ($row as $k => $value) {
                if ($k == $help['detail']['key'] || $k == $help['diagnose']['key']) {
                    $explodeDiagnoseAndDetail = $this->explodeDiagnoseAndDetail($value);

                    if (is_array($explodeDiagnoseAndDetail)) {
                        $explodeValue = $explodeDiagnoseAndDetail['explode'];
                        $importSql['detail'] = trim($explodeValue[$help['detail']['explodeKey']]);
                        $importSql['diagnosis'] = $explodeDiagnoseAndDetail['diagnosis'];
                    }
                    // else not a true diagnosis
                    else {
                        break;
                    }
                    //if not a true detail required
                    /*
                    if (!in_array($importSql['detail'], $detailRequired)) {
                        break;
                    }
                    */
                }
                else {
                    //if value is empty = empty cell in row = end of data
                    if (empty($value)) {
                        break;
                    }
                    $importSql['value'] = $value;
                    //test data. Now 1 region
                    $importSql['region'] = $regions;
                    $importSql['year'] = trim($years[$k]);
                    $intermediateResultData[$importSql['diagnosis']][$key ][] = $importSql;
                }
            }
        }

        return $this->formattingDiagnosisModel($intermediateResultData);
    }

    /**
     * Help search and explode string
     *
     * @param string $string
     * @return false|string[]
     */
    private function explodeDiagnoseAndDetail(string $string) {
        $dataDiagnosis = array(
            "Atemwege",
            "Muskel/Skelett",
            "Verdauung",
            "Verletzungen",
            "Herz Kreislauf",
            "Psychische Störungen",
            "Infektionen",
            "Neubildungen",
            "Stoffwechsel",
            "Nerven und Sinne",
            "Haut",
            "Urogenitalsystem",
        );

        foreach ($dataDiagnosis as $diagnosis) {
            if (stristr($string, $diagnosis) !== false) {
                return array('explode' => explode($diagnosis, $string), 'diagnosis' => $diagnosis);
            }
        }

        return false;
    }

    /**
     * Formatting for the diagnosis model. Fetching the last line in each diagnosis
     * @param array $diagnoses
     * @return array
     */
    private function formattingDiagnosisModel(array $diagnoses): array
    {
        $formattingData = array();

        foreach ($diagnoses as $key => $diagnosis) {
            $lastRow = last($diagnosis);
            foreach ($lastRow as $k=>$v) {
                $formattingData[] = $v;
            }
        }

        return $formattingData;
    }

}
