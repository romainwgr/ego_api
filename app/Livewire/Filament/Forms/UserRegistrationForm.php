<?php

namespace App\Livewire\Filament\Forms;

use Livewire\Component;
use App\Models\EgoUserTest;
use Illuminate\Support\Facades\Http;

class UserRegistrationForm extends Component
{
    public EgoUserTest $user;

    public function mount()
    {
        $this->user = new EgoUserTest([
            'is_validated' => 0   // Par défaut décoché
        ]);
    }

    public function submit()
    {
        $validated = $this->validate([
            'user.username' => 'required|string',
            'user.userInstitute' => 'required|string',
            'user.userInstituteWebsite' => 'required|url',
            'user.userORCID' => 'required|string',
            'user.userPassword' => 'required|string|min:6',
            'user.userFirstName' => 'required|string',
            'user.userLastName' => 'required|string',
            'user.professionalEmail' => 'required|email',
            'user.userMail' => 'requred|email',
            'user.userMotivation' => 'min:50|string',
            'user.egoMembership' => 'boolean'
        ]);

        // Optionnel : sauvegarde locale
        $this->user->save();

        // Envoi vers WordPress
        $response = Http::post(route('api.send-to-wordpress'), $this->user->toArray());

        if ($response->successful()) {
            session()->flash('success', 'Utilisateur enregistré et envoyé à WordPress.');
            $this->user = new EgoUserTest(); // Réinitialiser le formulaire
        } else {
            session()->flash('error', 'Erreur lors de l’envoi à WordPress.');
        }
    }

    public function render()
    {
        return view('livewire.filament.forms.user-registration-form');
    }
}
