<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class SliderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_slider' => 'required|string|max:255',
            'description_slider' => 'required|string',
            'image_slider' => 'required|string',
            'estado' => 'required|string',
            'link_1' => 'nullable|string|max:255',
            'link_2' => 'nullable|string|max:255',
        ];
    }
    protected function prepareForValidation() {
        $this->merge(self::convertirComprobantesBase64Url($this->all(),$this->route()->getActionMethod()));
    }
    public static function  convertirComprobantesBase64Url(array $datos, $tipo_metodo = 'store')
    {
        try {
            switch ($tipo_metodo) {
                case 'store':
                    if ($datos['image_slider']) {
                        $datos['image_slider'] = (new GuardarImagenIndividual($datos['image_slider'], RutasStorage::SLIDER))->execute();
                    }
                    break;
                case 'update':
                    if ($datos['image_slider'] && Utils::esBase64($datos['image_slider'])) {
                        $datos['image_slider'] = (new GuardarImagenIndividual($datos['image_slider'], RutasStorage::SLIDER))->execute();
                    } else {
                        unset($datos['image_slider']);
                    }
                    break;
            }
        } catch (\Throwable $th) {
            Log::error('Error al convertir comprobantes base64: '. $th->getMessage());
        }
        return $datos;
    }
}



