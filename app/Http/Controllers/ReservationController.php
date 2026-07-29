<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\AreaDisabledDay;
use App\Models\Reservation;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function getReservations() {
        $array = ['error' => '', 'list' => []];
        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab' ];
        $areas = Area::where('allowed', 1)->get();

        foreach($areas as $area) {
            $dayList = explode(',', $area['days']);

            $dayGroups = [];

            $lastDay = intval(current($dayList));
            $dayGroups[] = $daysHelper[$lastDay];
            array_shift($dayList);

            foreach($dayList as $day) {
                if(intval($day) != $lastDay + 1) {
                    $dayGroups[] = $daysHelper[$lastDay];
                    $dayGroups[] = $daysHelper[$day];
                }
            
                $lastday = intval($day);
            }

            $dayGroups[] = $daysHelper[end($dayList)];

            $dates = '';
            $close = 0;
            foreach($dayGroups as $group) {
                if($close === 0) {
                    $dates.= $group;
                } else {
                    $dates .= '-'.$group.',';
                }
                $close = 1 - $close;
            }
            $dates = explode(',', $dates);
            array_pop($dates);

            $start = date('H:i', strtotime($area['start_time']));
            $end = date('H:i', strtotime($area['end_time']));

            foreach($dates as $dKey => $dValue) {
                $dates[$dKey] .= ' '.$start.'ás'.$end;
            }

            $array['list'][] = [
                'id' => $area['id'],
                'cover' => asset('storage/'.$area['cover']),
                'title' => $area['title'],
                'dates' => $dates
            ];
        }

        return $array;
    }
    public function setReservation(int $id, Request $request) {
        $array = ['error'=> ''];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'property' => 'required'
        ]);
        if(!$validator->fails()){
            $date = $request->input('date');
            $time = $request->input('time');
            $property = $request->input('property');

            $unit = Unit::find($property);
            $area = Area::find($id);

            if($unit && $area) {
                $can = true;

                $weekday = date('w', strtotime($date));

                $allowedDays = explode(',', $area['days']);
                if(in_array($weekday, $allowedDays)){
                    $can = false;
                }else {
                    $start = strtotime($area['start_time']);
                    $end = strtotime('-1 hour', strtotime($area['end_time']));
                    $revtime = strtotime($time);
                    if($revtime < $start || $revtime > $end) {
                        $can = false;
                    }
                }

                $existingDisabledDay = AreaDisabledDay::where('id_area', $id)->where('day', $date)->count();
                if($existingDisabledDay > 0) {
                    $can = false;
                }

                $existingReservations = Reservation::where('id_area', $id)->where('reservation_date', $date. ' '.$time)->count();
                if($existingReservations) {
                    $can = false;
                }

                if($can){
                    $newReservation = new Reservation();
                    $newReservation->id_unit = $property;
                    $newReservation->id_area = $id;
                    $newReservation->reservation_date = $date.' '.$time;
                    $newReservation->save();
                } else{
                    $array['error'] = 'Reserva não permitida';
                    return $array;
                }


            }else{
                $array['error'] = 'Dados incorretos';
                return $array;
            }
        } else {
            $array['error'] = $validator->errors()->first();
        }

        return $array;
    }
    public function getDisabledDates(int $id) {
        $array = ['error' => '', 'list' => []];
        
        $area = Area::find($id);
        if ($area) {
            // Dias disabled padrão
            $disabledDays = AreaDisabledDay::where('id_area', $id)->get();
            foreach ($disabledDays as $disabledDay) {
                $array['list'][] = $disabledDay['day'];
            }
            
            // Dias disabled através do allowed
            $allowedDays = explode(',', $area['days']);
            $offDays = [];
            for ($q = 0; $q < 7; $q++) {
                if (!in_array($q, $allowedDays)) {
                    $offDays[] = $q;
                }
            }

            // Listar os dias proibidos +3 meses para frente
            $start = strtotime('today');
            $end = strtotime('+3 months');
            for ($current = $start; $current < $end; $current = strtotime('+1 day', $current)) {
                $wd = date('w', $current);
                if (in_array($wd, $offDays)) {
                    $array['list'][] = date('Y-m-d', $current);
                }
            }
        } else {
            $array['error'] = 'Area inexistente';
        }

        return $array;
    }

    public function getTimes(int $id, Request $request) {
        $array = ['error' => '', 'list' => []];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d'
        ]);
        if(!$validator->fails()) {
            $date = $request->input('date');
            $area = Area::find($id);

            if($area) {

                $can = true;
                // Verificar se é dia disabled
                $existingDisabledDay = AreaDisabledDay::where('id_area', $id)->where('day', $date)->count();

                if($existingDisabledDay > 0) {
                    $can = false;
                }

                // Verifica se é dia permitido
                $allowedDays = explode(',', $area['days']);
                $weekday = date('w', strtotime($date));
                if(!in_array($weekday, $allowedDays)){
                    $can = false;
                }

                if($can) {
                    $start = strtotime($area['start_time']);
                    $end = strtotime($area['end_time']);
                    $times = [];

                    for($lastTime = $start; $lastTime < $end; $lastTime = strtotime('+1 hour', $lastTime)){
                        $times[] = $lastTime;
                    }

                    $timeList = [];
                    foreach($times as $time) {
                        $timeList[] = [
                            'id' => date('H:i:s', $time),
                            'title' => date('H:i', $time).' - '.date('H:i', strtotime('+1 hour', $time))
                        ];
                    }
                    $reservations = Reservation::where('id-area', $id)->whererBetween('reservation_date', [
                        $date.' 00:00:00',
                        $date.' 23:59:59'
                    ])->get();

                    $toRemove = [];
                    foreach($reservations as $reservation) {
                        $time = date('H:i:s', strtotime($reservation['reservation_date']));
                        $toRemove[] = $time;
                    }

                    foreach($timeList as $timeItem) {
                        if(!in_array($timeItem['id'], $toRemove)){
                            $array['list'][] = $timeItem;
                        }
                    }
                    return $array;
                } else {
                    return ['error' => 'Não há horário'];
                }

            } else {
                $array['error'] = ' Area inexistente';
            }
        } else {
            $array['error'] = $validator->errors()->first();
        }

        return $array;
    }
    public function getMyReservation(Request $request) {
        $array = ['error' => '', 'list' => []];

        $property = $request->input('property');
        if($property) {
            $unit = Unit::find($property);
            if($unit) {

                $reservations = Reservation::where('id_unit', $property)->orderBy('reservation_date', 'DESC')->get();

                foreach($reservations as $reservation) {
                    $area = Area::find($reservation['id_area']);

                    $daterev = date('d/m/Y H:i', strtotime($reservation['reservation_date']));
                    $afterTime = date('H:i', strtotime('+1 hout', strtotime($reservation['reservation_date'])));
                    $daterev.= ' à '.$afterTime;

                    $array['list'][] = [
                        'id' => $reservation['id'],
                        'id_area' => $reservation['id_area'],
                        'title' => $area['title'],
                        'cover' => asset('storage/'.$area['cover']),
                        'datereserved' => $daterev
                    ];
                    return $array;

                }
            } else {
                $array['error'] = 'Propriedade inexistente';
                return $array;
            }

        } else {
            $array['error'] = 'Propriedade necessária';
            return $array;
        }
        return $array;
    }
    public function deleteReservation(int $id) {
        $array = ['error' => ''];

        $user = Auth::user();
        $reservation = Reservation::find($id);
        if($reservation) {
            $unit = Unit::where('id', $reservation['id_unit'])->where('id_owner', $user->id)->count();

            if($unit > 0) {
                Reservation::find($id)->delete();
            } else {
                $array['error'] = 'Esta reserva não é sua';
                return $array;
            }
        }
        return $array;
    }
}
