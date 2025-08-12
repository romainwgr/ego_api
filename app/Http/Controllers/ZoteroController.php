<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ZoteroItem;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ZoteroController extends Controller
{
    /**
     * Récupère et retourne les éléments Zotero depuis la base de données locale.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = ZoteroItem::orderBy('date', 'desc')->where('itemType', '!=', 'note');
            
            $items = $query->get();

            $formattedItems = $items->map(function ($item) {
                $creators = $item->creators;
                $formattedCreators = [];

                if (is_array($creators)) {
                    foreach ($creators as $creator) {
                        $fullName = '';
                        if (isset($creator['lastName']) && !empty($creator['lastName'])) {
                            $fullName = $creator['lastName'];
                            if (isset($creator['firstName']) && !empty($creator['firstName'])) {
                                $fullName = $creator['firstName'] . ' ' . $fullName;
                            }
                        } elseif (isset($creator['firstName']) && !empty($creator['firstName'])) {
                            $fullName = $creator['firstName'];
                        }

                        if (!empty($fullName)) {
                            $formattedCreators[] = $fullName;
                        }
                    }
                }

                $displayDate = 'Unknown Date';

                if (!empty($item->date_original)) {
                    $displayDate = $item->date_original;
                } elseif (!empty($item->date)) {
                    $displayDate = (string) $item->date;
                }

                return [
                    'title'            => $item->title,
                    'creators'         => implode(', ', $formattedCreators),
                    'date'             => $displayDate,
                    'url'   => $item->attachment_url,
                    'itemType'         => $item->itemType,
                    'publicationTitle' => $item->publicationTitle,
                    'itemKey'          => $item->itemKey,
                    'abstractNote'     => $item->abstractNote,
                ];
            });

            return response()->json($formattedItems);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des éléments Zotero pour l\'API : ' . $e->getMessage());
            return response()->json(['error' => 'Impossible de récupérer les données Zotero.'], 500);
        }
    }
}