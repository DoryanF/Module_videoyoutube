<?php

class ProductVideo extends ObjectModel
{
    public $id_product_video;
    public $id_product;
    public $url_product_video;


    public static function getUrlProductVideo($id_product)
    {
        $sql = 'SELECT url_product_video 
                FROM '._DB_PREFIX_.'product_video 
                WHERE id_product = '.(int)$id_product;

        return DB::getInstance()->getValue($sql);
    }

    public static function insertUrl($id_product, $url_product_video)
    {
        $sql = 'INSERT INTO '._DB_PREFIX_.'product_video (id_product, url_product_video) VALUES ('.(int)$id_product.', "'.pSQL($url_product_video).'")';

        return DB::getInstance()->execute($sql);
    }

    public static function updateUrl($id_product, $url_product_video)
    {
        $sql = 'UPDATE '._DB_PREFIX_.'product_video SET url_product_video = "'.pSQL($url_product_video).'"
        WHERE id_product = '.(int)$id_product;

        return DB::getInstance()->execute($sql);
    }
}