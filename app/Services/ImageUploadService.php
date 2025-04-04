<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Used for syntax sugar/
     *
     * @return \App\Services\ImageUploadService
     */
    public static function resolve()
    {
        return resolve(self::class);
    }

    public function handle($file, Product $product): string
    {
        // $filename = $file->getClientOriginalExtension();  <== this user input value is not safe!
        $safeName = $this->getSafeFilename($product, $file);
        $file->move(public_path('uploads'), $safeName);

        return 'uploads/'.$safeName;
    }

    /**
     * @see https://securinglaravel.com/laravel-security-file-upload-vulnerability/
     */
    private function getSafeFilename(Product $product, UploadedFile $file): string
    {
        return Str::limit(md5($product->getKey()), 20, '').'.'.$file->extension();
    }
}
