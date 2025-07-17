// Fait le 30/06 par Damien JEGOU
// Javascript pour la page d'enregistrement des déploiements
import Fuse from 'fuse.js';
import { toggleInput } from './popup-sensor.js';

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
window.toggleGroup = toggleGroup;

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
    suggestGliderName();
    disableAllExceptGlidernameAndIsFields(document.getElementById('ego-inscription-form'));
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

function populatePlatformType(family){
    const jsonElement = document.getElementById('uuv-usv');

    let vehicle = JSON.parse(jsonElement.textContent);
    const modelSelect = document.getElementById('platformType');
    modelSelect.innerHTML = '<option value="">Select a platform type</option>';
    if (vehicle.family[family]) {
        let type = vehicle.family[family]['type'];
        Object.values(type).forEach(model => {
            const option = document.createElement('option');
            option.value = model.ego_acronyme || '';
            option.textContent = model.name || model.ego_acronyme || 'Unknown';
            modelSelect.appendChild(option);
        });
    }
}

document.getElementById('platformFamily').addEventListener('change', function () {
    const family = this.value;
    populatePlatformType(family);
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

function disableAllExceptGlidernameAndIsFields(formElement) {
    const elements = formElement.querySelectorAll('input, select, textarea, button');

    elements.forEach(element => {
        const name = element.getAttribute('name');
        if (name !== 'gliderName' && !name?.startsWith('is')) {
        element.disabled = true;
        }
    });
}

function enableAllFields(formElement) {
    const elements = formElement.querySelectorAll('input, select, textarea, button');

    elements.forEach(element => {
        element.disabled = false;
    });
}

const activeSensorNames = new Set();

function addSensor() {
    const select = document.getElementById('sensor-model');
    const model = select.value;
    if (!model) return alert("Please select a sensor model");
    const modelName = select.options[select.selectedIndex].text;

    if (activeSensorNames.has(modelName)) {
        alert(`Le capteur "${modelName}" a déjà été ajouté. Veuillez choisir un autre modèle.`);
        return; // Stop the function if duplicate
    }

    const container = document.getElementById('sensor-list-container');

    let block = document.createElement('div');
    block.className = 'border rounded p-2 mb-2 bg-light group';
    block.style.display = 'block';
    block.innerHTML = `
    <h4  class= "group-header" onclick = "const group = this.parentElement;content=group.querySelector('.group-content');if(content.style.display == 'block'){content.style.display = 'none';}else{content.style.display = 'block';} console.log('yoplait');let arrow = group.querySelector('.arrow'); if(content.style.display == 'none'){arrow.style.transform = 'rotate(0deg)';}else{arrow.style.transform = 'rotate(90deg)';}" style="display: block;"><span class="arrow">&#9656;</span> ${modelName}</h4>
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
                <input type="file" class="form-control" id="capteurs[${sensorIndex}][file_calib]" name="capteurs[${sensorIndex}][file_calib]" multiple  onchange="handleFileSelection(this.id, 'capteurs[${sensorIndex}][file_list]')">
            </section>
            <section class="mt-2">
                <div id="capteurs[${sensorIndex}][file_list]" class="file-list"></div>
            </section>
            <section class="mt-2">
                <label>Select parameters:</label>
                <button type="button" class="btn btn-primary btn-sm" onclick="toggleParams(${model})">Toggle parameters</button>
            </section>
            <section class="mt-2 text-end">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeSensor(${sensorIndex})">Remove</button>
            </section>
            <input type="hidden" sensor="${model}" required="required" alert="You must choose the parameters or give the glider variable name for ${model}" value=""  name="capteurs[${model}][parameter]" value="${model}">
            <div id="parameters-${model}">
            </div>
        </div>
        
    `;

    if (!block) return alert("le block est null");
    if (!container) return alert("le container est null");
    container.appendChild(block);
    handleFileSelection("capteurs[" + sensorIndex + "][file_calib]", "capteurs[" + sensorIndex + "][file_list]");
    fillDivParams(model);
    sensorIndex++;
}
window.addSensor = addSensor;

async function fillDivParams(model) {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/decrire-capteur/' + model); // Remplacez par l'URL de votre route
        if (!response.ok) {
            throw new Error(`Erreur HTTP ! Statut : ${response.status}`);
        }
        const html = await response.text();

        const targetDiv = document.getElementById('parameters-' + model);
        if (targetDiv) {
            targetDiv.innerHTML = html;
            targetDiv.style.display = 'none';
        }
    } catch (error) {
        console.error("Erreur lors du chargement de la vue Blade :", error);
    }
}

function toggleParams(model) {
    const targetDiv = document.getElementById('parameters-' + model);
    if (!targetDiv) {
        console.error(`Div with id 'parameters-${model}' not found.`);
        return;
    }
    targetDiv.style.display = 'block';
}
window.toggleParams = toggleParams;



document.getElementById('reset-form').addEventListener('click', function () {
    
    // Vider la liste des capteurs
    document.getElementById('sensor-list-container').innerHTML = '';
});

function removeSensor(index) {
    const sensorList = document.getElementById('sensor-list-container'); // Correction ici: 'sensor-list' devient 'sensor-list-container'
    const sensorToRemove = sensorList.querySelector(`.sensor-item[data-index="${index}"]`);
    if (sensorToRemove) {
        const modelName = sensorToRemove.dataset.modelName; // Récupère le nom du modèle stocké

        if (modelName && activeSensorNames.has(modelName)) {
            activeSensorNames.delete(modelName);
            console.log("Capteur supprimé : " + modelName + ", Capteurs actifs :", Array.from(activeSensorNames));
        }

        sensorList.removeChild(sensorToRemove);
    }
}
window.removeSensor = removeSensor;
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
    const deploymentNameInput = document.getElementById('deploymentName');
    const hidden = document.getElementById('deploymentName_id');

    const suggestionsList = document.createElement('ul');
    suggestionsList.id = 'suggestionsDeploymentName';
    suggestionsList.style.position = 'absolute';
    suggestionsList.style.background = 'white';
    suggestionsList.style.border = '1px solid #ccc';
    suggestionsList.style.zIndex = '9999';
    suggestionsList.style.listStyle = 'none';
    suggestionsList.style.padding = '0';
    suggestionsList.style.margin = '0';
    suggestionsList.style.maxHeight = '150px';
    suggestionsList.style.overflowY = 'auto';

    deploymentNameInput.parentNode.appendChild(suggestionsList);

    const wrapper = deploymentNameInput.closest('.autocomplete-wrapper');
    wrapper.style.position = 'relative';
    wrapper.style.overflow = 'visible';

    // Positionner la liste juste sous le champ input
    suggestionsList.style.top = `${deploymentNameInput.offsetTop + deploymentNameInput.offsetHeight}px`;
    suggestionsList.style.left = `${deploymentNameInput.offsetLeft}px`;

    // Données injectées dans la page via Blade
    const deployments = JSON.parse(document.getElementById('deployments-data').textContent);

    deploymentNameInput.addEventListener('input', () => {
        const value = deploymentNameInput.value.trim();
        const gliderId = document.getElementById('gliderName_id').value;
        hidden.value = ''; // reset

        suggestionsList.innerHTML = '';

        if (!gliderId) {
            suggestionsList.innerHTML = '<li style="padding: 5px; color: #888;">Select a glider first</li>';
            suggestionsList.style.display = 'block';
            return;
        }

        const filteredDeployments = deployments.filter(d => d.glider_id == gliderId);

        // Match exact ?
        const exactMatch = filteredDeployments.find(d => d.name.toLowerCase() === value.toLowerCase());
        if (exactMatch) {
            hidden.value = exactMatch.deployment_id;
            suggestionsList.style.display = 'none';
            return;
        }

        const fuse = new Fuse(filteredDeployments, {
            keys: ['name'],
            threshold: 0.3,
        });

        const results = fuse.search(value);

        if (value === '' || results.length === 0) {
            suggestionsList.style.display = 'none';
            return;
        }

        results.slice(0, 5).forEach(result => {
            const item = document.createElement('li');
            item.textContent = result.item.name;
            item.dataset.id = result.item.deployment_id;
            item.style.padding = '5px';
            item.style.cursor = 'pointer';

            item.addEventListener('click', () => {
                deploymentNameInput.value = result.item.name;
                hidden.value = result.item.deployment_id;
                suggestionsList.innerHTML = '';
                suggestionsList.style.display = 'none';
            });

            suggestionsList.appendChild(item);
        });

        suggestionsList.style.display = 'block';
    });

    // Cacher les suggestions si on clique ailleurs
    document.addEventListener('click', (e) => {
        if (!suggestionsList.contains(e.target) && e.target !== deploymentNameInput) {
            suggestionsList.innerHTML = '';
            suggestionsList.style.display = 'none';
        }
    });
}


