<?php

namespace App\Http\Controllers;

use App\Models\EgoMember;
use App\Models\EgoUser;
use Illuminate\Http\Response;

class EgoMemberTableauController extends Controller
{
    public function renderHtml()
    {
        $members = EgoMember::select(
            'attached_icon',
            'name',
            'name_detail',
            'resp_phpbbid',
            'address',
            'locator',
            'edmoRecordId',
            'country'
        )
        ->where('is_displayed', 1)
        ->orderBy('name')
        ->get();

        $html = '
        <style>
            table.ego-table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
            table.ego-table th, table.ego-table td { border: 1px solid #ccc; padding: 8px; vertical-align: top; }
            table.ego-table th { background-color: #f2f2f2; text-align: left; }
            .ego-member-logo { max-height: 40px; }
        </style>

        <table class="ego-table">
            <thead>
                <tr>
                    <th>Nat.</th>
                    <th colspan = "2">EGO group <small>(click on 🧑‍🤝‍🧑 to see all group members)</small></th>
                    <th>Address</th>
                    <th>EDMO id</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($members as $member) {
            $flag = asset('storage/country/'.e($member->country).'.png'); // recupérer les images des pays
            //recuperer les logos des groupes
            
            
            
            $logo = $member->attached_icon;
                //? '<img src="' . e($member->attached_icon) . '" class="ego-member-logo" alt="' . e($member->name) . ' logo">'
                //: 'Pas d’image.<br>alt : ' . e($member->name);

            if($logo=="" || !$logo){
                $logo = "pas d'image pour ".e($member->name);
            }


            $group = '<strong>' . e($member->name) . '</strong>';
            if ($member->resp_phpbbid) {
                $resp = EgoUser::select(
                    'first_name',
                    'last_name',
                    'id'
                )
                ->where('id', e($member->resp_phpbbid))
                ->first();
                if($resp){
                    $respName = e($resp->first_name).' '. e($resp->last_name);
                    $group .= '<br><small>(Resp. ' . $respName . ')</small>';
                }
                
            }
            $locator = e($member->locator);
            $html .= '
                <tr>
                    <td><img  src=' . $flag . ' alt = '.e($member->country).'></td>
                    <td><a href='.$locator.'>' . $logo . '</a></td><td><a href='.$locator.'>' . $group . '</a></td>
                    <td>' . nl2br(e($member->address)) . '</td>
                    <td>' . e($member->edmoRecordId) . '</td>
                </tr>';
        }

        $html .= '</tbody></table>';

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }
}

