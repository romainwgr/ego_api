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
Once you have chosen the sensor, provide at least the Serial number and the last calibration file.">Sensor list</h5>
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
<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2"></script>
<script>
    const sensor = JSON.parse(document.getElementById('sensors-data').textContent);

    /*function toggleGroup(header) {
        const group = header.parentElement;
        group.classList.toggle('open');
        console.log("toggleGroup");
    }*/

    function toggleGroup(header) {
        let group = header.closest('.group');
        if (group) {
            group.classList.toggle('open');
        }
    }


    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('.group').forEach(group => {
            group.classList.add('open');
        });
        optionsPlatformFamily();
        populateSensorFamilies(sensor);
        suggestUser();
        suggestInstitute();
        suggestObservatory();
        suggestDeployment();
        document.getElementById('sensor-family').addEventListener('change', function () {
            const selectedFamilyId = this.value;
            populateSensorModels(sensor, selectedFamilyId);
        });
    });
    let sensorIndex = 0;
    
    function optionsPlatformFamily(){
        const jsonElement = document.getElementById('uuv-usv');

        let vehicle = JSON.parse(jsonElement.textContent);

        const platformFamilySelect = document.getElementById('platformFamily');
        Object.entries(vehicle.families).forEach(([key, label]) => {
            if (key !== 'usv') { // si tu veux exclure 'usv'
                const option = document.createElement('option');
                option.value = key;
                option.textContent = label;
                platformFamilySelect.appendChild(option);
            }
        });

    }

    document.getElementById('platformFamily').addEventListener('change', function () {
        const family = this.value;
        const jsonElement = document.getElementById('uuv-usv');

        let vehicle = JSON.parse(jsonElement.textContent);
        const modelSelect = document.getElementById('platformType');
        modelSelect.innerHTML = '<option value="">Select a platform type</option>';
        if (vehicle.family[family]) {
            let type = vehicle.family[family]['type'];
            Object.values(type).forEach(model => {
                const option = document.createElement('option');
                option.value = model.og_model || '';
                option.textContent = model.name || model.og_model || 'Unknown';
                modelSelect.appendChild(option);
            });
        }
    });

    /*document.getElementById('sensor-family').addEventListener('change', function () {
        const family = this.value;
        const modelSelect = document.getElementById('sensor-model');
        modelSelect.innerHTML = '<option value="">Select a sensor model</option>';
        if (sensor[family]) {
            sensor[family].forEach(model => {
                const option = document.createElement('option');
                option.value = model.value;
                option.textContent = model.name;
                modelSelect.appendChild(option);
            });
        }
    });*/

    function populateSensorFamilies(sensorData) {
        const familySelect = document.getElementById('sensor-family');
        for (const familyId in sensorData) {
            const option = document.createElement('option');
            option.value = familyId;
            option.text = sensorData[familyId].SENSOR_FAMILY_NAMES;
            familySelect.appendChild(option);
        }
    }

    // Remplit les modèles basés sur la famille sélectionnée
    function populateSensorModels(sensorData, selectedFamilyId) {
        const modelSelect = document.getElementById('sensor-model');
        modelSelect.innerHTML = '<option value="">Select a sensor model</option>';

        const family = sensorData[selectedFamilyId];
        if (family && family.SENSOR_MODEL_NAMES) {
            for (const modelId in family.SENSOR_MODEL_NAMES) {
                const model = family.SENSOR_MODEL_NAMES[modelId];
                const option = document.createElement('option');
                option.value = modelId;
                option.text = model.SENSOR_MODEL_NAME;
                modelSelect.appendChild(option);
            }
        }
    }

    

    function addSensor() {
        const select = document.getElementById('sensor-model');
        const model = select.value;
        if (!model) return alert("Please select a sensor model");
        const modelName = select.options[select.selectedIndex].text;

        const container = document.getElementById('sensor-list-container');

        let block = document.createElement('div');
        block.className = 'border rounded p-2 mb-2 bg-light group';
        block.style.display = 'block';
        block.innerHTML = `
        <h4  class= "group-header" onclick = "const group = this.parentElement;content=group.querySelector('.group-content');if(content.style.display == 'block'){content.style.display = 'none';}else{content.style.display = 'block';} console.log('yoplait');let arrow = group.querySelector('.arrow'); if(content.style.display == 'none'){arrow.style.transform = 'rotate(0deg)';}else{arrow.style.transform = 'rotate(90deg)';}"><span class="arrow">&#9656;</span> ${modelName}</h4>
            <input type="hidden" name="capteurs[${sensorIndex}][nom]" value="${model}">

            <div class="group-content">
                <section class="mt-2">
                    <label>Serial nb *:</label>
                    <input type="text" class="form-control" name="capteurs[${sensorIndex}][serial]">
                </section>
                <section class="mt-2">
                    <label>Calibration date:</label>
                    <input type="date" class="form-control" name="capteurs[${sensorIndex}][date_calib]">
                </section>
                <section class="mt-2">
                    <label>Cal. file *:</label>
                    <input type="file" class="form-control" name="capteurs[${sensorIndex}][file_calib]">
                </section>
                <section class="mt-2 text-end">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.group').remove()">Remove</button>
                </section>
            </div>
        `;

        if (!block) return alert("le block est null");
        if (!container) return alert("le container est null");
        container.appendChild(block);
        sensorIndex++;
    }

    function toggleParams(event, el) {
        console.log("toggleParams");
        event.preventDefault();
        const block = el.nextElementSibling;
        block.style.display = block.style.display === 'none' ? 'block' : 'none';
    }

    document.getElementById('reset-form').addEventListener('click', function () {
        
        // Vider la liste des capteurs
        document.getElementById('sensor-list-container').innerHTML = '';
    });

    function removeSensor(index) {
        const sensorList = document.getElementById('sensor-list');
        const sensorToRemove = sensorList.querySelector(`.sensor-item[data-index="${index}"]`);
        if (sensorToRemove) {
            sensorList.removeChild(sensorToRemove);
        }
    }

    function suggestUser() {
        const userData = JSON.parse(document.getElementById('users-data').textContent);
        const piInput = document.getElementById('principalInvestigator');
        const hiddenInput = document.getElementById('principalInvestigator_id');

        const fuse = new Fuse(userData, {
            keys: ['userFirstName', 'userLastName'],
            threshold: 0.3,
        });

        // Créer la liste de suggestions si elle n'existe pas déjà
        let suggestionsList = document.getElementById('suggestions');
        if (!suggestionsList) {
            suggestionsList = document.createElement('ul');
            suggestionsList.id = 'pi-suggestions';
            suggestionsList.style.position = 'absolute';
            suggestionsList.style.background = 'white';
            suggestionsList.style.border = '1px solid #ccc';
            suggestionsList.style.zIndex = '10000';
            suggestionsList.style.listStyle = 'none';
            suggestionsList.style.padding = '0';
            suggestionsList.style.margin = '0';
            suggestionsList.style.maxHeight = '150px';
            suggestionsList.style.overflowY = 'auto';
            piInput.parentNode.style.position = 'relative'; // pour positionner suggestionsList correctement
            piInput.parentNode.appendChild(suggestionsList);
        }

        const wrapper = piInput.closest('.autocomplete-wrapper');
        wrapper.style.position = 'relative';

        // Place la suggestion list juste sous l'input
        suggestionsList.style.top = `${piInput.offsetTop + piInput.offsetHeight}px`;
        suggestionsList.style.left = `${piInput.offsetLeft}px`;

        piInput.addEventListener('input', function () {
            const value = piInput.value.trim().toLowerCase();
            suggestionsList.innerHTML = '';
            hiddenInput.value = ''; // Réinitialiser l'ID caché

            // Vérifie si on a une correspondance exacte
            const exactMatch = userData.find(user =>
                (user.userFirstName + ' ' + user.userLastName).toLowerCase() === value
            );

            if (exactMatch) {
                hiddenInput.value = exactMatch.userId;
                suggestionsList.style.display = 'none';
                return;
            }

            // Sinon, afficher les suggestions
            if (value === '') {
                suggestionsList.style.display = 'none';
                return;
            }

            const results = fuse.search(value);

            if (results.length === 0) {
                suggestionsList.style.display = 'none';
                return;
            }

            results.slice(0, 5).forEach(result => {
                const item = document.createElement('li');
                item.textContent = result.item.userFirstName + ' ' + result.item.userLastName;
                item.dataset.id = result.item.userId;
                item.style.cursor = 'pointer';
                item.style.padding = '5px';

                item.addEventListener('click', () => {
                    piInput.value = result.item.userFirstName + ' ' + result.item.userLastName;
                    hiddenInput.value = result.item.userId;
                    suggestionsList.innerHTML = '';
                    suggestionsList.style.display = 'none';
                });

                suggestionsList.appendChild(item);
            });

            suggestionsList.style.display = 'block';
        });
    }
    function suggestInstitute(){
        //A faire a partir du premier formulaire
        const input = document.getElementById('operatingInstitute');
        const hidden = document.getElementById('operatingInstitute_id');
        const suggestionsList = document.createElement('ul');

        suggestionsList.id = 'suggestionsInstitute';
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

        const wrapper = input.closest('.autocomplete-wrapper');
        wrapper.style.position = 'relative';

        // Place la suggestion list juste sous l'input
        suggestionsList.style.top = `${input.offsetTop + input.offsetHeight}px`;
        suggestionsList.style.left = `${input.offsetLeft}px`;

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
    }

    function suggestObservatory() {
        const input = document.getElementById('observatory');
        const hidden = document.getElementById('observatory_id');
        const suggestionsList = document.createElement('ul');

        suggestionsList.id = 'suggestionsObservatory';
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

        const wrapper = input.closest('.autocomplete-wrapper');
        wrapper.style.position = 'relative';

        // Place la suggestion list juste sous l'input
        suggestionsList.style.top = `${input.offsetTop + input.offsetHeight}px`;
        suggestionsList.style.left = `${input.offsetLeft}px`;

        // Données injectées dans la page via Blade
        const observatories = JSON.parse(document.getElementById('observatories-data').textContent);

        const fuse = new Fuse(observatories, {
            keys: ['name'],
            threshold: 0.3,
        });

        input.addEventListener('input', () => {
            const value = input.value.trim();
            hidden.value = ''; // ← Toujours réinitialiser d'abord

            // Rechercher un match exact
            const exactMatch = observatories.find(o => o.name.toLowerCase() === value.toLowerCase());
            if (exactMatch) {
                hidden.value = exactMatch.item_id;
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
                item.textContent = result.item.name;
                item.dataset.id = result.item.item_id;
                item.addEventListener('click', () => {
                    input.value = result.item.name;
                    hidden.value = result.item.item_id;
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
    }

    function suggestDeployment() {
        const input = document.getElementById('deploymentName');
        const hidden = document.getElementById('deploymentName_id');
        const suggestionsList = document.createElement('ul');
        suggestionsList.id = 'suggestionsDeploymentName';
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
        const wrapper = input.closest('.autocomplete-wrapper');
        wrapper.style.position = 'relative';

        // Place la suggestion list juste sous l'input
        suggestionsList.style.top = `${input.offsetTop + input.offsetHeight}px`;
        suggestionsList.style.left = `${input.offsetLeft}px`;
        // Données injectées dans la page via Blade
        const deployments = JSON.parse(document.getElementById('deployments-data').textContent);
        const fuse = new Fuse(deployments, {
            keys: ['name'],
            threshold: 0.3,
        });
        input.addEventListener('input', () => {
            const value = input.value.trim();
            hidden.value = ''; // ← Toujours réinitialiser d'abord

            // Rechercher un match exact
            const exactMatch = deployments.find(d => d.name.toLowerCase() === value.toLowerCase());
            if (exactMatch) {
                hidden.value = exactMatch.deployment_id;
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
                item.textContent = result.item.name;
                item.dataset.id = result.item.deployment_id;
                item.addEventListener('click', () => {
                    input.value = result.item.name;
                    hidden.value = result.item.deployment_id;
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
    }
        
</script>


