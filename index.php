<?php

use GuzzleHttp\Client;
// use GuzzleHttp\Psr7\Request;

require 'vendor/autoload.php';

$client = new Client (['base_uri' => 'https://products.rubezh.ru', 'timeout' => '5.0']);
$response = $client->request ('GET', 'passport-api/v1/catalog');
$data_catalog = $response->getBody(); // Забираем тело ответа
$json_catalog = json_decode($data_catalog, true); // Декодируем в ассоциативный массив
$full_catalog = $json_catalog["data"]["catalog"]; // Берем основные данные для использования в цикле

// Выполняем цикл по количеству значений из passport-api/v1/catalog
for ($i = 0, $size = count($full_catalog); $i < $size; $i++) {
    
    $one_product = $full_catalog[$i]; // Выбираем по индексу одну запись продукта из каталога 
    $id_product = $one_product["xml_id"]; // берем 'xml_id' для обращения к passport-api/v1/nomenclatures/

    $response = $client->request ('GET', 'passport-api/v1/nomenclatures/'.$id_product);
    $data_property = $response->getBody();
    $json_property = json_decode($data_property, true);
    $one_property = $json_property["data"]; // берем основные данные для объединения
    
    $merged_catalog [] = array_merge($one_product, $one_property); // обьединяем два массива

}

$merged_catalog = json_encode(['data' => $merged_catalog], JSON_PRETTY_PRINT); // перекодируем с дабавлением 'data' и для лучшей читаемости используем флаг JSON_PRETTY_PRINT 
file_put_contents('merged_catalog.json', $merged_catalog, LOCK_EX); // создаем файл и записываем в него объедененные массивы с флагом LOCK_EX для защиты от одновременной записи в файл
