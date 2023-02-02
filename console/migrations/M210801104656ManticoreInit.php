<?php

namespace console\migrations;

use common\libs\manticore\Client;
use yii\db\Migration;

/**
 * Class M210801104656ManticoreInit
 */
class M210801104656ManticoreInit extends Migration
{
    public function up(): bool
    {
        $client = new Client();

        $client->dropIndexIfExist('products');
        $client->dropIndexIfExist('vocabulary');
        $client->dropIndexIfExist('firms');
        $client->dropIndexIfExist('firms_profiles');
        $client->dropIndexIfExist('locations');
        $client->dropIndexIfExist('okeds');

        $result = [];
        //товары
        $result['products'] = $client->createIndex('products', [
            //название товара.
            'title' => ['type' => Client::TYPE_STR],
            'status' => ['type' => Client::TYPE_INT],
            'user_id' => ['type' => Client::TYPE_INT],
            'firm_id' => ['type' => Client::TYPE_INT],
            //цена.
            'price' => ['type' => Client::TYPE_FLOAT],
            //характеристики.
            'vocabularies' => ['type' => Client::TYPE_MVA],
            //значения характеристик.
            'terms' => ['type' => Client::TYPE_MVA],
            //категории.
            'categories' => ['type' => Client::TYPE_MVA],
            //способы доставки.
            'delivery_terms' => ['type' => Client::TYPE_MVA],
        ]);
        //словари товаров.
        $result['vocabulary'] = $client->createIndex('vocabulary', [
            'title' => ['type' => Client::TYPE_STR],
            'type' => ['type' => Client::TYPE_INT],
            'terms' => ['type' => Client::TYPE_MVA],
        ]);
        //организации.
        $result['firms'] = $client->createIndex('firms', [
            'status' => ['type' => Client::TYPE_INT],
            'profile_id' => ['type' => Client::TYPE_INT],
            'user_id' => ['type' => Client::TYPE_INT],
            'country_id' => ['type' => Client::TYPE_INT],
            'region_id' => ['type' => Client::TYPE_INT],
            'city_id' => ['type' => Client::TYPE_INT],
            'bin' => ['type' => Client::TYPE_BIGINT],
            'title' => ['type' => Client::TYPE_STR],
            'legal_address' => ['type' => Client::TYPE_STR],
            'activity' => ['type' => Client::TYPE_MVA],
        ]);
        //профили организаций (из парсера).
        $result['firms_profiles'] = $client->createIndex('firms_profiles', [
            //ID организации.
            'firm_id' => ['type' => Client::TYPE_INT],
            //БИН.
            'bin' => ['type' => Client::TYPE_BIGINT],
            //ОКЕД.
            'oked' => ['type' => Client::TYPE_INT],
            //Спарсенная ли запись.
            'is_parsed' => ['type' => Client::TYPE_INT],
            //КАТО
            'kato' => ['type' => Client::TYPE_INT],
            //Классификатор размерности предприятия.
            'krp' => ['type' => Client::TYPE_INT],
            //Название организации.
            'title' => ['type' => Client::TYPE_STR],
            //Вид деятельности предприятия.
            'activity' => ['type' => Client::TYPE_STR],
            //Населённый пункт.
            'locality' => ['type' => Client::TYPE_STR],
            //Размер организации.
            'company_size' => ['type' => Client::TYPE_STR],
            //Руководитель.
            'leader' => ['type' => Client::TYPE_STR],
            //Юридический адрес.
            'legal_address' => ['type' => Client::TYPE_STR],
        ]);
        //locations.
        $result['locations'] = $client->createIndex('locations', [
            //Название города.
            'title' => ['type' => Client::TYPE_STR],
            //Идентификатор региона.
            'region' => ['type' => Client::TYPE_INT],
        ]);
        //Oked.
        $result['okeds'] = $client->createIndex('okeds', [
            //Название сферы деятельности.
            'title' => ['type' => Client::TYPE_STR],
            'key' => ['type' => Client::TYPE_INT],
        ]);
        foreach ($result as $index_name => $result_bool) {
            if ($result_bool === false) {
                echo("Произошла ошибка при создании индекса {$index_name}.");
                return false;
            }
        }
        echo "Индексы созданы.";
        return true;
    }

    public function down(): bool
    {
        $client = new Client();
        $client->dropIndexIfExist('products');
        $client->dropIndexIfExist('vocabulary');
        $client->dropIndexIfExist('firms');
        $client->dropIndexIfExist('firms_profiles');
        $client->dropIndexIfExist('locations');
        $client->dropIndexIfExist('okeds');
        return true;
    }
}
