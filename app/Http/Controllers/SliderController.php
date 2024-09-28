<?php

namespace App\Http\Controllers;

use App\Http\Requests\SliderRequest;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            \Session::flash('success_message',trans('admin.success_add'));

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
    public function update(SliderRequest $request, slider $slider)
    {
        try {
            DB::beginTransaction();
            $slider->update($request->validated());
            $modelo = new SliderResource($slider->refresh());
            DB::commit();
            // $mensaje = Utils::obtenerMensaje($this->entidad, 'update');
            return response()->json(compact('modelo'));
        } catch (Exception $e) {
            DB::rollBack();
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
        //
    }
}
