<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Str;


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

        // Obter o arquivo
        $file = $request->file('file');

        // Gerar um UUID para o nome do arquivo
        $uuid = Str::uuid()->toString();

        // Definir o caminho no S3 com o UUID
        $path = 'uploads/' . $uuid . '_' . $file->getClientOriginalName();

        // Armazenar o arquivo no S3 com permissões públicas
        $stored = Storage::disk('s3')->put($path, file_get_contents($file), 'public');

        // Verificar se o arquivo foi armazenado corretamente
        if (!$stored) {
            return response()->json(['error' => 'Failed to store file.'], 500);
        }

        // Retornar a URL do arquivo armazenado
        return response()->json([
            'path' => $path,
            'url' => Storage::disk('s3')->url($path),
        ]);
    }
}
