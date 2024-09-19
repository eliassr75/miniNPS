<?php

namespace App\Http\Controllers;

use App\Models\ClientData;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        //

        $users = [];
        if(Auth::user()->entity->id === 1){
            $users = User::orderBy('id', 'desc')->paginate(10);
        }else{
            $users = User::where('entity_id', Auth::user()->entity->id)
                ->orderBy('id', 'desc')->paginate(10);
        }

        return view('users.index', [
            'users' => $users,
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

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $level = $request->input('level');
        $phone = $request->input('phone');
        $entity_id = Auth::user()->entity->id;

        $exists = User::where('email', $email)->first();
        $msg = 'Cadastro realizado com sucesso!';
        if($exists){
            $msg = 'E-mail informado já possui cadastro!';
        }else{
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'level' => $level,
                'entity_id' => $entity_id,
            ]);

            if (!empty($phone)) {
                $user->client_data()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['phone' => $phone]
                );
            }
        }
        return redirect()->route('users.index')->with('msg', $msg);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $level = $request->input('level');
        $phone = $request->input('phone');

        $msg = 'Cadastro atualizado com sucesso!';
        $user->update([
            'name' => $name,
            'email' => $email,
            'password' => $password ? Hash::make($password) : $user->password,
            'level' => $level,
        ]);

        try{
            if (!empty($phone)) {
                ClientData::updateOrCreate(
                    ['user_id' => $user->id],
                    ['phone' => $phone]
                );
            }
        }catch (\Exception $e){
            $msg = $e->getMessage();
        }

        return redirect()->route('users.index')->with('msg', $msg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