document.getElementById('gliderName').addEventListener('input', function () {
    const gliderName = document.getElementById('gliderName').value.trim();
    if (gliderName.length >= 1) {
        enableAllFields(document.getElementById('ego-inscription-form'));
    } else {
        disableAllExceptGlidernameAndIsFields(document.getElementById('ego-inscription-form'));
    }
});

function setSelectValue(selectElement, value) {
    // Vérifie si l'option existe déjà
    let option = Array.from(selectElement.options).find(opt => opt.value === value);
    if (!option) {
        // Crée une nouvelle option si elle n'existe pas
        option = new Option(value, value);
        selectElement.appendChild(option);
    }
    selectElement.value = value;
}

function getInstituteNameById(id) {
    const institutes = JSON.parse(document.getElementById('groupes-data').textContent);
    const found = institutes.find(inst => inst.id == id);
    return found ? found.nom : '';
}


function suggestGliderName() {
    const input = document.getElementById('gliderName');
    const hidden = document.getElementById('gliderName_id');
    const wmoInput = document.getElementById('WMONumber');
    const owningInstituteId = document.getElementById('owningInstitute_id');
    const owningInstitute = document.getElementById('owningInstitute');
    const serialNumberInput = document.getElementById('gliderSerialNumber');
    const platformFamilySelect = document.getElementById('platformFamily');
    const platformTypeSelect = document.getElementById('platformType');
    const suggestionsList = document.createElement('ul');
    suggestionsList.id = 'suggestionsGliderName';
    suggestionsList.style.position = 'absolute';
    suggestionsList.style.background = 'white';
    suggestionsList.style.border = '1px solid #ccc';
    suggestionsList.style.zIndex = '1000';
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
    const gliders = JSON.parse(document.getElementById('gliders-data').textContent);
    const fuse = new Fuse(gliders, {
        keys: ['name'],
        threshold: 0.3,
    });
    input.addEventListener('input', ()=>{
        const value = input.value.trim();
        hidden.value = ''; // ← Toujours réinitialiser d'abord
        wmoInput.value = ''; // Réinitialiser le champ WMO
        owningInstituteId.value = ''; // Réinitialiser l'ID de l'institut
        owningInstitute.value = ''; // Réinitialiser le nom de l'institut
        serialNumberInput.value = ''; // Réinitialiser le numéro de série
        setSelectValue(platformFamilySelect, ''); // Réinitialiser la famille de la plateforme
        setSelectValue(platformTypeSelect, ''); // Réinitialiser le type de plateforme

        // Rechercher un match exact
        const exactMatch = gliders.find(g => g.name.toLowerCase() === value.toLowerCase());
        if (exactMatch) {
            hidden.value = exactMatch.glider_id;
            wmoInput.value = exactMatch.WMO_platform_code || 'YYYY';
            setSelectValue(platformFamilySelect, exactMatch.family || 'YYYYY');
            populatePlatformType(exactMatch.family);
            setSelectValue(platformTypeSelect, exactMatch.type || '');
            owningInstituteId.value = exactMatch.owner_id || 'YYYYYY';
            serialNumberInput.value = exactMatch.no_serie || 'YYYYYY';
            owningInstitute.value = getInstituteNameById(owningInstituteId.value) || '';
            suggestionsList.innerHTML = '';
            suggestionsList.style.display = 'none';
            wmoInput.disabled = true; // Désactiver le champ WMO si un glider est sélectionné
            owningInstitute.disabled = true; // Désactiver le champ Institut si un glider est
            serialNumberInput.disabled = true; // Désactiver le champ Numéro de série si un glider est sélectionné
            platformFamilySelect.disabled = true; // Désactiver la famille de la plateforme si un glider est sélectionné
            platformTypeSelect.disabled = true; // Désactiver le type de plateforme si un glider est sélectionné
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
            item.dataset.id = result.item.glider_id;
            item.addEventListener('click', () => {
                input.value = result.item.name;
                hidden.value = result.item.glider_id;
                wmoInput.value = result.item.WMO_platform_code || 'YYYY';
                setSelectValue(platformFamilySelect, result.item.family || 'YYYYY');
                populatePlatformType(result.item.family);
                setSelectValue(platformTypeSelect, result.item.type || '');
                owningInstituteId.value = result.item.owner_id || 'YYYYYY';
                serialNumberInput.value = result.item.no_serie || 'YYYYYY';
                owningInstitute.value = getInstituteNameById(owningInstituteId.value) || '';
                suggestionsList.innerHTML = '';
                suggestionsList.style.display = 'none';
                wmoInput.disabled = true; // Désactiver le champ WMO si un glider est sélectionné
                owningInstitute.disabled = true; // Désactiver le champ Institut si un glider est
                serialNumberInput.disabled = true; // Désactiver le champ Numéro de série si un glider est sélectionné
                platformFamilySelect.disabled = true; // Désactiver la famille de la plateforme si un glider est sélectionné
                platformTypeSelect.disabled = true; // Désactiver le type de plateforme si un glider est sélectionné
            });
            suggestionsList.appendChild(item);
        });
        suggestionsList.style.display = 'block';
    })
    document.addEventListener('click', (e) => {
        if (!suggestionsList.contains(e.target) && e.target !== input) {
            suggestionsList.innerHTML = '';
            suggestionsList.style.display = 'none';
        }
    });
}

