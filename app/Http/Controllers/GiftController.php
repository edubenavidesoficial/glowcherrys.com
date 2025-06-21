<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiftRequest;
use App\Http\Requests\GuardarImagenIndividual;
use App\Http\Requests\RutasStorage;
use App\Http\Requests\Utils;
use App\Http\Resources\GiftResource;
use App\Models\Gift;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Razorpay\Api\Resource;

class GiftController extends Controller
{
    public function index()
    {
        $gifts_data = Gift::active()->get();
        $results =  GiftResource::collection($gifts_data);
        return response()->json(compact('results'));
    }
    public function show(Gift $gift)
    {
        $modelo = new GiftResource($gift);
        return response()->json(compact('modelo'));
    }
    public function store(GiftRequest $request)
    {

        try {
            DB::beginTransaction();
            $gift = Gift::create($request->validated());
            DB::commit();
            //$mensaje = Utils::obtenerMensaje($this->entidad, 'store');
            \Session::flash('success_message', trans('admin.success_add'));

            return redirect('panel/admin/settings/gift');
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
            $gift = Gift::find($id);
            $gift->delete();
            DB::commit();
            \Session::flash('success_message', trans('admin.success_delete'));
            return redirect('panel/admin/settings/gift');
        } catch (Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'Error al eliminar' => [$e->getMessage()],
            ]);
        }
    }
    public function update(Gift $gift, GiftRequest $request)
    {
        try {
            DB::beginTransaction();
            $datos = $request->validated();
            $gift->update($datos);
            $modelo = new GiftResource($gift->refresh());
            DB::commit();
            return response()->json([
                'modelo' => $modelo,
                'message' => 'Gift actualizado correctamente.'
            ], 200); // Respuesta exitosa*/
        } catch (Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'Error al actualizar' => [$e->getMessage()],
            ]);
        }
    }
    public function send(Gift $gift)
    {
        // Lógica para enviar el regalo
        // Verificar balance del usuario, etc.

        return response()->json([
            'success' => true,
            'message' => 'Regalo enviado correctamente',
            'diamonds_sent' => $gift->diamonds
        ]);
    }
}
