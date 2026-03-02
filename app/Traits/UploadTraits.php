<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Interfaces\EncodedImageInterface;

trait UploadTraits
{
    /**
     * Upload the file with slugging to a given path
     *
     * @param UploadedFile $image
     * @param $path
     * @return string
     */
    public function uploadFile(UploadedFile $image, $subFolder = null, String $diskName = 'public', $returnFileName = false)
    {
        if (!$image) {
            return null;
        }

        $extension = $image->getClientOriginalExtension();

        // Ambil nama file asli tanpa ekstensi
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

        // Bersihkan nama file dari simbol, hanya huruf, angka, spasi, underscore
        $sanitizedName = preg_replace('/[^A-Za-z0-9 _-]/', '', $originalName);

        // Ganti spasi dengan underscore biar konsisten
        $sanitizedName = str_replace(' ', '_', $sanitizedName);

        // Tambahkan tanggal sekarang ke nama
        $date = Carbon::now()->format('Ymd_His');

        // Buat nama file akhir
        $fileName = $sanitizedName . '_' . $date . '.' . $extension;

        // Tentukan folder simpan
        $filePath = $subFolder
            ? Carbon::now()->format('Y/m/d/') . $subFolder . '/'
            : Carbon::now()->format('Y/m/d/');

        if ($returnFileName) {
            return $fileName;
        }

        // Simpan file dengan nama baru
        Storage::disk($diskName)->putFileAs($filePath, $image, $fileName);

        return $filePath . $fileName;
    }

    /**
     * Handling delete file.
     */
    public function deleteFile(?string $path)
    {
        try {
            if ($path != null) {
                $pathReplace = str_replace('storage/', '', parse_url($path, PHP_URL_PATH));
                if (Storage::disk('public')->exists($pathReplace)) {
                    Storage::disk('public')->delete($pathReplace);
                    // info('File deleted: ' . $pathReplace);
                } else {
                    // info('File not found: ' . $pathReplace);
                }
                return null;
            } else {
                return null;
            }
        } catch (\Throwable $th) {
            info($th);

            return null;
        }
    }
}
