<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Industries;
use App\Models\Regions;
use Illuminate\Http\Request;
use App\Http\Controllers\Formatters\FormatterDashboardToDb as DashboardFormatter;
use App\Http\Controllers\Formatters\FormatterDiagnosesToDb as DiagnosesFormatter;
use App\Http\Controllers\Formatters\FormatterIndustries as IndustriesFormatter;
use App\Http\Controllers\Formatters\FormatterRegions as RegionsFormatter;
use App\Models\Statistics;
use App\Models\Diagnosis;


class ImportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    /**
     * Import main statistics excel file to db
     *
     * @param Request $request
     * @return array
     */
    public function importStatistics(Request $request): array
    {

        $request->validate([
            'file' => 'required|string'
        ]);

        $formatterData = $this->callFormatter(new DashboardFormatter(), $request->file);

        if (!empty($formatterData['status']) && $formatterData['status'] == 'error') {
            return $formatterData;
        }

        Statistics::truncate();

        try {
            foreach ($formatterData['result'] as $key => $data) {
                Statistics::create($data);
            }
        } catch (\Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'message' => 'Import is done.');
    }

    /**
     * Import diagnoses excel file to db
     *
     * @param Request $request
     * @return array|string[]
     */
    public function importDiagnoses(Request $request): array
    {

        $request->validate([
            'file' => 'required|string'
        ]);

        $formatterData = $this->callFormatter(new DiagnosesFormatter(), $request->file);

        if (!empty($formatterData['status']) && $formatterData['status'] == 'error') {
            return $formatterData;
        }

        Diagnosis::truncate();

        try {
            foreach ($formatterData['result'] as $key => $data) {
                Diagnosis::create($data);
            }
        } catch (\Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'message' => 'Import is done.');
    }

    /**
     * Import industries excel file to db
     *
     * @param Request $request
     * @return array|string[]
     */
    public function importIndustries(Request $request): array
    {

        $request->validate([
            'file' => 'required|string'
        ]);

        $formatterData = $this->callFormatter(new IndustriesFormatter(), $request->file);

        if (!empty($formatterData['status']) && $formatterData['status'] == 'error') {
            return $formatterData;
        }

        Industries::truncate();

        try {
            foreach ($formatterData['result'] as $key => $data) {
                Industries::create($data);
            }
        } catch (\Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'message' => 'Import is done.');
    }

    /**
     * Import regions to map
     *
     * @param Request $request
     * @return array|string[]
     */
    public function importRegionsToMap(Request $request): array
    {

//        $request->validate([
//            'file' => 'required|string'
//        ]);

        $formatterData = $this->callFormatter(new RegionsFormatter());

        if (!empty($formatterData['status']) && $formatterData['status'] == 'error') {
            return $formatterData;
        }

        Regions::truncate();

        try {
            foreach ($formatterData['result'] as $key => $data) {
                Regions::create($data);
            }
        } catch (\Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'message' => 'Import is done.');
    }

    /**
     * Call formatter class
     *
     * @param $formatter
     * @param $file
     * @return mixed
     */
    private function callFormatter($formatter, $file = null)
    {
        return $formatter->formatterData($file);
    }

}
