<?php

namespace App\Http\Requests;
use Illuminate\Support\Str;

class Utils
{
    public static function esBase64(string $imagen): bool
    {
        return str_contains($imagen, ';base64');
    }

    public static function decodificarImagen(string $imagen_base64): string
    {
        $partes = explode(";base64,", $imagen_base64);
        return base64_decode($partes[1]);
    }

    public static function obtenerMimeType(string $imagen_base64): string
    {
        return explode("/", mime_content_type($imagen_base64))[1];
    }

    public static function obtenerExtension(string $imagen_base64): string
    {
        $mime_type = self::obtenerMimeType($imagen_base64);
        return explode("+", $mime_type)[0];
    }
    public static function generarNombreArchivoAleatorio(string $extension): string
    {
        $nombre = Str::random(10);
        return $nombre . '.' . $extension;
    }
    public static function obtenerRutaRelativaImagen(string $ruta, string $nombre_archivo = ""): string
    {
        $ruta = str_replace('public/', '', $ruta);
        return '/storage/' . $ruta . '/' . $nombre_archivo;
    }
    public static function obtenerRutaAbsolutaImagen(string $ruta_imagen_en_public, string $nombre_archivo): string
    {
        return storage_path() . '/app/' . $ruta_imagen_en_public . '/' . $nombre_archivo; // aqui cambie
    }
}
