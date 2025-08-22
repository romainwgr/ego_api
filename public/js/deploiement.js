"use strict";
import fuse from "fuse.js";

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