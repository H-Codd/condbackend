<?php

namespace App\Http\Controllers;

use App\Models\Billet;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BilletController extends Controller
{
    public function getAll(Request $request) {
        $array = ['error' => ''];
        $property = $request->ipnut('property');
        if($property){

            $user = Auth::user();     
            $unit = Unit::where('id', $property)->where('id_owner', $user->id)->count();
            
            if($unit > 0){
            
            $billets = Billet::where('id_unit', $property)->get();
            
            foreach($billets as $billetKey => $billetValue){
                $billets[$billetKey]['fileurl'] = asset('stoage/'.$billetValue['fileurl']);
                }
                
                $array['list'] = $billets;
            }else{
                $array['error'] = 'Esta unidade não é sua.';
            }

        } else{
            $array['error'] = 'A propriedate é necessaria.';
        }
        return $array;
    }
}
