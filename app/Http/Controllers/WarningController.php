<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Warning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WarningController extends Controller
{
    public function getMyWarnings(Request $request) {
        $array = ['error' => ''];

        $property = $request->input('peoperty');
        if($property){
            $user = Auth::user();

            $unit = Unit::where('id', $property)->where('id_owner', $user->id)->count();

            if($unit > 0) {

                $warnings = Warning::where('id_unit', $property)->orderBy('datecreated', 'DESC')->orderBy('id', 'DESC')->get();
                
                foreach($warnings as $warnKey => $warnValue){
                    $warnings[$warnKey]['datecreated'] = date('d/m/Y', strtotime($warnValue['datecreated']));
                    $photoList = [];
                    $photos = explode(',', $warnValue['photos']);

                    foreach($photos as $photo) {
                        if(!empty($photo)){
                            $photoList[] = asset('storage/'.$photo);
                        }
                    }

                    $warnings[$warnKey]['photos'] = $photoList;
                }

                $array['list'] = $warnings;

            } else {

                $array['error'] = 'Esta unidade não é sua';
                
            }

        } else {
            $array['error'] = 'A propriedade é necessaria.';
        }

        return $array;
    }

    public function addWarningFile(Request $request) {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(),[
            'photo' => 'required|file|mimes:jpg,png'
        ]);

        if(!$validator->fails()){
            $file = $request->file('photo')->store('public');

            $array['photo'] = asset(Storage::url($file));
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function setWarning(Request $request) {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'property' => 'required',
            'list' => 'nullable|array'
        ]);

        if (!$validator->fails()) {
            $title = $request->input('title');
            $property = $request->input('property');
            $list = $request->input('list');

            $newWarn = new Warning();
            $newWarn->id_unit = $property;
            $newWarn->title = $title;
            $newWarn->status = 'IN REVIEW';
            $newWarn->datecreated = date('Y-m-d');

            $photos = [];
            if ($list && is_array($list)) {
                foreach ($list as $listItem) {
                    $url = explode('/', $listItem);
                    $photos[] = end($url);
                }
            }

            $newWarn->photos = !empty($photos) ? implode(',', $photos) : '';
            $newWarn->save();

            $array = ['data' => $newWarn];
        } else {
            $array['error'] = $validator->errors()->first();
    }
        return $array;
    }
}
