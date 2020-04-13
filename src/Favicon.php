<?php

namespace Trozgan\Favicon;

use Sunra\PhpSimple\HtmlDomParser;
use GuzzleHttp\Client;

class Favicon{
  public function getFavicon($url = ''){
    if (!$url) {
      throw new \InvalidArgumentException('Url cannot be empty', E_ERROR);
    }

    $this->context = stream_context_create(array(
      'http' => array(
        'header' => array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201'),
      ),
      'https' => array(
        'header' => array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201'),
      ),
    ));

    $megvan = FALSE;
    $icon = NULL;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_REFERER, "http://www.example.org/yay.htm");
    curl_setopt($curl, CURLOPT_USERAGENT, "spider");
    curl_exec($curl);

    $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if($responseCode == 200){
      $dom = HtmlDomParser::file_get_html($url,false,$this->context);
      if($dom){
        foreach($dom->find('link[rel="apple-touch-icon"]') as $apple){
          $icon = $apple->attr['href'];
          $megvan = TRUE;
        }

        if($megvan == FALSE){
          foreach($dom->find('link[rel*="icon"]') as $apple){
            $icon = $apple->attr['href'];
            $megvan = TRUE;
          }
        }
      }
    }

    if($megvan){
      if($icon[0] == '/'){
        $icon = $url.$icon;
      }

      $icon = explode("?", $icon)[0];
      return $icon;
    } else { return FALSE;}
  }
}
