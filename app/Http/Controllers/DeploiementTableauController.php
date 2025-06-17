<?php

namespace App\Http\Controllers;

use App\Models\EgoDeploiement;
use Illuminate\Http\Response;

class DeploiementTableauController extends Controller
{
    public function renderHtml()
    {
        $deploiements = EgoDeploiement::select(
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
                    <th>Glider Name</th>
                    <th>Deployment</th>
                    <th>EGO Group</th>
                    <th>(planned) Starting date</th>
                    <th>(planned) Ending date</th>
                    <th>Status</th>
                    <th>Has JSON</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($deploiements as $deploiement) {
            
            $html .= '
                <tr>
                    <td>' /*. Nom du glider */.'</td>
                    <td>'. /*  Nom du deploiement .*/   '</td>
                    <td>' . /* Nom du groupe du deploiement .*/ '</td>
                    <td>'. /*  Date de debut .*/   '</td>
                    <td>'. /*  Date de fin .*/   '</td>
                    <td>'. /*  Statut .*/   '</td>
                    <td>'. /*  a un JSON .*/   '</td>
                    <td>' . /* mettre lien vers formulaire de modification .*/   '</td>
                </tr>';
        }

        $html .= '</tbody></table>';

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }
}