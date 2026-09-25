<?php
require_once __DIR__ . "/../includes/config.php";


include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
$error = $_SESSION["error"]??"";
$page = $_GET["page"]??"login";


echo "<div class='gerror'>{$error}</div>";

if($page=="login"){
    require_once "forms/loginForm.php";
}else if($page=="register"){
    require_once "forms/registrationForm.php";
}else{
    $profile_data = $_SESSION["user"];
    $userId = $_SESSION['user']['userid'] ?? null;

    //Using api call to get user spoken languages
    $USER_LANGUAGES = $mainURL."/api/users/{$userId}/languages";
    $USER_LANGUAGES_RESPONSE = getJSONFromURL($USER_LANGUAGES);
    $spoken_languages = $USER_LANGUAGES_RESPONSE["data"];
    $language = $spoken_languages[0]['LANGUAGE_NAME'] ?? null;
    //Main column data
    echo "<div style='float:left'>";
    echo "<h1>User Profile</h1>";
    echo "<b>Name:</b>  {$profile_data["first_name"]} {$profile_data["last_name"]}<BR>";
    echo "<b>Country of Origin:</b>  {$profile_data["original_country_name"]}<BR>";
    echo "<b>Email:</b>  <a href='mailto:{$profile_data["email"]}'>{$profile_data["email"]}</a><BR>";
    echo "<b>Role:</b>  {$profile_data["role"]}<BR>";
    echo "<b>Member Since:</b>  {$profile_data["date_registered"]}<BR>";
    echo "</div>";
    //echo "{$profile_data["userid"]}<BR>";

    echo "<a href='processes/logout.php'>Log out</a>";

    //Side Column Data
    echo "<div style='float:right;margin-right:50px;'>";
    //Languages
    echo "<h2>Spoken Languages</h2>";
        if (empty($spoken_languages)) {
        echo "No languages listed.";
    } else {
        foreach ($spoken_languages as $lang) {
            echo $lang['LANGUAGE_NAME'];
            echo "<br>";
        }
    }

    //TODO: connect button clicks to API calls
    //TODO: Type in language and it will come up, perhaps autofill but that might be too ambitious right now.

    //using api call to get all languages
    // $all_languages = $mainURL."/api/languages?search=span";
    //This line is causing problems
    // $all_languages_response = getJSONFromURL($all_languages);
    // $languages = $all_languages_response["data"];
    echo '<form style="margin-top: 20px;">';
    echo '<label for="languageSelect">Language to add:</label> ';
    echo '<input id="allLangs" type="text" list="allLanguages">';
    // echo '<p> output: <span id="output"></span></p>';
    // ?>
    // <script>
    // document.getElementById('allLangs').addEventListener('input', function(){
    //     document.getElementById('output').textContent = this.value;
    // });
    // </script>
    // <?php
    // echo '<datalist id="allLanguages">';
    //     foreach ($languages as $langs) {
    //         echo "<option value='{$langs['LANGUAGE_ID']}'>{$langs['LANGUAGE_NAME']}</option>";
    //     }
    //     echo '</datalist>';
    echo '<button type="submit">Add Language</button>';
    echo '</form>';

    
    echo '<form id="deleteLanguageForm" style="margin-top: 20px;" onsubmit="event.preventDefault(); deleteLanguage( ' . (int)$userId . ');">';
    echo '<label for="languageSelect">Select a Language to Delete:</label> ';
    echo '<select name="languageId" id="languageSelection">';
        echo "<option value='0' selected> --Choose a language-- </option>";
        foreach ($spoken_languages as $langs) {
            echo "<option value='{$langs['LANGUAGE_ID']}'>{$langs['LANGUAGE_NAME']}</option>";
        }
    echo '</select>';
    echo '<button type="submit">Delete Language</button>';
    echo '</form>';
    ?>
    
    <script>
    function deleteLanguage(userId){
        const languageId = document.getElementById('languageSelection').value;

        if(!languageId || languageId === '0'){
            alert('Please select a language to delete.');
            return;
        }

        if(!confirm('Are you sure you want to delete this language from your profile?')){
            return;
        }

        const baseUrl = '<?php echo $mainURL; ?>';
        const delUrl = `${baseUrl}/api/users/${userId}/languages/${languageId}`;

        fetch(delUrl, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json'
            }
        }).then(response => response.json()).then(data => {
            if (data.success) {
                alert(data.message || 'Language successfully deleted.');
                location.reload();
            }else{
                alert('Error: ' + data.message);
            }
        }).catch(error => {
            console.error('Fetch error: ', error);
        });
    }
    </script>
    <?php

    //Countries
    echo "<h2>Countries of Interest</h2>";
    echo "<BR><BR><BR>";
    
    //Countries
    echo "<h2>Groups</h2>";
    echo "<BR><BR><BR>";
    echo "</div>";
}   

include "includes/footer.php";
?>