function handleFileSelection(inputId, fileListDivId) {
    const inputElement = document.getElementById(inputId);
    const fileListDiv = document.getElementById(fileListDivId);
    
    if (!inputElement || !fileListDiv) {
        console.error("L'élément input ou le div de liste de fichiers n'a pas été trouvé.");
        return;
    }

    let existingFiles = [];
    if (inputElement.dataset.files) {
        existingFiles = JSON.parse(inputElement.dataset.files);
    }

    const newFiles = Array.from(inputElement.files);

    const combinedFiles = [...existingFiles];
    newFiles.forEach(newFile => {
        const isDuplicate = combinedFiles.some(existingFile => 
            existingFile.name === newFile.name && existingFile.size === newFile.size
        );
        if (!isDuplicate) {
            combinedFiles.push({
                name: newFile.name,
                size: newFile.size,
                type: newFile.type
            });
        }
    });

    // Mettre à jour les fichiers stockés dans le dataset et l'affichage
    updateFileListDisplay(inputId, fileListDivId, combinedFiles);

    // Réinitialiser la valeur de l'input pour permettre de sélectionner les mêmes fichiers
    inputElement.value = ''; 
}
window.handleFileSelection = handleFileSelection;

function updateFileListDisplay(inputId, fileListDivId, filesToDisplay) {
    const inputElement = document.getElementById(inputId);
    const fileListDiv = document.getElementById(fileListDivId);

    if (!inputElement || !fileListDiv) {
        console.error("L'élément input ou le div de liste de fichiers n'a pas été trouvé.");
        return;
    }

    // Stocker les fichiers combinés dans un dataset pour la persistance
    inputElement.dataset.files = JSON.stringify(filesToDisplay);

    fileListDiv.innerHTML = ''; // Nettoyer la liste existante
    if (filesToDisplay.length > 0) {
        const ul = document.createElement('ul');
        filesToDisplay.forEach((file, index) => {
            const li = document.createElement('li');
            li.textContent = file.name;

            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'X';
            deleteButton.title = `Supprimer ${file.name}`;
            deleteButton.className = 'delete-file-button'; // Pour le style CSS
            // Passer l'index du fichier pour le supprimer facilement
            deleteButton.onclick = () => removeFile(inputId, fileListDivId, index);

            li.appendChild(deleteButton);
            ul.appendChild(li);
        });
        fileListDiv.appendChild(ul);
    } else {
        fileListDiv.textContent = 'Aucun fichier sélectionné.';
    }
}

window.updateFileListDisplay = updateFileListDisplay;

function removeFile(inputId, fileListDivId, fileIndexToRemove) {
    const inputElement = document.getElementById(inputId);
    
    if (!inputElement) {
        console.error("L'élément input n'a pas été trouvé.");
        return;
    }

    let existingFiles = [];
    if (inputElement.dataset.files) {
        existingFiles = JSON.parse(inputElement.dataset.files);
    }

    // Supprimer le fichier à l'index spécifié
    const updatedFiles = existingFiles.filter((_, index) => index !== fileIndexToRemove);

    // Mettre à jour l'affichage avec la nouvelle liste de fichiers
    updateFileListDisplay(inputId, fileListDivId, updatedFiles);
}