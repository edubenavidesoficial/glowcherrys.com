<?php

namespace App\Http\Controllers;

use App\Http\Requests\SliderRequest;
use App\Models\Gift;
use Illuminate\Http\Request;

class GiftController extends Controller
{
     public function index()
    {
        $gifts = Gift::active()->ordered()->get();
         return response()->json([
            'success' => true,
            'data' => $gifts
        ]);
    }

    public function store(SliderRequest $request)
    {
        $data = $request->validate();
        Gift::create($data);

return response()->json([
            'message' => "GIF registrado satisfactoriamente",
        ]);    }

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
