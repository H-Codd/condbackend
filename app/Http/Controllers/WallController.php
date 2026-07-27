<?php

namespace App\Http\Controllers;

use App\Models\Wall;
use App\Models\Walllikes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WallController extends Controller
{
    public function getAll() {
        $array = ['error'=> '', 'list' => []];

        $user = Auth::user();

        $walls = Wall::all();

        foreach($walls as $wallkey => $wallValue){
            $walls[$wallkey]['likes'] = 0;
            $walls[$wallkey]['liked'] = false;

            $likes = Walllikes::where('id_wall', $wallValue['id'])->count();
            $walls[$wallkey]['likes'] = $likes;

            $meLikes = Walllikes::where('id_wall', $wallValue['id'])->where('id_user', $user->id)->count();

            if($meLikes > 0) {
                $walls[$wallkey]['liked'] = true;
            }
        }
        $array['list'] = $walls;

        return $array;
    }

    public function toggleLike(int $id) {
        $array = ['error'=> ''];

        $user = Auth::user();

        $meLikes= Walllikes::where('id_wall',$id)->where('id_user', $user->id)->count();

        if($meLikes > 0) {
            Walllikes::where('id_wall', $id)->where('id_user', $user->id)->delete();
            $array['liked'] = false;
        } else {
            $newLike = new Walllikes();
            $newLike->id_wall = $id;
            $newLike->id_user = $user->id;
            $newLike->save();
            $array['liked'] = true;
        }

        $array['likes'] = Walllikes::where('id_wall', $id)->count();


        return $array;
    }
}