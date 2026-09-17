<?php
include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";

// Get the list of language IDs from the 'language' query parameter
$languageIds = isset($_GET['language']) ? explode(',', $_GET['language']) : array();
$isResult = !empty($languageIds);
$users = array();

if ($isResult) {
    // Prepare the API URL to fetch users by language IDs
    $apiUrl = $mainURL . "/api/languages/users_by_language/" . implode(',', $languageIds);

    // Fetch the data from the API
    $response = getJSONFromURL($apiUrl);

    // Check if the response is valid and contains user data
    if (isset($response['data']) && is_array($response['data'])) {
        $users = $response['data'];
    }
}

?>

<script>
    // Create script to append new language input to div
    function addLanguageInput() {
        var container = document.getElementById("language-inputs");

        var newInput = document.createElement("input");
        newInput.type = "text";
        newInput.name = "language[]";
        newInput.placeholder = "Enter a language name";
        container.appendChild(newInput);
        container.appendChild(document.createElement("br"));
    }
</script>
<h2>Translator Coordinator - Search Feature</h2>
        <div class="w3-section" id="search">
            <span class="name"><b>Search for users by a language:</b></span>
            <form action="search.php" method="get">
                <div id="language-inputs">
                    <input type="text" name="language[]" placeholder="Enter a language name">
                    <br/>
                </div>
                <button type="button" onclick="addLanguageInput()">Add Language</button>
                <br/>
                <input type="submit" value="Search">
            </form>
        </div>
        
        <?php if (count($users) > 0): ?>
            // Display if users > 0
            
            <div class="w3-section" id="search-results">
                <h3>Search Results:</h3>
                <ul>
                    <?php foreach ($users as $user): ?>
                        <li>
                            <a href="profile.php?id=<?php echo $user['USER_ID']; ?>">
                                <?php echo $user['FIRST_NAME'] . ' ' . $user['LAST_NAME']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <BR><BR><BR><BR>

<?php



include "includes/footer.php";

?>