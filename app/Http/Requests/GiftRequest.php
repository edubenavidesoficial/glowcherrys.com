<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class GiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ajustar según sistema de permisos
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $methodName = $this->route()->getActionMethod(); // Obtiene 'store' o 'update'
        $rules = [
            'name' => 'required|string|max:255|unique:gifts,name',
            'price' => 'required|numeric|min:0|max:9999.99',
            'diamonds' => 'required|integer|min:1|max:1000',
            'is_active' => 'sometimes|boolean',
            'image_path' => 'required|string' // Acepta base64 o URL existente
        ];

        if ($this->isMethod('patch') || $this->isMethod('put') || $methodName === 'update') {
            $rules['name'] = 'required|string|max:255';
            $rules['image'] = 'sometimes|string';
        }

        return $rules;
    }

    /**
     * Prepara los datos antes de la validación
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'image_path' => $this->procesarImagen($this->image ?? null, $this->route()->getActionMethod())
        ]);
    }

    /**
     * Procesa la imagen y devuelve el path para guardar
     */
    protected function procesarImagen(?string $imagen, string $metodo): ?string
    {
        try {
            // Si no hay imagen o no es base64 (y es update), mantener la existente
            if (empty($imagen)) {
                return null;
            }

            // Si es update y la imagen no es base64, asumir que es la URL existente
            if ($metodo === 'update' && !Utils::esBase64($imagen)) {
                return $this->route('gift')->image_path;
            }

            // Guardar nueva imagen (para create o update con nueva imagen)
            return (new GuardarImagenIndividual($imagen, RutasStorage::GIFTS))->execute();
        } catch (\Throwable $th) {
            Log::error('Error al procesar imagen: ' . $th->getMessage());
            throw new \Exception('Error al procesar la imagen del regalo');
        }
    }

    /**
     * Mensajes de validación personalizados
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del regalo es obligatorio',
            'name.unique' => 'Ya existe un regalo con este nombre',
            'price.required' => 'El precio es obligatorio',
            'price.min' => 'El precio no puede ser negativo',
            'diamonds.required' => 'La cantidad de diamantes es obligatoria',
            'diamonds.min' => 'Debe otorgar al menos 1 diamante',
            'image.required' => 'La imagen del regalo es obligatoria',
        ];
    }
}
