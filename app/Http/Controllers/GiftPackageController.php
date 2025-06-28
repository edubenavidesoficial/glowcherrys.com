<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiftPackageRequest;
use App\Http\Resources\GiftPackageResource;
use App\Models\GiftPackage;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GiftPackageController extends Controller
{
     public function index()
    {
        $giftpackages_data = GiftPackage::active()->get();
        $results =  GiftPackageResource::collection($giftpackages_data);
        return response()->json(compact('results'));
    }
    public function show(GiftPackage $gifts_package)
    {
        $modelo = new GiftPackageResource($gifts_package);
        return response()->json(compact('modelo'));
    }
    public function store(GiftPackageRequest $request)
    {

        try {
            DB::beginTransaction();
            $giftpackage = GiftPackage::create($request->validated());
            DB::commit();
            //$mensaje = Utils::obtenerMensaje($this->entidad, 'store');
            \Session::flash('success_message', trans('admin.success_add'));

            return redirect('panel/admin/settings/diamonts');
        } catch (Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'Error al insertar' => [$e->getMessage()],
            ]);
        }
    }
 public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $giftpackage = GiftPackage::find($id);
            $giftpackage->delete();
            DB::commit();
            \Session::flash('success_message', trans('admin.success_delete'));
            return redirect('panel/admin/settings/diamonts');
        } catch (Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'Error al eliminar' => [$e->getMessage()],
            ]);
        }
    }
    public function update(GiftPackage $gifts_package, GiftPackageRequest $request)
    {
        try {
            DB::beginTransaction();
            $datos = $request->validated();
            $gifts_package->update($datos);
            $modelo = new GiftPackageResource($gifts_package->refresh());
            DB::commit();
            return response()->json([
                'modelo' => $modelo,
                'message' => 'GiftPackage actualizado correctamente.'
            ], 200); // Respuesta exitosa*/
        } catch (Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'Error al actualizar' => [$e->getMessage()],
            ]);
        }
    }

}
