<?php

use WHMCS\Database\Capsule;
use WHMCS\Module\Registrar\tphost\Request;
use WHMCS\Domain\TopLevel\ImportItem;
use WHMCS\Results\ResultsList;

function tphost_MetaData()
{
    return array(
        'DisplayName' => 'The PowerHost',
        'APIVersion' => '1.0',
    );
}

function tphost_GetConfigArray()
{
    return array(
        "Description" => array("Type" => "System", "Value" => "Don't have a The PowerHost Account yet? Get one here: " . "<a href=\"https://thepowerhost.in/my/register.php\" target=\"_blank\">" . "https://thepowerhost.in</a>"),
        "email" => array("FriendlyName" => "Email Address:", "Type" => "text", "Size" => "25", "Default" => "", "Description" => "Enter your email address which you registered in our system."),        		
        "apikey" => array("FriendlyName" => "API Key:", "Type" => "text", "Size" => "25", "Default" => "", "Description" => "Enter your API Key."),
    );
}

function tphost_RegisterDomain($params)
{
    $params_to_send = [
        'firstname' => '',
        'lastname' => '',
        'companyname' => '',
        'address1' => '',
        'address2' => '',
        'city' => '',
        'state' => '',
        'postcode' => '',
        'country' => '',
        'fullphonenumber' => '',
        'ns1' => '',
        'ns2' => '',
        'ns3' => '',
        'ns4' => '',
        'ns5' => '',
        'email' => '',
        'domain' => '',
        'regperiod' => '',
    ];
    foreach ($params_to_send as $key => $value) {
        if (isset($params[$key])) {
            $params_to_send[$key] = $params[$key];
        }
    }
    $params_to_send['phonenumber'] = $params_to_send['fullphonenumber'];
    $params_to_send['action'] = 'register';
    $result = Request::call($params_to_send);
    if ($result['result'] == 'success') {
        return array("success" => "complete");
    } else {
        return array("error" => $result['data']);
    }
}

function tphost_TransferDomain($params)
{
    $params_to_send = [
        'firstname' => '',
        'lastname' => '',
        'companyname' => '',
        'address1' => '',
        'address2' => '',
        'city' => '',
        'state' => '',
        'postcode' => '',
        'country' => '',
        'fullphonenumber' => '',
        'ns1' => '',
        'ns2' => '',
        'ns3' => '',
        'ns4' => '',
        'ns5' => '',
        'email' => '',
        'domain' => '',
        'eppcode' => '',
        'regperiod' => '',
        'transfersecret' => '',
    ];
    foreach ($params_to_send as $key => $value) {
        if (isset($params[$key])) {
            $params_to_send[$key] = $params[$key];
        }
    }
    $params_to_send['eppcode'] = $params_to_send['transfersecret'];
    $params_to_send['phonenumber'] = $params_to_send['fullphonenumber'];
    $params_to_send['action'] = 'transfer';
    $result = Request::call($params_to_send);
    if ($result['result'] == 'success') {
        return array("success" => "complete");
    } else {
        return array("error" => $result['data']);
    }

}

function tphost_RenewDomain($params)
{
    $params_to_send['regperiod'] = $params['regperiod'];
    $params_to_send['domain'] = $params['domain'];
    $params_to_send['action'] = 'renew';
    $result = Request::call($params_to_send);
    if ($result['result'] == 'success') {
        return array("success" => "complete");
    } else {
        return array("error" => $result['data']);
    }
}

function tphost_GetNameservers($params)
{
    $params_to_send = [];
    $params_to_send['domain'] = $params['domain'];
    $params_to_send['action'] = 'getNameServers';
    $result = Request::call($params_to_send);
    if ($result['result'] != 'success') {
        return array('error' => $result['data']);
    }else{
        return $result['data'];
    }
}

function tphost_SaveNameservers($params)
{

    $postfields = [];
    $postfields['action'] = 'updateNameServers';
    $postfields['domain'] = $params['domain'];
    if ($params['ns1']) {
        $postfields["ns1"] = $params['ns1'];
    }else{
        return array('error' => "Nameserver 1 Are required");
    }
    if ($params['ns2']) {
        $postfields["ns2"] = $params['ns2'];
    }else{
        return array('error' => "Nameserver 2 Are required");
    }
    if ($params['ns3']) {
        $postfields["ns3"] = $params['ns3'];
    }
    if ($params['ns4']) {
        $postfields["ns4"] = $params['ns4'];
    }
    if ($params['ns5']) {
        $postfields["ns5"] = $params['ns5'];
    }
    $response = Request::call($postfields);
    if ($response['result'] != 'success') {
        return array('error' => $response['data']);
    }
}


