
const searchBar = document.getElementById('search-bar');
const resultsDiv = document.getElementById('results');
const languageSelect = document.getElementById('language-select');
const regionSelect = document.getElementById('region-select');
const countrySelect = document.getElementById('country-select');

let debounceTimer;
let controller;

function searchUsers() {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {

        const params = new URLSearchParams({
            ajax: '1',
            query: searchBar.value.trim(),
            language: languageSelect.value,
            region: regionSelect.value,
            country: countrySelect.value
        });

        if (!searchBar.value.trim() &&
            !languageSelect.value &&
            !regionSelect.value &&
            !countrySelect.value) {

            if (controller) controller.abort();

            resultsDiv.innerHTML = '';
            return;
        }

        if (controller) controller.abort();

        controller = new AbortController();

        fetch('search.php?' + params.toString(), {
            signal: controller.signal
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Search failed');
            }

            return response.text();
        })
        .then(data => {
            resultsDiv.innerHTML = data;
        })
        .catch(error => {
            if (error.name !== 'AbortError') {
                resultsDiv.textContent = 'Unable to load search results.';
            }
        });

    }, 300);
}

searchBar.addEventListener('input', searchUsers);
languageSelect.addEventListener('change', searchUsers);
regionSelect.addEventListener('change', searchUsers);
countrySelect.addEventListener('change', searchUsers);
