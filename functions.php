<?php
/**
 * fonction pour interroger une api
 */


function getApi($url, $method='GET', $json_data='', $httpheader = 'Content-Type: application/json'){
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($httpheader));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (strlen($json_data) > 0)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    $resp  = curl_exec($ch);
    
    if(!$resp){
        $resp = (json_encode(array(array("error" => curl_error($ch), "code" => curl_errno($ch)))));
    } 
    curl_close($ch);
    return $resp;
}