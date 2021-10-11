<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Files;

class FilesUploadController extends BaseController
{

    /**
     * @param Request $request
     * @return array|string[]
     */
    public function upload(Request $request): array
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:10240'
        ]);

        $fileModel = new Files;

        try {
            if($request->file()) {
                $fileName = time().'_'.$request->file->getClientOriginalName();
                $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

                $fileModel->name = time().'_'.$request->file->getClientOriginalName();
                $fileModel->file_path = '/storage/' . $filePath;
                $fileModel->save();

                return array('status' => 'success', 'message' => 'File has been uploaded.', 'file' => $fileName);
            }
        }
        catch (\Exception $e) {
            return array('status' => 'error', 'message' => $e->getMessage());
        }
    }

}
