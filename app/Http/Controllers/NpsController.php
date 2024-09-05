<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Nps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Routing\Controller;
use App\Http\Middleware\CheckUserCookie;

class NpsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware(CheckUserCookie::class)->except('finish');
    }


    public function finish()
    {
        return view('nps.finish');
    }

    public function index()
    {

        $entity = session('entity_token');

        if (!$entity):
            abort(403, 'Acesso não autorizado.');
        endif;

        $entity = Entity::where('token', $entity)->first();
        $nps = Nps::orwhere('range', 'default')
            ->orwhere('range', 'minimal')
            ->orwhere('range', 'emoji')
            ->where('entity_id', $entity->id)
            ->where('visibility', 'default')->get();

        if($nps->isEmpty()):
            abort(404, 'Não há resultados para a página solicitada!');
        endif;

        return view('nps.index', [
            "nps" => $nps,
            "page" => "question"
        ]);
    }

    public function why()
    {
        return view('nps.index', [
            "nps" => Nps::orwhere('range', 'why')
                ->where('visibility', 'default')->get(),
            "page" => "why",
        ]);
    }

    public function justify()
    {
        return view('nps.index', [
            "nps" => Nps::orwhere('range', 'text')
                ->where('visibility', 'default')->get(),
            "page" => "justify",
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Nps $nps)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nps $nps)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nps $nps)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nps $nps)
    {
        //
    }
}
