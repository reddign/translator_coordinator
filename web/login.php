<?php
require_once __DIR__ . "/../includes/config.php";
//redirect to login page
$page = $_GET["page"] ?? "login";
if ($page !== "login" && $page !== "register" && $page !== "forgot-password") {
    require_once __DIR__ . "/processes/authenticate.php";
}

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";

function e($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}

$error = $_SESSION["error"]??"";
unset($_SESSION["error"]);



echo "<div class='gerror'>" . e($error) . "</div>";

if($page=="login" || $page=="forgot-password"){
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
    echo "<b>Name:</b> " . e($profile_data["first_name"]) . " " . e($profile_data["last_name"]) . "<BR>";
    echo "<b>Country of Origin:</b> " . e($profile_data["original_country_name"]) . "<BR>";
    echo "<b>Email:</b> <a href='mailto:" . e($profile_data["email"]) . "'>" . e($profile_data["email"]) . "</a><BR>";
    echo "<b>Role:</b> " . e($profile_data["role"]) . "<BR>";
    echo "<b>Member Since:</b> " . e($profile_data["date_registered"]) . "<BR>";
    echo "</div>";

    echo "<form action='processes/logout.php' method='POST' style='display:inline'>
        <button type='submit'>Log out</button>
      </form>";

    //Side Column Data
    echo "<div style='float:right;margin-right:50px;'>";
    //Languages
    echo "<h2>Spoken Languages</h2>";
        if (empty($spoken_languages)) {
        echo "No languages listed.";
    } else {
        foreach ($spoken_languages as $lang) {
            switch ($lang['proficency_level']) { # show proficiency level as a description instead of a number
                case 1:
                    $proficiencyDescription = "Beginner";
                    break;
                case 2:
                    $proficiencyDescription = "Elementary";
                    break;
                case 3:
                    $proficiencyDescription = "Intermediate";
                    break;
                case 4:
                    $proficiencyDescription = "Upper Intermediate";
                    break;
                case 5:
                    $proficiencyDescription = "Native/Fluent";
                    break;
                default:
                    $proficiencyDescription = "Unknown";
            }

            $languageName = e($lang['LANGUAGE_NAME']);
            $languageId = (int)$lang['LANGUAGE_ID'];

            echo "<div style='margin-bottom:10px;'>";
            echo e($lang['LANGUAGE_NAME'])." - ".$proficiencyDescription . " ";

            echo "
                    <form action='processes/removeLanguage.php' method='POST' style='display:inline'>
                    <input type='hidden' name='language_id' value='{$languageId}'>
                    <button type='submit'
                            onclick='return confirm(\"Are you sure you want to delete this language?\");'>
                        Remove
                    </button>
                </form>
            ";

            echo "</div>";
        }
    }
        
    # form to add a new language to the user's profile
    echo "
    <form action='processes/addlanguage.php' method='POST'>
        <button type='button' id='toggleSelectBtn' aria-expanded='false' aria-controls='countryWrapper'>+</button>

        <div id='countryWrapper' class='form-group' hidden>
            <select name='language_id'>
            <option value='' disabled selected>Choose Language</option>";
                // Language API
                $languageDataURL = $mainURL."/api/languages";
                $languageResponse = getJSONFromURL($languageDataURL);
                $languages = $languageResponse["data"];

                foreach($languages as $language){
                    echo "<option value='".$language["LANGUAGE_ID"]."'>";
                    echo $language["LANGUAGE_NAME"];
                    echo "</option>";
                }
        echo "
        </select>
        <select name='proficiency_level'>
            <option value='' disabled selected>Choose Proficiency Level</option>
            <option value='1'>1 - Beginner</option>
            <option value='2'>2 - Elementary</option>
            <option value='3'>3 - Intermediate</option>
            <option value='4'>4 - Upper Intermediate</option>
            <option value='5'>5 - Advanced/Native</option>
        </select>
        <br>
        </div>

        <button type='submit'>Submit</button>
    </form>";
    echo "<BR><BR><BR>";
    
    echo "</div>";
}

// //USER PROFILE PAGE
//     //Written with the assumption that all of this will be converted into php code

//     //TODO: Access the API to get user language information
        //$userId = $_SESSION[userid]
//      $USER_LANGUAGES = $mainURL."/api/users/{$userId}/languages";
//     //TODO: Add user userId and userLanguages to the database

//     function getUserLanguages(userId){
//         //Takes in a user's id and returns their languages
//         return userLanguages;
//     }

//     function selectLanguage(userLanguages){
//         //Takes all the user languages and selects just one
//         return selectedLanguage;
//     }

//     //TODO: update language on profile function
//     function updateLanguage(selectedLanguage){
//         //takes in the selected language and updates it
//         return updatedLanguage;
//     }

//     //TODO: delete language from profile
//     function deleteLanguage(selectedLanguage){
//         //takes in the selected language and removes it from the users languages
//     }
    

include "includes/footer.php";
?>
<style>
.form-group {
    margin-top: 12px;
    margin-bottom: 16px;
  }
</style>

<script>
  const toggleBtn = document.getElementById('toggleSelectBtn');
  const selectWrapper = document.getElementById('countryWrapper');

  toggleBtn.addEventListener('click', function () {
    // Toggle the HTML 'hidden' attribute
    const isHidden = selectWrapper.hasAttribute('hidden');

    if (isHidden) {
      selectWrapper.removeAttribute('hidden');
      toggleBtn.textContent = '−';
      toggleBtn.setAttribute('aria-expanded', 'true');
    } else {
      selectWrapper.setAttribute('hidden', '');
      toggleBtn.textContent = '+';
      toggleBtn.setAttribute('aria-expanded', 'false');
    }
  });
</script>