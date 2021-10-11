<?php

namespace App\Http\Controllers\Formatters;

use App\Interfaces\FormatterInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FormatterIndustries extends Controller implements FormatterInterface
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    /**
     * @param string|null $file
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
     * @param array $importData
     * @return array
     */
    private function _formatterData(array $importData): array
    {

        $resultDataFormatter = array();
        $importSql = array();
        $i = 0;
        foreach ($importData as $key => $row) {
            if  (!empty($row[0]) && empty($row[1])) {
                $importSql['industryGroup'] = trim($row[2]);
            }
            else if (empty($row[0]) && !empty($row[1])){
                $importSql['industryName'] = trim($row[2]);
                $i++;
                $resultDataFormatter[] = $importSql;
                unset($importSql['industryName']);
            }
        }
        return $resultDataFormatter;
    }
}
