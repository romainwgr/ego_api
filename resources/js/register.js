// Fait le 30/06 par Damien JEGOU
// Javascript pour la page d'incription

import Fuse from 'fuse.js';
let messageDiv;

document.addEventListener('DOMContentLoaded', () => {
    messageDiv = document.getElementById('ego-form-message');
    messageDiv.textContent = "";
    const input = document.getElementById('userInstitute');
    const hidden = document.getElementById('group_id');
    const options = document.querySelectorAll('#groups_list option');
    const suggestionsList = document.createElement('ul');

    suggestionsList.id = 'suggestions';
    suggestionsList.style.position = 'absolute';
    suggestionsList.style.background = 'white';
    suggestionsList.style.border = '1px solid #ccc';
    suggestionsList.style.zIndex = '10';
    suggestionsList.style.listStyle = 'none';
    suggestionsList.style.padding = '0';
    suggestionsList.style.margin = '0';
    suggestionsList.style.maxHeight = '150px';
    suggestionsList.style.overflowY = 'auto';

    input.parentNode.appendChild(suggestionsList);

    // Données injectées dans la page via Blade
    const groupes = JSON.parse(document.getElementById('groupes-data').textContent);

    const fuse = new Fuse(groupes, {
        keys: ['group_name'],
        threshold: 0.3,
    });

    input.addEventListener('input', () => {
        const value = input.value.trim();
        hidden.value = ''; // ← Toujours réinitialiser d'abord

        // Rechercher un match exact
        const exactMatch = groupes.find(g => g.group_name.toLowerCase() === value.toLowerCase());
        if (exactMatch) {
            hidden.value = exactMatch.group_id;
            return;
        }

        // Sinon, afficher les suggestions
        const results = fuse.search(value);
        suggestionsList.innerHTML = '';

        if (value === '' || results.length === 0) {
            suggestionsList.style.display = 'none';
            return;
        }

        results.slice(0, 5).forEach(result => {
            const item = document.createElement('li');
            item.textContent = result.item.group_name;
            item.dataset.id = result.item.group_id;
            item.addEventListener('click', () => {
                input.value = result.item.group_name;
                hidden.value = result.item.group_id;
                suggestionsList.innerHTML = '';
                suggestionsList.style.display = 'none';
            });
            suggestionsList.appendChild(item);
        });

        suggestionsList.style.display = 'block';
    });

    document.addEventListener('click', (e) => {
        if (!suggestionsList.contains(e.target) && e.target !== input) {
            suggestionsList.innerHTML = '';
            suggestionsList.style.display = 'none';
        }
    });
    
});
document.getElementById('ego-inscription-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('action', 'ego_envoyer_inscription');

    fetch('http://localhost:8000/api/inscription', {
        method: 'POST',
        body: formData
    })
    .then(async res => {
        const data = await res.json();
        
        messageDiv.textContent = ""; // Réinitialiser le message avant d'afficher un nouveau
        if (!res.ok) {
            // Laravel retourne souvent une structure de ce type en cas d'erreur de validation
            const errors = data.errors;
            if (errors) {
                // On récupère le premier message d'erreur
                const firstError = Object.values(errors)[0][0];
                messageDiv.textContent = firstError;
            } else {
                messageDiv.textContent = data.message || "Une erreur inconnue s'est produite.";
            }
            throw new Error("Erreur de validation");
        }

        if (data.success) {
            if(data.type === 'with_group'){
                form.outerHTML = `<p style="color: green;">Your registration request has been submitted successfully. A request to your institute will be send once your account is validated</p>`;
            }
            else{
                form.outerHTML = `<p style="color: green;">Your registration request has been submitted successfully.</p>`;
            }
            
        } else {
            messageDiv.textContent = data.message || "Une erreur s'est produite.";
        }
    })
    .catch(error => {
        if (messageDiv.textContent === "") {
            messageDiv.textContent = "Erreur de communication avec le serveur.";
        }
        console.error("Erreur : ", error);
    });
});