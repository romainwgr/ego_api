<!-- Fait par Damien Jegou --> 
<!-- Ce fichier génère un formulaire dynamique pour l'inscription d'un déploiement de véhicule autonome (glider, USV, etc.) -->

<style>
    .form-container {
        max-width: 1000px;
        margin: 50px auto;
        padding: 30px;
        background-color: #dde7f0;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    section {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
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
        min-width: 200px;
    }

    section .field-hint {
        flex: 1.5;
        font-size: 0.85em;
        color: #003366;
        padding-left: 10px;
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

    .group-content {
        display: none;
        margin-top: 10px;
    }

    .group.open .group-content {
        display: block;
    }

    .group h2 {
        cursor: pointer;
        background-color: #c2d4e3;
        padding: 10px;
        border-radius: 5px;
        user-select: none;
    }

    @media (max-width: 768px) {
        section {
            flex-direction: column;
            align-items: stretch;
        }

        section label,
        section input,
        section select,
        section textarea,
        section .field-hint {
            flex: unset;
            width: 100%;
        }

        section .field-hint {
            padding-left: 0;
            margin-top: 5px;
        }
    }

    .group-header {
        display: flex;
        align-items: center;
        font-size: 1.2em;
        font-weight: bold;
        cursor: pointer;
        background-color: #c2d4e3;
        padding: 10px;
        border-radius: 5px;
        user-select: none;
    }

    .group-header h4{
        font-size: 0.8 em;
    }

    .group-header .arrow {
        display: inline-block;
        margin-right: 10px;
        transition: transform 0.2s ease;
    }

    .group.open .group-header .arrow {
        transform: rotate(90deg);
    }

    .glider-sensors label {
        font-weight: bold;
    }

    .parameter-block {
        border-left: 3px solid #ccc;
        padding-left: 10px;
    }
    .glider-sensor-form {
        background-color: #dce6f0;
        padding: 15px;
        border-radius: 4px;
        max-width: 100%;
    }

    .form-body {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .add-sensor {
        width: 45%;
    }

    .add-sensor label {
        display: block;
        font-weight: bold;
        margin-top: 10px;
    }

    .add-sensor select {
        width: 100%;
        margin-top: 3px;
    }

    .add-btn {
        margin-top: 10px;
        padding: 5px 10px;
        background-color: #cce5ff;
        border: 1px solid #007bff;
        color: #007bff;
        cursor: pointer;
        border-radius: 3px;
    }

    .sensor-list {
        width: 50%;
        margin-left: 10px;
    }

    .sensor-list h5 {
        font-weight: bold;
    }
    /* Style unifié des <select> */
    select.form-select {
        display: block;
        width: 100%;
        padding: 6px 10px;
        font-size: 14px;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 4px;
        appearance: auto;
        box-shadow: none;
    }

    /* Ajout de la flèche standard */
    select.form-select:focus {
        outline: none;
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Optionnel : homogénéiser les marges pour les labels + selects */
    .add-sensor label {
        margin-top: 10px;
        font-weight: bold;
    }

    .add-sensor select {
        margin-top: 3px;
        margin-bottom: 10px;
    }

    .inline-radio-section {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    .autocomplete-wrapper {
        flex: 2;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .autocomplete-wrapper input[type="text"] {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        z-index: 10;
        position: relative;
        background-color: white;
    }

    /* Liste des suggestions bien positionnée en dessous */
    .suggestion-list {
        position: absolute;
        top: calc(100% + 2px); /* 2px d’espace sous l’input */
        left: 0;
        right: 0;
        z-index: 1000;
        background-color: white;
        border: 1px solid #ccc;
        border-top: none;
        border-radius: 0 0 4px 4px;
        max-height: 180px;
        overflow-y: auto;
        list-style: none;
        margin: 0;
        padding: 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .suggestion-list li {
        padding: 6px 10px;
        cursor: pointer;
        white-space: nowrap;
    }

    .suggestion-list li:hover {
        background-color: #f0f8ff;
    }
    .file-input-wrapper {
        flex: 2; /* Pour qu'il prenne la même largeur que les autres inputs */
        display: flex; /* Utilise flexbox pour l'alignement interne */
        flex-direction: column; /* Empile l'input et la liste */
        gap: 5px; /* Petit espace entre l'input et la liste des fichiers */
        min-width: 200px; /* Conserver le min-width de la section input */
    }
</style>

<div class="form-container">
    <form id="ego-inscription-form">
        @csrf
        @php $currentGroup = null; @endphp

        @foreach ($champs as $champ)
            @if ($currentGroup !== $champ['group'])
                @if (!is_null($currentGroup))
                        </div>
                    </div>
                @endif
                @php $currentGroup = $champ['group']; @endphp
                @if(!is_null($currentGroup))                
                    <div class="group" id="{{ $currentGroup }}">
                        <h2 class="group-header" onclick="toggleGroup(this)"><span class="arrow">&#9656;</span>{{ $currentGroup }}</h2>
                        <div class="group-content">
                @endif
            @endif

            <section @if ($champ['type'] === 'radio') class="inline-radio-section" @endif>
                <label for="{{ $champ['name'] }}" @if (isset($champ['title'])) title="{{ $champ['title'] }}" @endif>
                    {{ $champ['label'] }}
                    @if (!empty($champ['required']) && $champ['required'])
                        <span class="required-asterisk">*</span>
                    @endif
                </label>

                @if ($champ['type'] === 'textarea')
                    <textarea name="{{ $champ['name'] }}" id="{{ $champ['name'] }}" @if (!empty($champ['required']) && $champ['required']) required @endif></textarea>
                @elseif ($champ['type'] === 'checkbox')
                    <input type="checkbox" name="{{ $champ['name'] }}" id="{{ $champ['name'] }}" value="1">
                @elseif ($champ['type'] === 'select')
                    <select name="{{ $champ['name'] }}" id="{{ $champ['name'] }}" @if (!empty($champ['required']) && $champ['required']) required @endif>
                        @foreach ($champ['options'] as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                @elseif ($champ['type'] === 'radio')
                    @foreach ($champ['options'] as $option)
                        <label>                            <input type="radio" name="{{ $champ['name'] }}" value="{{ $option['value'] }}" @if (!empty($champ['required']) && $champ['required']) required @endif>
                            {{ $option['label'] }}
                        </label>
                    @endforeach
                @else
                    @if(isset($champ['datalist']) && $champ['datalist'])
                        <div class = "autocomplete-wrapper">
                    @endif
                    <input type="{{ $champ['type'] }}" name="{{ $champ['name'] }}" id="{{ $champ['name'] }}" @if (!empty($champ['required']) && $champ['required']) required @endif>
                    @if(isset($champ['datalist']) && $champ['datalist'])
                        <ul class="suggestion-list" id="{{ $champ['name'] }}_suggestion"></ul>
                        </div>
                        <input type="hidden" name="{{ $champ['name'] }}_id" id="{{ $champ['name'] }}_id">
                    @endif
                @endif

                <div class="field-hint">
                    @if ($champ['json code'] !== 'none' && $champ['json code'] !== 'null')
                        (EGO json code <strong>{{ $champ['json code'] }}</strong>)
                    @elseif ($champ['json code'] === 'none')
                        (Not in EGO json format)
                    @elseif (!empty($champ['labelAfter']))
                        {{ $champ['labelAfter'] }}
                    @endif
                </div>
            </section>
        @endforeach

        @if (!is_null($currentGroup))
                </div>
            </div>
        @endif
        <div class="group">
            <h2 class="group-header" onclick="toggleGroup(this)"><span class="arrow">&#9656;</span>Glider Sensor</h2>
            <div class="group-content">
                <div class="form-body">
                    {{-- Zone gauche : ajout capteurs --}}
                    <div class="add-sensor">
                        <h3 title="Choose the family of the sensor to add, and then the sensor model.">Add sensors</h3>

                        <label for="sensor-family" title="Sensor families are larges categories of sensors models measuring the same types of parameters.
Select a family, then a model, and press the plus icon to add the new sensor.
To remove a sensor already selected, simply press the minus icon">Sensor families:</label>
                        <select id="sensor-family" class="form-select">
                            <option value="">Select a sensor family</option>
                        </select>

                        <label for="sensor-model" title="Sensor models are the actual devices installed on the glider. A given sensor model can include several physical sensors (e.g. a CTD) and delivers Parameters. Here, only the Sensor Models from the OceanGliders database are presented. They all (almost!) have a template. OceanGliders standardized Parameters must be translated to a glider manufacturer vocabulary. This translation is not avaiailable for all sensor model/glider pairs in the EGO database.">Sensor models:</label>
                        <select id="sensor-model" class="form-select">
                            <option value="">Select a sensor model</option>
                        </select>

                        <button type="button" class="add-btn" title="Add sensor" onclick="addSensor()">➕ Add</button>
                    </div>

                    {{-- Zone droite : capteurs ajoutés --}}
                    <div class="sensor-list">
                        <h3 title="Choose at least ONE sensor (select Sensor families then Sensor model and press the plus icon).
If you have such sensor model registered in the EGO database (from Coriolis), you can choose from the Json files of these models to use as a template.
Once you have chosen the sensor, provide at least the Serial number and the last calibration file.">Sensor list</h3>
                        <div id="sensor-list-container"></div>
                    </div>
                </div>
            </div>
        </div>
        <section style="text-align: center;">
            <button type="submit" class="submit-button">Save</button>
            <button type="reset" id="reset-form" class="submit-button">Reset full form</button>
        </section>

        <div id="ego-form-message" style="margin-top: 10px;"></div>
    </form>
</div>
<script id="sensors-data" type="application/json">
    {!! json_encode($sensors) !!}
</script>
<script id="uuv-usv" type="application/json">
    {!! json_encode($vehicle) !!}
</script>
<script id="users-data" type="application/json">
    {!! json_encode($user) !!}
</script>
<script id="groupes-data" type="application/json">
    {!! json_encode($groupes) !!}
</script>
<script id="observatories-data" type="application/json">
    {!! json_encode($observatories) !!}
</script>
<script id="deployments-data" type="application/json">
    {!! json_encode($deployments) !!}
</script>
<script id="gliders-data" type="application/json">
    {!! json_encode($gliders) !!}
</script>




@vite('resources/js/deploiement.js')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

