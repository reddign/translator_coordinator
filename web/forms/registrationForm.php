<style>
    .register-page {
        background-color: #6aaee2;
        padding: 30px;
        border-radius: 12px;
        min-height: 80vh;
    }

    .register-page h1 {
        color: #fdfdfd;
        font-weight: bold;
    }

    .register-page form {
        background-color: #ffffff;
        padding: 25px;
        border-radius: 10px;
        max-width: 500px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .register-page input,
    .register-page select {
        padding: 10px;
        border: 1px solid #4a87b6;
        border-radius: 5px;
        margin-top: 5px;
        max-width: 100%;
    }

    .register-page input[type="submit"] {
        background-color: #6bb6e8;
        color: white;
        border: none;
        cursor: pointer;
        padding: 10px 22px;
        border-radius: 5px;
    }

    .register-page input[type="submit"]:hover {
        background-color: #428fc5;
    }
</style>

<div class="register-page">
<H1>Register</H1>
<p>Meet new people. Share your culture. Different backgrounds, one world.</p>
    <BR>
<div class='w3-section'>
    <form action="processes/register.php" method="POST">
        First Name: <input type='text' name='first'><BR><BR>
        Last Name: <input type='text' name='last'><BR><BR>
        Email: <input type='text' name='email'><BR><BR>
        
        Country of Origin:
        <select name='country_origin'>
            <option>Choose Country</option>
            <option value="1" selected>United States of America</option>
            <?php
                //Need a dynamic drop down list of countries
                // Define the API URL
                $countryDataURL = $mainURL."/api/countries";
                $countryResponse = getJSONFromURL($countryDataURL);
                $countries = $countryResponse["data"];

                foreach($countries as $country){
                    echo "<option value='".$country["COUNTRY_ID"]."'>";
                    echo $country["COUNTRY_NAME"];
                    echo "</option>";
                }
            ?>
        </select><br>

        Spoken Language:
        <select name='language_origin'>
            <option>Choose Language</option>
            <option value="1" selected>English</option>
            <?php
                // Language API
                $languageDataURL = $mainURL."/api/languages";
                $languageResponse = getJSONFromURL($languageDataURL);
                $languages = $languageResponse["data"];

                foreach($languages as $language){
                    echo "<option value='".$language["LANGUAGE_ID"]."'>";
                    echo $language["LANGUAGE_NAME"];
                    echo "</option>";
                }
            ?>
        </select><br>

        Password: <input type='password' name='pass'><BR><BR>
        <input type='submit' value='Register'><BR>
    </form>
</div>
