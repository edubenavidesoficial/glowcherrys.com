<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarImagenIndividual;
use App\Http\Requests\RutasStorage;
use App\Http\Requests\SliderRequest;
use App\Http\Requests\Utils;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Image;


class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct() {}
    public function index()
    {
        $results = [];
        $results = Slider::get();
        $results = SliderResource::collection($results);
        return response()->json(compact('results'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SliderRequest $request)
    {
        try {
            DB::beginTransaction();
            $slider = Slider::create($request->validated());
            DB::commit();
            //$mensaje = Utils::obtenerMensaje($this->entidad, 'store');
            \Session::flash('success_message', trans('admin.success_add'));

            return redirect('panel/admin/settings/slider');
        } catch (Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'Error al insertar' => [$e->getMessage()],
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Slider $slider)
    {
        $modelo = new SliderResource($slider);
        return response()->json(compact('modelo'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Slider $slider, Request $request)
    {
        try {
            DB::beginTransaction();
            $datos = $request->all();
            if ($datos['image_slider'] && Utils::esBase64($datos['image_slider'])) {
                $datos['image_slider'] = (new GuardarImagenIndividual($datos['image_slider'], RutasStorage::SLIDER))->execute();
            } else {
                unset($datos['image_slider']);
            }
                      // Actualiza el slider con los datos validados
            $slider->update($datos);
            // Puedes devolver un recurso o los datos actualizados como prefieras
            $modelo = new SliderResource($slider->refresh());
            DB::commit();

            return response()->json([
                'modelo' => $modelo,
                'message' => 'Slider actualizado correctamente.'
            ], 200); // Respuesta exitosa*/
        } catch (Exception $e) {
            DB::rollBack();

            // Retorna un error de validación con el mensaje de excepción
            throw ValidationException::withMessages([
                'Error al actualizar' => [$e->getMessage()],
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $slider = Slider::find($id);
            $slider->delete();
            DB::commit();
            \Session::flash('success_message', trans('admin.success_delete'));
            return redirect('panel/admin/settings/slider');
        } catch (Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'Error al eliminar' => [$e->getMessage()],
            ]);
        }

    }
}
