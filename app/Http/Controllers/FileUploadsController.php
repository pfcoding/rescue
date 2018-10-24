<?php

namespace App\Http\Controllers;

use App\FileUpload;
use App\Pokemon;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Storage;

class FileUploadsController extends Controller
{
    public function uploadFile(Request $request)
    {
        $file     = Input::file('file');
        $filename = $file->getClientOriginalName();

        $path = hash('sha256', time());

        $pathFilename = $path . '/' . $filename;

        try {

            Storage::disk('uploads')->put($pathFilename, File::get($file));

            $input['filename'] = $filename;
            $input['mime']     = $file->getClientMimeType();
            $input['path']     = $path;
            $input['size']     = $file->getClientSize();

            $file = FileUpload::create($input);

            // import json file data
            if ($this->import($pathFilename)) {
                return response()->json([
                    'success' => true,
                    'id' => $file->id
                ], 200);
            }

        } catch (\Throwable $t) {
            return response()->json([
                'success' => false,
                'msg' => $t->getMessage()
            ], 500);
        }

    }

    private function import($pathFilename)
    {
        try {
            $data = json_decode(Storage::disk('uploads')->get($pathFilename));

            if ($data === false || is_null($data)) {
                throw new Exception('Encoding error');
            }

            foreach ($data as $datum) {
                $keys = get_object_vars($datum);
                if (count($keys) == 2 && array_key_exists('name', $keys) && array_key_exists('types', $keys)) {
                    Pokemon::create([
                        'name' => ucfirst($datum->name),
                        'types' => $datum->types
                    ]);
                } else {
                    throw new Exception('Encoding error');
                }
            }

            return true;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function index()
    {
        $files = FileUpload::all();

        return view('files.index', compact('files'));
    }

    public function create()
    {
        return view('files.create');
    }
}
