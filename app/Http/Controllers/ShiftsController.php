<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\{Film,Shift};
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ShiftResource;
use App\Http\Requests\ShiftRequest;

class ShiftsController extends Controller
{

    public function store(ShiftRequest $request, Shift $shift)
    {

        $shift = Shift::create([
            'shift' => request()->shift,
            'film_id' => request()->film
        ]);
 
        return response()->json([
            'message'=>'Turno agregado',
            'turno' => $shift,
            'errors'=> ''
        ], 200);

    }

    public function show(Film $film)
    {

        $perPage = request()->has('per_page') ? (int) request()->per_page : null;
        $order = (request()->has('sort') && request()->sort != null) ? explode("|", request()->sort) : null;
        $asunto = $order != null ? $order[0] : 'created_at';
        $orderBy = $order != null ? $order[1] : 'DESC';

        $shift = $film->shifts;

        return ShiftResource::collection($shift);

    }

    public function update(ShiftRequest $request, Shift $shift)
    {
        $shift->update([
            'shift' => request()->shift,
            'film_id' => request()->film,
            'status' => request()->status,
        ]);

        return response()->json([
            'message'=>'Se Actualizo corretamente',
            'errors'=> '',
            'turnos' => $shift
        ], 200);

    }

    public function destroy(Shift $shift)
    {
        
        $shift->delete();
        return response()->json([
            'turnos' => $shift,
            'errors'=> ''
        ], 200);

    }

}
