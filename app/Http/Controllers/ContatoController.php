<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Controllers\Controller;


class ContatoController extends Controller
{
    public function config(){
        $user = User::first();

        return response()->json([
            'nome' => $user->name,
            'email' => $user->email,
            'telefone' => $user->telefone,
            'endereco' => $user->endereco,
        ]);

    }
}
