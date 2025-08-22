<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalRegion; // Continuez à utiliser ce modèle, il est juste mappé à ego_observatory

class GlobalRegionTableController extends Controller
{
    public function index()
    {
        // 1. Récupérer toutes les "catégories globales" (les en-têtes de colonne)
        // Ce sont les entrées où 'type' est 'reg_obs'
        $globalCategories = GlobalRegion::where('type', 'reg_obs')
                                        ->orderBy('short_name') // Trier par nom court pour l'ordre des colonnes
                                        ->get();

        // 2. Récupérer toutes les régions individuelles (les éléments sous les en-têtes)
        // Ce sont les entrées où 'type' n'est PAS 'reg_obs' et qui ont un 'belongto_id'
        $individualRegions = GlobalRegion::where('type', '!=', 'reg_obs')
                                         ->whereNotNull('belongto_id')
                                         ->where('belongto_id', '!=', 0) // S'assurer qu'il y a un parent valide
                                         ->orderBy('name') // Trier les éléments alphabétiquement dans chaque colonne
                                         ->get();

        // 3. Organiser les données dans la structure souhaitée
        $groupedData = [];

        // Remplir les catégories globales en premier
        foreach ($globalCategories as $category) {
            $categoryName = $category->short_name; // Ex: ANT-Global, ATL-Global
            $groupedData[$categoryName] = [
                'category_url' => $category->locator, // L'URL pour le titre de la colonne
                'items' => [],
            ];
        }

        // Ajouter les régions individuelles à leurs catégories respectives
        foreach ($individualRegions as $region) {
            // Trouver la catégorie parente en utilisant belongto_id
            $parentCategory = $globalCategories->firstWhere('item_id', $region->belongto_id);

            if ($parentCategory) {
                $parentCategoryName = $parentCategory->short_name;
                if (isset($groupedData[$parentCategoryName])) {
                    $groupedData[$parentCategoryName]['items'][] = [
                        'region_name' => $region->short_name, // Ou $region->name si vous préférez le nom long
                        'link_url' => $region->locator, // L'URL pour l'élément de la liste
                    ];
                }
            }
        }

        return response()->json($groupedData);
    }
}