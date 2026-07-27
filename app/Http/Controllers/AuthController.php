<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function unauthorized() {
        return response()->json([
            'error' => 'Não autorizado'
        ], 401);
    }

    public function register(Request $request) {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|digits:11|unique:users,cpf',
            'password' => 'required|confirmed',
        ]);

        if(!$validator->fails()){
            $name = $request->input('name');
            $email = $request->input('email');
            $cpf = $request->input('cpf');
            $password = $request->input('password');

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $newUser = new User();
            $newUser->name = $name;
            $newUser->email = $email;
            $newUser->cpf = $cpf;
            $newUser->password = $hash;
            $newUser->save();

            $token = Auth::attempt([
                'cpf' => $cpf,
                'password' => $password
            ]);

            if(!$token){
                $array['error'] = 'Ocorreu um erro.';
                return $array;
            }

            $array['token'] = $token;

            $user = Auth::user();
            $array['user'] = 'Sem usuario';
            if($user){
                $array['user'] = $user;
                $properties = Unit::select(['id', 'name'])->where('id_owner', $user->id)->get(); 
            }
                $array['user']['properties'] = $properties;
        }else{
            $array['error'] = $validator->errors()->first();
        }

        return $array;
    }

    public function login(Request $request) {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'cpf' => 'required|digits:11',
            'password' => 'required'
        ]);

        if(!$validator->fails()){
            $cpf = $request->input('cpf');
            $password = $request->input('password');
            
            $token = Auth::attempt([
                'cpf' => $cpf,
                'password' => $password
            ]);

            if(!$token){
                $array['error'] = 'CPF e/ou Senha estão errados.';
                return $array;
            }

            $array['token'] = $token;

            $user = Auth::user();
            $array['user'] = 'Sem usuario';
                if($user){
                    $array['user'] = $user;
                    $properties = Unit::select(['id', 'name'])->where('id_owner', $user->id)->get(); 
                }
                $array['user']['properties'] = $properties;
            }else{
                $array['error'] = $validator->errors()->first();
            }
        return $array;
    }

    public function validateToken() {
        $array = ['error' => ''];

        $user = Auth::user();
        $array['user'] = 'Sem usuario';
        if($user){ 
            $array['user'] = $user;
            $properties = Unit::select(['id', 'name'])->where('id_owner', $user->id)->get(); 
        }
            $array['user']['properties'] = $properties;

        return $array;
    }

    public function logout() {
        try{
            Auth::logout();
        }catch(\Exception $e){
            return['error' => $e->getMessage()];
        }
    }
}
