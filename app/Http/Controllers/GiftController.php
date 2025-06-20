<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiftRequest;
use App\Http\Requests\SliderRequest;
use App\Http\Resources\GiftResource;
use App\Models\Gift;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Razorpay\Api\Resource;

class GiftController extends Controller
{
    public function index()
    {
        $gifts_data = Gift::active()->ordered()->get();
        $results =  GiftResource::collection($gifts_data );
        return response()->json(compact('results'));

    }

    public function store(GiftRequest $request)
    {

try {
            DB::beginTransaction();
            $slider = Gift::create($request->validated());
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
