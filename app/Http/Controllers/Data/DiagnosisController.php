<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use \Exception;

class DiagnosisController extends Controller
{
    /**
     *  Get data from diagnoses
     */
    public function getDiagramsData(): array
    {
        $data = DB::table('diagnoses')->get()->all();

        $diagramsData = array();

        try {
            foreach ($data as $key => $diagnosis) {
                if ($diagnosis->value > 5) {
                    $diagramsData[$diagnosis->region][$diagnosis->year][$diagnosis->diagnosis][] = $diagnosis;
                }
            }
        } catch (Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }

        return array('status' => 'success', 'result' => $diagramsData);
    }
}
