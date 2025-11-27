<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ZoteroItem;
use Illuminate\Support\Facades\Log;

class SyncZoteroItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zotero:sync-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Zotero items from API to local database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $groupId = env('ZOTERO_GROUP_ID');
        $apiKey = env('ZOTERO_API_KEY');

        $baseUrl = "https://api.zotero.org/groups/{$groupId}/items";
        $start = 0;
        $limit = 100; // Max items per request according to Zotero API documentation
        $totalItems = 0;
        $processedItems = 0;

        // 1. Initialize the array to store Zotero API itemKeys
        $zoteroApiItemKeys = []; 

        $this->info('Début de la synchronisation des éléments Zotero...');

        do {
            try {
                $response = Http::withHeaders([
                    'Zotero-API-Key'   => $apiKey,
                    'Zotero-API-Version' => '3',
                ])
                ->retry(3, 5000)        // jusqu’à 3 tentatives, 5s entre chaque
                ->timeout(300)           // max 300s pour la requête complète (5 minutes)
                ->connectTimeout(15)    // max 15s pour établir la connexion
                ->get($baseUrl, [
                    'format' => 'json',
                    'start'  => $start,
                    'limit'  => $limit,
                    'order'  => 'dateAdded',
                    'sort'   => 'asc',
                ]);


                if ($response->failed()) {
                    $this->error("Erreur lors de la récupération des données Zotero (start: {$start}): " . $response->status() . " - " . $response->body());
                    Log::error("Zotero Sync Error (start: {$start}): " . $response->status() . " - " . $response->body());
                    return Command::FAILURE;
                }

                $data = $response->json();
                $totalResultsHeader = $response->header('Total-Results');

                // La première fois, on récupère le nombre total d'éléments
                if ($start === 0) {
                    $totalItems = (int)$totalResultsHeader;
                    $this->info("Total d'éléments à synchroniser : {$totalItems}");
                }

                if (empty($data)) {
                    $this->info("Aucun élément supplémentaire à récupérer.");
                    break; // Plus d'éléments à récupérer
                }

                foreach ($data as $item) {
                    $itemKey = $item['key'] ?? null;

                    if ($itemKey) { // Ensure itemKey is not null before adding
                        $zoteroApiItemKeys[] = $itemKey; // <-- THIS LINE IS CRUCIAL AND WAS LIKELY MISSING
                    }

                    $title = $item['data']['title'] ?? null;
                    $itemType = $item['data']['itemType'] ?? null;
                    $abstractNote = $item['data']['abstractNote'] ?? null;
                    $publicationTitle = $item['data']['publicationTitle'] ?? null;
                    $zoteroDateString = $item['data']['date'] ?? null;

            
                     // --- LOGIQUE POUR EXTRAIRE L'ANNÉE ET LA STOCKER DANS LA VARIABLE 'date' ---
                    $extractedYear = null;
                    if ($zoteroDateString) {
                        // Utilise une expression régulière pour trouver 4 chiffres consécutifs
                        if (preg_match('/\b(\d{4})\b/', $zoteroDateString, $matches)) {
                            $extractedYear = (int)$matches[1]; // Convertir en entier
                        }
                    }
                    // La colonne 'date' va maintenant stocker uniquement l'année extraite
                    $dateToStore = $extractedYear;
                    // --- FIN DE LA LOGIQUE D'EXTRACTION ---

                    // Gestion des créateurs
                    $creatorsData = [];
                    if (isset($item['data']['creators']) && is_array($item['data']['creators'])) {
                        foreach ($item['data']['creators'] as $creator) {
                            $creatorsData[] = [
                                'creatorType' => $creator['creatorType'] ?? null,
                                'firstName'   => $creator['firstName'] ?? null,
                                'lastName'    => $creator['lastName'] ?? null,
                            ];
                        }
                    }

                    // Logique pour déterminer l'URL la plus pertinente (DOI > URL)
                    $attachmentUrl = null;
                    if (isset($item['data']['DOI']) && !empty($item['data']['DOI'])) {
                        $attachmentUrl = "https://doi.org/" . $item['data']['DOI'];
                    } elseif (isset($item['data']['url']) && !empty($item['data']['url'])) {
                        $attachmentUrl = $item['data']['url'];
                    }

                  


                    if ($itemKey) {
                        ZoteroItem::updateOrCreate(
                            ['itemKey' => $itemKey],
                            [
                                'title'            => $title,
                                'creators'         => $creatorsData, // Laravel va caster ceci en JSON grâce à protected $casts
                                'date'             => $dateToStore,
                                'attachment_url'   => $attachmentUrl, // Ceci est la valeur qui sera sauvegardée
                                'itemType'         => $itemType,
                                'abstractNote'     => $abstractNote,
                                'publicationTitle' => $publicationTitle,
                            ]
                        );
                        $processedItems++;
                    }
                }

                $start += $limit; // Préparer pour la prochaine page
                $this->info("Traités: {$processedItems}/{$totalItems}");

            } catch (\Exception $e) {
                $this->error("Une erreur inattendue est survenue: " . $e->getMessage());
                Log::error("Zotero Sync Exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
                return Command::FAILURE;
            }

        } while ($processedItems < $totalItems); // Continuer tant qu'il y a des éléments à traiter

         // --- NOUVEAU: GESTION DES SUPPRESSIONS ---
        $this->info('Vérification des éléments supprimés dans Zotero...');
        $localItemKeys = ZoteroItem::pluck('itemKey')->toArray(); // Obtenir toutes les clés locales

        // Trouver les clés qui sont dans la DB locale mais pas dans la réponse API (donc supprimées/déplacées)
        $itemsToDelete = array_diff($localItemKeys, $zoteroApiItemKeys);

        if (!empty($itemsToDelete)) {
            $deletedCount = ZoteroItem::whereIn('itemKey', $itemsToDelete)->delete();
            $this->info("{$deletedCount} éléments supprimés de la base de données locale car ils ne sont plus dans Zotero.");
        } else {
            $this->info("Aucun élément à supprimer de la base de données locale.");
        }
        // --- FIN DE LA GESTION DES SUPPRESSIONS ---
        $this->info("Synchronisation terminée. Total d'éléments traités: {$processedItems}/{$totalItems}");
        $this->info('Synchronisation Zotero terminée avec succès.');
        return Command::SUCCESS;
    }
}