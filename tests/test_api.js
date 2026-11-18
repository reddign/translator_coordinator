
const baseURL = "../api";

let passed = 0;
let failed = 0;

// Helper function to display results
function displayResult(testName, success, message = "") {

    const results = document.getElementById("results");

    const item = document.createElement("li");

    item.textContent =
        `${success ? "PASS" : "FAIL"}: ${testName} ${message}`;

    item.style.color = success ? "green" : "red";

    results.appendChild(item);

    if (success) {
        passed++;
    } else {
        failed++;
    }
}


// TEST 1: Retrieve countries
async function testGetCountries() {

    const testName = "Retrieve Countries";

    try {

        const response = await fetch(`${baseURL}/countries`);

        const data = await response.json();

        const success =
            response.status === 200 &&
            data.success === true &&
            Array.isArray(data.data) &&
            data.data.length > 0;

        displayResult(testName, success);

    } catch (error) {

        displayResult(testName, false, error.message);
    }
}


// TEST 2: Country not found
async function testCountryNotFound() {

    const testName = "Country Not Found";

    try {

        const response = await fetch(
            `${baseURL}/countries/999999999`
        );

        const data = await response.json();

        const success =
            response.status === 404 &&
            data.success === false;

        displayResult(testName, success);

    } catch (error) {

        displayResult(testName, false, error.message);
    }
}


// TEST 3: Invalid user registration
async function testInvalidRegistration() {

    const testName = "Invalid Email Registration";

    try {

        const response = await fetch(
            `${baseURL}/users/register`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    first_name: "Test",
                    last_name: "Student",
                    email: "invalid-email",
                    password: "TestPassword123!",
                    original_country_id: 1
                })
            }
        );

        const data = await response.json();

        const success =
            response.status === 400 &&
            data.success === false;

        displayResult(testName, success);

    } catch (error) {

        displayResult(testName, false, error.message);
    }
}


// Run all tests
async function runTests() {

    passed = 0;
    failed = 0;

    document.getElementById("results").innerHTML = "";

    await testGetCountries();
    await testCountryNotFound();
    await testInvalidRegistration();

    document.getElementById("summary").textContent =
        `Tests Passed: ${passed} | Tests Failed: ${failed}`;
}
