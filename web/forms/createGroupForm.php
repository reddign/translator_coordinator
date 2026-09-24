<!--
    createGroupForm.php
    Standalone "Create Group" page — built with W3.CSS to match the
    rest of the project. Follows the same plain-POST pattern as
    loginForm.php / processes/login.php: no JS fetch, full page
    reload on submit, errors come back via $_SESSION["error"].

    STATUS NOTES:
    - "description" field is included in the UI already, ready for
      when the groups table gets that column added.
    - Region picker dropped (not part of the data model).
    - Country and language pickers are <select> dropdowns listing
      every available option, stubbed with placeholder data for
      now — swap PLACEHOLDER_COUNTRIES / PLACEHOLDER_LANGUAGES for
      real fetch() calls to api/countries and api/languages once
      wiring that in.
-->

<?php
// If your team's other pages show session-based error messages
// (login.php does this), match that convention here:
$groupError = $_SESSION["group_error"] ?? "";
$_SESSION["group_error"] = "";
?>

<div class="w3-container w3-padding-32" style="max-width:600px; margin:auto;">

    <h2>Create a Group</h2>

    <?php if ($groupError): ?>
        <div class="w3-panel w3-pale-red w3-border w3-round">
            <p><?php echo htmlspecialchars($groupError); ?></p>
        </div>
    <?php endif; ?>

    <form id="createGroupForm" action="processes/creategroup.php" method="POST" class="w3-card-4 w3-padding w3-round">

        <!-- Group Name -->
        <label class="w3-text-grey"><b>Group Name</b></label>
        <input class="w3-input w3-border w3-round w3-margin-bottom"
               type="text" name="groupName" id="groupName" required>

        <!-- Group Description -->
        <label class="w3-text-grey"><b>Description</b></label>
        <textarea class="w3-input w3-border w3-round w3-margin-bottom"
                  name="groupDescription" id="groupDescription"
                  rows="3" placeholder="What's this group about?"></textarea>

        <!-- Associated Countries (tag picker) -->
        <label class="w3-text-grey"><b>Associated Countries</b></label>
        <div id="countryTagContainer" class="w3-border w3-round w3-padding-small w3-margin-bottom">
            <div id="countryChips" class="w3-margin-bottom"></div>
            <select class="w3-select w3-border w3-round" id="countrySelect">
                <option value="" disabled selected>Select a country...</option>
            </select>
        </div>
        <!-- Synced by JS before submit; PHP reads $_POST['countryIds'] as a comma-separated string -->
        <input type="hidden" name="countryIds" id="countryIdsField">

        <!-- Associated Languages (tag picker) -->
        <label class="w3-text-grey"><b>Associated Languages</b></label>
        <div id="languageTagContainer" class="w3-border w3-round w3-padding-small w3-margin-bottom">
            <div id="languageChips" class="w3-margin-bottom"></div>
            <select class="w3-select w3-border w3-round" id="languageSelect">
                <option value="" disabled selected>Select a language...</option>
            </select>
        </div>
        <!-- Synced by JS before submit; PHP reads $_POST['languageIds'] as a comma-separated string -->
        <input type="hidden" name="languageIds" id="languageIdsField">

        <button type="submit" class="w3-button w3-blue w3-round w3-block">
            Create Group
        </button>

    </form>
</div>

<style>
.w3-padding-small { padding: 6px; }
</style>

<script>
/*
------------------------------------------------------------
TAG PICKER STUB — placeholder data for now.
Swap PLACEHOLDER_COUNTRIES / PLACEHOLDER_LANGUAGES for real
fetch() calls to api/countries and api/languages when ready.
------------------------------------------------------------
*/
/*
------------------------------------------------------------
Fetch real country/language data from the server-side proxy
files (processes/getCountries.php, processes/getLanguages.php),
which in turn call GET /api/countries and GET /api/languages.
------------------------------------------------------------
Confirmed response shape (from api/languages/index.php):
  { "success": true, "count": N, "data": [ {LANGUAGE_ID, LANGUAGE_NAME}, ... ] }

api/countries is ASSUMED to follow the same pattern with
COUNTRY_ID / COUNTRY_NAME — verify against the real
api/countries/index.php and adjust the idField/nameField
arguments below if the column names differ.
*/
let selectedCountryIds = [];
let selectedLanguageIds = [];

async function loadDropdownData(url, selectId, chipsId, hiddenFieldId, selectedArray, idField, nameField) {
    try {
        const response = await fetch(url);
        const result = await response.json();

        if (!result.success) {
            console.error(`${url} returned an error:`, result.message);
            return;
        }

        // Normalize into {id, name} regardless of the source column names
        const items = result.data.map(item => ({
            id: item[idField],
            name: item[nameField]
        }));

        setupTagDropdown(selectId, chipsId, hiddenFieldId, items, selectedArray);
    } catch (err) {
        console.error(`Failed to load data from ${url}:`, err);
    }
}

function setupTagDropdown(selectId, chipsId, hiddenFieldId, dataSource, selectedArray) {
    const select = document.getElementById(selectId);
    const chips = document.getElementById(chipsId);
    const hiddenField = document.getElementById(hiddenFieldId);

    // Populate the dropdown with every available option
    dataSource.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = item.name;
        select.appendChild(option);
    });

    select.addEventListener('change', function () {
        const id = parseInt(select.value, 10);
        if (!id || selectedArray.includes(id)) return;

        const item = dataSource.find(d => d.id === id);
        selectedArray.push(id);
        addChip(item, chips, selectedArray, hiddenField, select);

        // Remove the picked option from the dropdown and reset to placeholder
        select.querySelector(`option[value="${id}"]`).remove();
        select.value = '';
    });
}

function addChip(item, chipsContainer, selectedArray, hiddenField, select) {
    const chip = document.createElement('span');
    chip.className = 'w3-tag w3-round w3-blue w3-margin-right w3-margin-bottom';
    chip.style.display = 'inline-block';
    chip.innerHTML = item.name + ' <span style="cursor:pointer;">&times;</span>';
    chip.querySelector('span').onclick = function () {
        const idx = selectedArray.indexOf(item.id);
        if (idx > -1) selectedArray.splice(idx, 1);
        chip.remove();
        hiddenField.value = selectedArray.join(',');

        // Add the option back to the dropdown so it can be picked again
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = item.name;
        select.appendChild(option);
    };
    chipsContainer.appendChild(chip);
    hiddenField.value = selectedArray.join(',');
}

loadDropdownData('../processes/groupProcesses/getCountries.php', 'countrySelect', 'countryChips', 'countryIdsField', selectedCountryIds, 'COUNTRY_ID', 'COUNTRY_NAME'); // confirmed against api/countries/index.php
loadDropdownData('../processes/groupProcesses/getLanguages.php', 'languageSelect', 'languageChips', 'languageIdsField', selectedLanguageIds, 'LANGUAGE_ID', 'LANGUAGE_NAME'); // confirmed against api/languages/index.php
</script>