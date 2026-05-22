<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class BackofficeFile
{
    private const ORIGINAL_NAME_SEPARATOR = '__';

    public static function storeWithOriginalName(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $originalName = self::sanitizeOriginalName($file->getClientOriginalName());
        $storedName = Str::random(24) . self::ORIGINAL_NAME_SEPARATOR . $originalName;

        return $file->storeAs($directory, $storedName, $disk);
    }

    public static function displayName(?string $path): string
    {
        $filename = basename((string) $path);
        if ($filename === '') {
            return '';
        }

        if (str_contains($filename, self::ORIGINAL_NAME_SEPARATOR)) {
            return Str::after($filename, self::ORIGINAL_NAME_SEPARATOR);
        }

        return self::looksLikeHashedStorageName($filename) ? '등록된 파일' : $filename;
    }

    private static function sanitizeOriginalName(string $name): string
    {
        $name = basename($name);
        $name = preg_replace('/[\\\\\\/\\x00-\\x1F\\x7F]+/u', '_', $name) ?: 'file';
        $name = trim($name);

        return $name !== '' ? $name : 'file';
    }

    private static function looksLikeHashedStorageName(string $filename): bool
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);

        return strlen($name) >= 32 && preg_match('/^[A-Za-z0-9]+$/', $name) === 1;
    }
}
