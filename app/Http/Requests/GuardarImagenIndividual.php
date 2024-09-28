<?php
namespace App\Http\Requests;

use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;
class GuardarImagenIndividual
{
    private String $imagen_base64;
    private RutasStorage $ruta;
    private $nombre_predeterminado;

    public function __construct(string $imagen_base64, RutasStorage $ruta, string $nombre_predeterminado = null)
    {
        $this->imagen_base64 = $imagen_base64;
        $this->ruta = $ruta;
        $this->nombre_predeterminado = $nombre_predeterminado;
    }

    public function execute()
    {
        try {
            //code...
            $imagen_decodificada = Utils::decodificarImagen($this->imagen_base64);
            $extension = Utils::obtenerExtension($this->imagen_base64);

            $directorio = $this->ruta->value;
            $nombre_archivo = $this->nombre_predeterminado ? $this->nombre_predeterminado . '.' . $extension : Utils::generarNombreArchivoAleatorio($extension);
            $ruta_relativa = Utils::obtenerRutaRelativaImagen($directorio, $nombre_archivo);
            $ruta_absoluta = Utils::obtenerRutaAbsolutaImagen($directorio, $nombre_archivo);

            Image::make($imagen_decodificada)->resize(1800, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($ruta_absoluta);

            return $ruta_relativa;
        } catch (\Throwable $th) {
            Log::info($th->getMessage());

            return null;
        }
    }
}
