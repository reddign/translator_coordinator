<?php
require_once __DIR__ . '/../../includes/config.php';
include "includes/header.php";
include "includes/navbar.php";

function url(){
    $baseFilePath = "/translator_coordinator/web";
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $baseFilePath;
}

function getJSONFromURL($url){
    $json_data = @file_get_contents($url);

    if ($json_data === FALSE) {
        return ['data' => []];
    }

    $response = json_decode($json_data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['data' => []];
    }

    return $response;
}


$id = $_GET['id'] ?? null;
if (!empty($id)) {
    $countryDataURL  = "{$mainURL}/api/countries/{$id}";
    $currencyDataURL = "{$mainURL}/api/currencies?countryid={$id}";
    $languageDataURL = "{$mainURL}/api/languages?countryid={$id}";

    $countryResponse  = getJSONFromURL($countryDataURL);
    $currencyResponse = getJSONFromURL($currencyDataURL);
    $languageResponse = getJSONFromURL($languageDataURL);

    $countries  = $countryResponse['data'] ?? [];
    $currencies = $currencyResponse['data'] ?? [];
    $languages  = $languageResponse['data'] ?? [];

    $countryName = $countries[0]['COUNTRY_NAME'] ?? '';
    $countryId   = $countries[0]['COUNTRY_ID'] ?? $id;
    $languageId  = $languages[0]['LANGUAGE_ID'] ?? null;
}

function searchTranslators($filters = []): array
{
    $baseURL = $GLOBALS['mainURL'] ?? '';

    if (!is_array($filters)) {
        $filters = ['user' => $filters];
    }

    $params = array_filter([
        'user'     => $filters['user'] ?? null,
        'language' => $filters['language'] ?? null,
        'country'  => $filters['country'] ?? null,
        'region'   => $filters['region'] ?? null,
        'group'    => $filters['group'] ?? null,
    ], fn($v) => $v !== null && $v !== '');

    $queryString = !empty($params) ? '?' . http_build_query($params) : '';
    $userDataURL = "{$baseURL}/api/users{$queryString}";

    $response = getJSONFromURL($userDataURL);

    return $response['data'] ?? [];
}
?>