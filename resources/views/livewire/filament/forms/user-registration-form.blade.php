
<form wire:submit.prevent="submit" class="space-y-4">
    <input wire:model="user.username" type="text" placeholder="Username">
    <input wire:model="user.userInstitute" type="text" placeholder="Institute">
    <input wire:model="user.userInstituteWebsite" type="url" placeholder="Website">
    <input wire:model="user.userORCID" type="text" placeholder="ORCID">
    <input wire:model="user.userPassword" type="password" placeholder="Password">
    <input wire:model="user.userFirstName" type="text" placeholder="First Name">
    <input wire:model="user.userLastName" type="text" placeholder="Last Name">
    <input wire:model="user.professionalEmail" type="email" placeholder="Pro Email">
    <input wire:model="user.userMail" type="email" placeholder="Alt Email">
    <textarea wire:model="user.userMotivation" placeholder="Motivation"></textarea>
    <label>
    <input type="checkbox" wire:model="user.egoMembership" value="1">
        Membre EGO
    </label>

    <button type="submit">Envoyer</button>
</form>
