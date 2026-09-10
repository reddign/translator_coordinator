<?php
    //USER PROFILE PAGE
    //Written with the assumption that all of this will be converted into php code

    //TODO: Access the API to get user language information
    $USER_LANGUAGES = $mainURL."/api/users/{$userId}";
    //TODO: Add user userId and userLanguages to the database

    function getUserLanguages(userId){
        //Takes in a user's id and returns their languages
        return userLanguages;
    }

    function selectLanguage(userLanguages){
        //Takes all the user languages and selects just one
        return selectedLanguage;
    }

    //TODO: update language on profile function
    function updateLanguage(selectedLanguage){
        //takes in the selected language and updates it
        return updatedLanguage;
    }

    //TODO: delete language from profile
    function deleteLanguage(selectedLanguage){
        //takes in the selected language and removes it from the users languages
    }
?>