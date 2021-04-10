<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Film, Shift};
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\FilmResource;
use App\Http\Requests\FilmRequest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function store(FilmRequest $request ,Film $film)
    {

        if($request->file == null){
            $film = Film::create([
                'name' => $request->name,
                'publication_date' => $request->publication_date,
            ]);
            return response()->json([
                'message'=>'Pelicula agregado',
                'film' => $film,
                'errors'=> ''
            ], 200);
        }else{
            if (request()->hasFile('file')){
    
                $file = request()->file('file');
                $new_film = $film->create([
                    'name' => $request->name,
                    'publication_date' => $request->publication_date,
                    'cover' => 'temp'
                ]);
                $id_foto = $new_film->fresh()->id;
                $name_file = str_replace(' ', '_', $file->getClientOriginalName());
                $name_file =  preg_replace("/[^a-zA-Z0-9.]/", "", $name_file);

                $ubicacion_donde_guarda ='public/covers/'.$name_file;
                $ubicacion_donde_guarda2 = 'covers/'.$name_file;
                \Storage::disk('local')->put($ubicacion_donde_guarda,  \File::get($file));
                $new_film->cover = $ubicacion_donde_guarda2;

                $new_film->save();
    
                return response()->json([
                    'message'=>'Pelicula agregado',
                    'film' => $film,
                    'errors'=> ''
                ], 200);
    
            }
        }
        
    }

    public function show()
    {

        $perPage = request()->has('per_page') ? (int) request()->per_page : null;
        $order = (request()->has('sort') && request()->sort != null) ? explode("|", request()->sort) : null;
        $nombre = $order != null ? $order[0] : 'created_at';
        $orderBy = $order != null ? $order[1] : 'DESC';

        $film = Film::with('shifts')->orderBy($nombre, $orderBy)->paginate($perPage);

        return FilmResource::collection($film);

    }

    public function ver(Film $film)
    {
        
        return view('films.show')
            ->with('film', $film);

    }

    public function update(Film $film)
    {
        dd(request()->name);
        if(request()->file == null){
            $film = $film->update([
                'name' => request()->name,
                'publication_date' => request()->publication_date,
                'status' => request()->status
            ]);
            return response()->json([
                'message'=>'Se Actualizo correctamente',
                'errors'=> '',
                'film' => $film
            ], 200);
        }else{
            if (request()->hasFile('file')){
    
                $file = request()->file('file');
                $new_film = $film->fill([
                    'name' => request()->name,
                    'publication_date' => request()->publication_date,
                    'status' => request()->status,
                    'cover' => 'temp'
                ]);
                $id_foto = $new_film->fresh()->id;
                $name_file = str_replace(' ', '_', $file->getClientOriginalName());
                $name_file =  preg_replace("/[^a-zA-Z0-9.]/", "", $name_file);

                $ubicacion_donde_guarda ='public/covers/'.$name_file;
                $ubicacion_donde_guarda2 = 'covers/'.$name_file;
                \Storage::disk('local')->put($ubicacion_donde_guarda,  \File::get($file));
                $new_film->cover = $ubicacion_donde_guarda2;

                $new_film->save();
    
                return response()->json([
                    'message'=>'Se Actualizo correctamente',
                    'errors'=> '',
                    'film' => $film
                ], 200);
    
            }
        }
    }

    public function destroy(Film $film)
    {
        
        $film->delete();
        return response()->json([
            'films' => $film,
            'errors'=> ''
        ], 200);

    }

}
