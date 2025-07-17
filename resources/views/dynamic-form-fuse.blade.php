<!-- fait par Damien Jegou le 24/06/2025 -->
<!-- Ce fichier est un formulaire d'inscription dynamique utilisant Blade et Fuse.js pour l'autocomplétion -->

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


@vite('resources/js/register.js')
