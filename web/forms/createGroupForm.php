<!--
    createGroupForm.php
    Modal popup for "Create Group" — built with W3.CSS to match the
    rest of the project (see web/style.css and loginForm.php pattern).

    STATUS NOTES:
    - "description" field is included in the UI already, ready for
      when the groups table gets that column added.
    - "region" tag-picker is commented out below until the db admin
      confirms whether groups_to_regions gets created or region is
      dropped (derived from country instead).
    - Country and language pickers are stubbed with placeholder
      option data for now — step 3 will replace this with real
      fetch() calls to api/countries and api/languages.
-->

<!-- Trigger button — place this wherever the page already has its
     "Create Group" button, or use this as the button itself -->
<button class="w3-button w3-blue w3-round" onclick="openCreateGroupModal()">
    Create Group
</button>

<!-- ============ MODAL ============ -->
<div id="createGroupModal" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-top w3-round" style="max-width:500px;">

        <header class="w3-container w3-blue w3-round-top">
            <span onclick="closeCreateGroupModal()"
                  class="w3-button w3-display-topright w3-hover-red">&times;</span>
            <h2>Create a Group</h2>
        </header>

        <form id="createGroupForm" class="w3-container w3-padding-16" method="POST">

            <!-- Group Name -->
            <label class="w3-text-grey"><b>Group Name</b></label>
            <input class="w3-input w3-border w3-round w3-margin-bottom"
                   type="text" name="groupName" id="groupName" required>

            <!-- Group Description -->
            <label class="w3-text-grey"><b>Description</b></label>
            <textarea class="w3-input w3-border w3-round w3-margin-bottom"
                      name="groupDescription" id="groupDescription"
                      rows="3" placeholder="What's this group about?"></textarea>

            <!-- Associated Country (tag picker) -->
            <label class="w3-text-grey"><b>Associated Countries</b></label>
            <div id="countryTagContainer" class="w3-border w3-round w3-padding-small w3-margin-bottom">
                <div id="countryChips" class="w3-margin-bottom"></div>
                <input class="w3-input" type="text" id="countrySearchInput"
                       placeholder="Search countries..." autocomplete="off">
                <div id="countrySuggestions" class="w3-bar-block w3-white w3-card" style="display:none;"></div>
            </div>

            <!-- Associated Language (tag picker) -->
            <label class="w3-text-grey"><b>Associated Languages</b></label>
            <div id="languageTagContainer" class="w3-border w3-round w3-padding-small w3-margin-bottom">
                <div id="languageChips" class="w3-margin-bottom"></div>
                <input class="w3-input" type="text" id="languageSearchInput"
                       placeholder="Search languages..." autocomplete="off">
                <div id="languageSuggestions" class="w3-bar-block w3-white w3-card" style="display:none;"></div>
            </div>

            <!--
            ASSOCIATED REGION — on hold pending db admin decision.
            If groups_to_regions gets created, duplicate the country
            tag-picker block above with id="regionTagContainer" etc.
            If region is dropped (derived from country instead), this
            stays commented out permanently and region display happens
            read-only elsewhere, based on tagged countries.
            -->
            <!--
            <label class="w3-text-grey"><b>Associated Regions</b></label>
            <div id="regionTagContainer" class="w3-border w3-round w3-padding-small w3-margin-bottom">
                ...
            </div>
            -->

            <button type="submit" class="w3-button w3-blue w3-round w3-block">
                Create Group
            </button>

        </form>
    </div>
</div>

<style>
/* Hidden by default; toggled via JS below */
#createGroupModal { display: none; }
#createGroupModal.w3-show { display: block; }

.w3-padding-small { padding: 6px; }
</style>

<script>
function openCreateGroupModal() {
    document.getElementById('createGroupModal').classList.add('w3-show');
}

function closeCreateGroupModal() {
    document.getElementById('createGroupModal').classList.remove('w3-show');
}

// Click outside modal content to close
document.getElementById('createGroupModal').addEventListener('click', function (e) {
    if (e.target === this) closeCreateGroupModal();
});

// Escape key to close
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeCreateGroupModal();
});

/*
------------------------------------------------------------
TAG PICKER STUB — placeholder data for now.
Step 3 replaces PLACEHOLDER_COUNTRIES / PLACEHOLDER_LANGUAGES
with real fetch() calls to api/countries and api/languages,
and selectedCountryIds / selectedLanguageIds get sent as part
of the submit payload in step 4.
------------------------------------------------------------
*/
const PLACEHOLDER_COUNTRIES = [
    { id: 1, name: "Mexico" },
    { id: 2, name: "Spain" },
    { id: 3, name: "Argentina" }
];
const PLACEHOLDER_LANGUAGES = [
    { id: 1, name: "Spanish" },
    { id: 2, name: "English" },
    { id: 3, name: "Portuguese" }
];

let selectedCountryIds = [];
let selectedLanguageIds = [];

function setupTagPicker(inputId, suggestionsId, chipsId, dataSource, selectedArray) {
    const input = document.getElementById(inputId);
    const suggestions = document.getElementById(suggestionsId);
    const chips = document.getElementById(chipsId);

    input.addEventListener('input', function () {
        const query = input.value.toLowerCase();
        suggestions.innerHTML = '';
        if (!query) { suggestions.style.display = 'none'; return; }

        const matches = dataSource.filter(item =>
            item.name.toLowerCase().includes(query) &&
            !selectedArray.includes(item.id)
        );

        matches.forEach(item => {
            const option = document.createElement('div');
            option.className = 'w3-bar-item w3-button';
            option.textContent = item.name;
            option.onclick = function () {
                selectedArray.push(item.id);
                addChip(item, chips, selectedArray, dataSource, chipsId);
                input.value = '';
                suggestions.style.display = 'none';
            };
            suggestions.appendChild(option);
        });

        suggestions.style.display = matches.length ? 'block' : 'none';
    });
}

function addChip(item, chipsContainer, selectedArray) {
    const chip = document.createElement('span');
    chip.className = 'w3-tag w3-round w3-blue w3-margin-right w3-margin-bottom';
    chip.style.display = 'inline-block';
    chip.innerHTML = item.name + ' <span style="cursor:pointer;">&times;</span>';
    chip.querySelector('span').onclick = function () {
        const idx = selectedArray.indexOf(item.id);
        if (idx > -1) selectedArray.splice(idx, 1);
        chip.remove();
    };
    chipsContainer.appendChild(chip);
}

setupTagPicker('countrySearchInput', 'countrySuggestions', 'countryChips', PLACEHOLDER_COUNTRIES, selectedCountryIds);
setupTagPicker('languageSearchInput', 'languageSuggestions', 'languageChips', PLACEHOLDER_LANGUAGES, selectedLanguageIds);

/*
------------------------------------------------------------
SUBMIT HANDLER — placeholder payload for now (step 4 wires
this to processes/creategroup.php via fetch()).
------------------------------------------------------------
*/
document.getElementById('createGroupForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const payload = {
        groupName: document.getElementById('groupName').value,
        groupDescription: document.getElementById('groupDescription').value,
        countryIds: selectedCountryIds,
        languageIds: selectedLanguageIds
    };

    console.log('Create group payload:', payload);
    // Next step: fetch('processes/creategroup.php', { method: 'POST', ... })
});
</script>