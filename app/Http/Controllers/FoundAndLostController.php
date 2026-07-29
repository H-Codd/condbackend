<?php

namespace App\Http\Controllers;

use App\Models\FoundAndLost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FoundAndLostController extends Controller
{
    public function getAll() {
        $array = ['error' => ''];

        $lost = FoundAndLost::where('status', 'LOST')->orderBy('datecreated', 'DESC')->orderBy('id', 'DESC')->get();
        $recovered = FoundAndLost::where('status', 'RECOVERED')->orderBy('datecreated', 'DESC')->orderBy('id', 'DESC')->get();

        foreach($lost as $lostKey => $lostValue){
            $lost[$lostKey]['datecreated'] = date('d/m/Y', strtotime($lostValue['datecreated']));
            $lost[$lostKey]['photo'] = asset('storage/',$lostValue['photo']);
        }

        foreach($lost as $recKey => $recValue){
            $lost[$recKey]['datecreated'] = date('d/m/Y', strtotime($recValue['datecreated']));
            $lost[$recKey]['photo'] = asset('storage/',$recValue['photo']);
        }

        $array['lost'] = $lost;
        $array['recovered'] = $recovered;

        return $array;
    }

    public function insert(Request $request) {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'where' => 'required|string',
            'photo' => 'required|file|mimes:jpg,png'
        ]);
        if(!$validator->fails()) {
            $description = $request->input('description');
            $where = $request->input('where');
            $file = $request->file('photo')->store('public');
            $file = explode('public/', $file);
            $photo = $file[1];

            $newLost = new FoundAndLost();
            $newLost->status = 'LOST';
            $newLost->photo = $photo;
            $newLost->description = $description;
            $newLost->where = $where;
            $newLost->datecreated = date('Y-m-d');
            $newLost->save();
        } else{
            $array['error'] = $validator->errors()->first();
        }

        return $array;
    }

    public function update(int $id, Request $request) {
        $array = ['error'=> ''];

        $status = $request->input('status');
        if($status && in_array($status, ['LOST', 'RECOVERED'])) {
            $item = FoundAndLost::find($id);
            if($item) {
                $item->status = $status;
                $item->save();
            } else {
                $array['error'] = 'Produto inexistente';
                return $array;
            }
        }else {
            $array['error'] = 'Insira Status para atualizar';
        }

        return $array;
    }

}
