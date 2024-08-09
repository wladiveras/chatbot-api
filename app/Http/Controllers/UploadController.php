<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Validator;

class UploadController extends BaseController
{
    public function upload(Request $request)
    {
        // Validar os dados da solicitação
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mp3|max:20480',
        ]);

        // Verificar se a validação falhou
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Verificar se o arquivo foi carregado corretamente
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return response()->json(['error' => 'Invalid file upload.'], 400);
        }

        // Armazenar o arquivo no S3
        $path = $request->file('file')->store('uploads', 's3');

        // Verificar se o caminho do arquivo foi gerado corretamente
        if (!$path) {
            return response()->json(['error' => 'Failed to store file.'], 500);
        }

        // Retornar a URL do arquivo armazenado
        return response()->json([
            'path' => $path,
            'url' => Storage::disk('s3')->url($path),
        ]);
    }
}
