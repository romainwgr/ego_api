<style>
    .form-container {
        max-width: 1000px; 
        margin: 50px auto;
        padding: 30px;
        background-color: #dde7f0; /* rectangle bleu clair */
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    section {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    section label {
        flex: 1;
        font-weight: bold;
        margin-right: 10px;
    }

    section input,
    section select,
    section textarea {
        flex: 2;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    textarea {
        resize: vertical;
    }

    .submit-button {
        text-align: right;
    }

    .required-asterisk {
        color: red;
        margin-left: 2px;
    }

    .field-label-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 4px;

    }

    .field-subtitle {
        font-size: 0.7em;
        color: #003366;
        line-height: 1.3;
        margin-top: 2px;
        max-width: 20em;
    }

    .suggestion-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: white;
        border: 1px solid #ccc;
        z-index: 1000;
        max-height: 150px;
        overflow-y: auto;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .suggestion-list li {
        padding: 6px 10px;
        cursor: pointer;
    }

    .suggestion-list li:hover {
        background-color: #f0f0f0;
    }
    .autocomplete-wrapper {
        width: 100%;
        position: relative;
    }
</style>
<div class="form-container">
    <form id="ego-inscription-form" >
        @csrf
        
        <!-- boucle en blade -->

        @foreach ($champs as $champ) 
            <section>
                <div class="field-label-group">
                    <label for="{{ $champ['name'] }}">
                        {{ $champ['label'] }}
                        @if (!empty($champ['required']) && $champ['required'])
                            <span class="required-asterisk">*</span>
                        @endif
                    </label>
                    @if (!empty($champ['subtitle']))
                        <div class="field-subtitle">{{ $champ['subtitle'] }}</div>
                    @endif
                </div>
                @if ($champ['type'] === 'textarea')
                    <textarea name="{{ $champ['name'] }}" id="{{ $champ['name'] }}" @if (!empty($champ['required']) && $champ['required']) required @endif></textarea>
                @elseif ($champ['type'] === 'checkbox')
                    <input type="checkbox" name="{{ $champ['name'] }}" id="{{ $champ['name'] }}" value="1">
                @else
            @if ($champ['name'] === 'userInstitute')
                <div class="autocomplete-wrapper">
                    <input 
                        type="{{ $champ['type'] }}" 
                        name="{{ $champ['name'] }}" 
                        id="{{ $champ['name'] }}" 
                        value="{{ $champ['value'] ?? '' }}" 
                        class="institute-input" 
                        style="width: 100%;box-sizing: border-box;"
                        autocomplete="off"
                        @if (!empty($champ['required']) && $champ['required']) required @endif
                    >
                    <ul class="suggestion-list"></ul>
                </div>
                <input type="hidden" name="group_id" id="group_id">
                <script id="groupes-data" type="application/json">
                    {!! json_encode($groupes) !!}
                </script>
            @else
                <input 
                    type="{{ $champ['type'] }}" 
                    name="{{ $champ['name'] }}" 
                    id="{{ $champ['name'] }}" 
                    value="{{ $champ['value'] ?? '' }}" 
                    @if (!empty($champ['required']) && $champ['required']) required @endif
                >
            @endif
        @endif
            </section>
        @endforeach

        <section>
            <button type="submit" class="submit-button">Envoyer</button>
        </section>

        <div id="ego-form-message" style="margin-top: 10px;"></div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2"></script>
<script>
    
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
        /*.then(res => res.json())
        .then(data => {
            const messageDiv = document.getElementById('ego-form-message');
            console.log('envoyé');
            if (data.success) {
                form.outerHTML = `<p style="color: green;">Demande d'inscription réussis</p>`;
            } else {
                messageDiv.textContent = data.data.message || "Une erreur s'est produite.";
            }
        })
        .catch(() => {
            document.getElementById('ego-form-message').textContent = "Erreur de communication avec le serveur.";
        });*/
    });
    /*document.addEventListener('DOMContentLoaded', () => {
        const input = document.querySelector('.institute-input');
        const suggestions = document.getElementById('suggestions');
        const hiddenId = document.getElementById('group_id');

        if (input) {
            input.addEventListener('input', async () => {
                const query = input.value;
                if (query.length < 2) {
                    suggestions.innerHTML = '';
                    return;
                }

                try {
                    const response = await fetch(`http://localhost:8000/api/groups/search?q=${encodeURIComponent(query)}`);
                    const groups = await response.json();

                    suggestions.innerHTML = '';
                    groups.forEach(group => {
                        const li = document.createElement('li');
                        li.textContent = `${group.group_name} - ${group.group_desc}`;
                        li.dataset.id = group.group_id;
                        li.style.cursor = 'pointer';
                        suggestions.appendChild(li);
                    });
                } catch (error) {
                    console.error("Erreur de recherche de groupes :", error);
                }
            });

            suggestions.addEventListener('click', (e) => {
                if (e.target.tagName === 'LI') {
                    input.value = e.target.textContent;
                    hiddenId.value = e.target.dataset.id;
                    suggestions.innerHTML = '';
                }
            });
        }
    });*/
    
</script>
