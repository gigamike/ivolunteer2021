<?php

namespace Application\Service;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;

class ServiceCurl extends AbstractActionController
{
  public function curl($url){
    $parsedUrl = parse_url($url);
    $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : null;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    if($scheme == 'https'){
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    }
    $curlHeader = curl_getinfo($curl);
    $curlResult = curl_exec($curl);
    $curlErrorMessage = curl_error($curl);
    $curlErrorNo = curl_errno($curl);
    curl_close($curl);

    $response = array(
      'header' => $curlHeader,
      'result' => trim($curlResult),
      'error_message' => trim($curlErrorMessage),
      'error_no' => trim($curlErrorNo),
    );

    return $response;
  }
}
