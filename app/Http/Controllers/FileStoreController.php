<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\ContentRangeUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Str;
use function dd;

class FileStoreController extends Controller
{
    public function __invoke(Request $request)
    {
//        throw new UploadMissingFileException;

        $receiver = new FileReceiver(
            UploadedFile::fake()->createWithContent('file', $request->getContent()),
            $request,
            ContentRangeUploadHandler::class
        );

        if($receiver->isUploaded() === false) {
            throw new UploadMissingFileException;
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $this->storeFile($request, $save->getFile());
        }

        $save->handler();
    }

    private function storeFile(Request $request, UploadedFile $file)
    {
        $extension = $file->guessExtension();

        $request->user()->files()->create([
           'file_path' => $file->storeAs('files', Str::uuid() . '.' . $extension, 'public'),
        ]);
    }
}