function tphost_GetRegistrarLock($params)
{

    $postfields['domain'] = $params['domain'];
    $postfields['action'] = 'lockStatus';
    $lock_status = Request::call($postfields);
    if ($lock_status['result'] == 'success') {
        return $lock_status['data'];
    } else {
        return ['error' => $lock_status['data']];
    }
}

function tphost_SaveRegistrarLock($params)
{
    $postfields['domain'] = $params['domain'];
    $postfields['action'] = 'updateLock';
    $postfields['lock'] = $params['lockenabled'] == "locked" ? 1 : 0;
    $lock_status = Request::call($postfields);
    if ($lock_status['result'] == 'success') {
        return $lock_status['data'];
    } else {
        return ['error' => $lock_status['data']];
    }
}

function tphost_GetEPPCode($params)
{

    $postfields['domain'] = $params['domain'];
    $postfields['action'] = 'getEPP';
    $epp_status = Request::call($postfields);
    if ($epp_status['result'] == 'success') {
        return array('eppcode' => $epp_status['data']);
    } else {
        return ['error' => $epp_status['data']];
    }

}


function tphost_GetContactDetails($params)
{

    $postfields['domain'] = $params['domain'];
    $postfields['action'] = 'getContactDetails';
    $lock_status = Request::call($postfields);
    unset($lock_status['data']['result']);
    if ($lock_status['result'] == 'success') {
        return $lock_status['data'];
    } else {
        return ['error' => $lock_status['data']];
    }
}


function tphost_SaveContactDetails($params)
{
        $contactDetails = $params['contactdetails'];
    $postfields['domain'] = $params['domain'];
     
    $postfields['domain'] = $params['domain'];
    
     $postfields['contacts']= http_build_query($contactDetails);
    $postfields['action'] = 'saveContactDetails';
    $lock_status = Request::call($postfields);
    if ($lock_status['result'] != 'success') {
        return ['error' => $lock_status['data']];
    }

}


function tphost_GetTldPricing(array $params)
{
    $params_to_send = [];
    $params_to_send['action'] = 'GetTldPricing';
    $result = Request::call($params_to_send);
    if ($result['result'] == 'success') {
        
        $results = new ResultsList;
        $currency = $result["data"]["currency"]["code"];
        $exchangeRate = 1; // Default exchange rate (1:1 for USD)
        $whmcs_currency = Capsule::table('tblcurrencies')
                        ->where('default', '=', '1')
                        ->first();
        // Check if the currency is INR and convert to USD if necessary
        if ($currency == $whmcs_currency->code) {
            $exchangeRate = 1;
        }else{
            $exchangeRate = tphost_getExchangeRate(strtoupper($currency), strtoupper($whmcs_currency->code));
        }
        
        foreach ($result["data"]["pricing"] as $extension => $value) {
            // Convert prices to USD if the original currency is INR
            $registerPriceUSD = $value["register"][1] * $exchangeRate;
            $renewPriceUSD = $value["renew"][1] * $exchangeRate;
            $transferPriceUSD = $value["transfer"][1] * $exchangeRate;

            // All the set methods can be chained and utilised together.
            $item = (new ImportItem)
                ->setExtension($extension)
                ->setMinYears(1)
                ->setMaxYears(1)
                ->setRegisterPrice($registerPriceUSD)
                ->setRenewPrice($renewPriceUSD)
                ->setTransferPrice($transferPriceUSD)
                ->setCurrency(strtoupper($whmcs_currency->code)) // Set to USD after conversion
                ->setEppRequired($value['transferSecretRequired']);

            $results[] = $item;
        }
        return $results;
    } else {
         return array("error" => $result["data"]["pricing"]);
    } 
}

function tphost_getExchangeRate($from, $to) {
    $url = "https://v6.exchangerate-api.com/v6/75a8e95fa74d0f4654c8907f/latest/$from";
    
    // Make the API call
    $response = file_get_contents($url);
    
    // Check if the API call was successful
    if ($response === FALSE) {
        return 1; // Default to 1:1 exchange rate if API call fails
    }

    // Parse the JSON response
    $data = json_decode($response, true);
    
    // Check if the rate is available in the response
    if (isset($data['conversion_rates'][$to])) {
        return $data['conversion_rates'][$to];
    } else {
        return 1; // Default to 1:1 exchange rate if rate is not available
    }
}


