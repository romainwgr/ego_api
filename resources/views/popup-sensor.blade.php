<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
    }

    .param-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 12px;
    }

    .param-label {
        flex: 0 0 250px; /* largeur fixe mais flexible */
        font-weight: bold;
        word-break: break-word;
    }

    .param-action {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 250px;
    }

    input[type="text"] {
        width: 100%;
        padding: 4px;
    }

    .info-label {
        font-style: italic;
        color: #666;
    }

    .d-none {
        display: none;
    }
    .btn-validate,
    .btn-cancel {
        padding: 8px 16px;
        border: none;
        font-weight: bold;
        cursor: pointer;
        border-radius: 4px;
    }

    .btn-validate {
        background-color: #198754;
        color: white;
    }

    .btn-cancel {
        background-color: #dc3545;
        color: white;
    }
</style>

<h2>Paramètres pour : {{ $model['SENSOR_MODEL_NAME'] }}</h2>

<form id="gliderParamForm">
    @foreach($model['PARAMETER_ALT_LABEL_ARRAY'] ?? [] as $index => $param)
        <div class="param-row">
        <div class="param-label">{{ $param }}</div>
        <div class="param-action">
            <input type="checkbox" id="chk_{{ $index }}" onchange="window.toggleInput({{ $index }})">
            <span id="label_{{ $index }}" class="info-label">No glider variable for this param.</span>
            <input type="text" name="glider_variables[{{ $param }}]" id="input_{{ $index }}" class="d-none" placeholder="Nom de variable pour {{ $param }}">
        </div>
        </div>
    @endforeach
    <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="submitForm()" class="btn-validate">Valider</button>
        <button type="button" onclick="window.close()" class="btn-cancel">Annuler</button>
    </div>
</form>


@vite('resources/js/popup-sensor.js')

