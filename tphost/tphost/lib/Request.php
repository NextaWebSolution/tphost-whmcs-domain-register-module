<?php

namespace WHMCS\Module\Registrar\tphost;

use WHMCS\Database\Capsule;

class Request
{
    public static function call($post_fields)
    {
        $api_key = decrypt(Capsule::table('tblregistrars')
            ->where('registrar', '=', 'tphost')
            ->where('setting', '=', 'apikey')
            ->value('value'));
        $email = decrypt(Capsule::table('tblregistrars')
            ->where('registrar', '=', 'tphost')
            ->where('setting', '=', 'email')
            ->value('value'));			
        $server_url = 'https://thepowerhost.in/my/index?m=domain_reseller_panel&apikey=' . $api_key.'&email='.$email;			
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $server_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $return['errors'][] = array('message' => 'curl error: ' . curl_errno($ch) . " - " . curl_error($ch));
        }
        curl_close($ch);
        $results = json_decode($response, true);
        if (!$results) {
            $return['result'] = 'error';
            $return['data'] = 'API Bad response';
        } else {
            $return = $results;
        }
        return $return;
    }

}