<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

use PrestaShop\PrestaShop\Core\Product\ProductExtraContent;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_.'videoyoutube/classes/ProductVideo.php';

class VideoYoutube extends Module
{
    public function __construct()
    {
        $this->name = 'videoyoutube';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Doryan Fourrichon';
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        
        //récupération du fonctionnement du constructeur de la méthode __construct de Module
        parent::__construct();
        $this->bootstrap = true;

        $this->displayName = $this->l('Video Youtube');
        $this->description = $this->l('Permet d\'ajouter une vidéo youtube sur la page d\'accueil et sur les fiches produits');

        $this->confirmUninstall = $this->l('Do you want to delete this module');

    }

    public function install()
    {
        if (!parent::install() ||
        !Configuration::updateValue('VIDEOACTIVE',0) ||
        !Configuration::updateValue('URLVIDEO','') ||
        !Configuration::updateValue('ACTIVEAUTO',0) ||
        !Configuration::updateValue('ACTIVELOOP',0) ||
        !$this->registerHook('displayHome') ||
        !$this->registerHook('displayAdminProductsExtra') ||
        !$this->registerHook('displayProductExtraContent') ||
        !$this->registerHook('actionProductUpdate') ||
        !$this->createTable()
        ) {
            return false;
        }
            return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
        !Configuration::deleteByName('VIDEOACTIVE') ||
        !Configuration::deleteByName('URLVIDEO') ||
        !Configuration::deleteByName('ACTIVEAUTO') ||
        !Configuration::deleteByName('ACTIVELOOP') ||
        !$this->unregisterHook('displayHome') ||
        !$this->unregisterHook('displayAdminProductsExtra') ||
        !$this->unregisterHook('displayProductExtraContent') ||
        !$this->unregisterHook('actionProductUpdate') ||
        !$this->deleteTable()
        ) {
            return false;
        }
            return true;
    }

    public function getContent()
    {
        return $this->postProcess().$this->renderForm();
    }

    public function postProcess()
    {
        if(Tools::isSubmit('saving'))
        {
            if(Validate::isBool(Tools::getValue('VIDEOACTIVE')) && Validate::isString('URLVIDEO'))
            {
                Configuration::updateValue('VIDEOACTIVE',Tools::getValue('VIDEOACTIVE'));
                Configuration::updateValue('URLVIDEO',Tools::getValue('URLVIDEO'));
                Configuration::updateValue('ACTIVEAUTO',Tools::getValue('ACTIVEAUTO'));
                Configuration::updateValue('ACTIVELOOP',Tools::getValue('ACTIVELOOP'));
    
                return $this->displayConfirmation('Bien enregistré !');

            }
        }
    }

    public function renderForm()
    {
        $field_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                        'label' => $this->l('Active video'),
                        'name' => 'VIDEOACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                ],
                [
                    'type' => 'switch',
                        'label' => $this->l('Active autoplay'),
                        'name' => 'ACTIVEAUTO',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                ],
                [
                    'type' => 'switch',
                        'label' => $this->l('Active loop video'),
                        'name' => 'ACTIVELOOP',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                ],
                [
                    'type' => 'text',
                    'name' => 'URLVIDEO',
                    'label' => $this->l('Url video')
                ]
            ],
            'submit' => [
                'title' => $this->l('save'),
                'class' => 'btn btn-primary',
                'name' => 'saving'
            ]
        ];

        $helper = new HelperForm();
        $helper->module  = $this;
        $helper->name_controller = $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->fields_value['VIDEOACTIVE'] = Configuration::get('VIDEOACTIVE');
        $helper->fields_value['URLVIDEO'] = Configuration::get('URLVIDEO');
        $helper->fields_value['ACTIVEAUTO'] = Configuration::get('ACTIVEAUTO');
        $helper->fields_value['ACTIVELOOP'] = Configuration::get('ACTIVELOOP');

        return $helper->generateForm($field_form);

    }

    //VIDEO PAGE HOME
    public function hookDisplayHome($params)
    {

        $urlYoutube = explode("=",Configuration::get('URLVIDEO'));

        if(Configuration::get('VIDEOACTIVE') == 1)
        {
            $this->smarty->assign(array(
                'url_video' => $urlYoutube[1],
                'active_auto' => Configuration::get('ACTIVEAUTO'),
                'active_loop' => Configuration::get('ACTIVELOOP')
            ));

            return $this->display(__FILE__,'/views/templates/hook/video_home.tpl');
        }
    }
    //

    //Table SQL
    public function createTable()
    {
        return Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'product_video(
                id_product_video INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                id_product INT NOT NULL,
                url_product_video VARCHAR(255) NOT NULL
            )'
        );
    }

    public function deleteTable()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS '._DB_PREFIX_.'product_video'
        );
    }
    //

    //Admin Product
    public function hookDisplayAdminProductsExtra($params)
    {
        $this->smarty->assign(array(
            'product' => $params["id_product"]
        ));
            
        return $this->display(__FILE__, 'views/templates/hook/video_admin_product.tpl');
    }

    public function hookActionProductUpdate($params)
    {
        $productId = (int)Tools::getValue('product');
        $urlVideo = Tools::getValue('url_video_product');

        $product = new Product($productId);
        $product->active = 1;
        $product->save();

        // Vérifiez si une entrée existe déjà pour ce produit
        $existingUrl = ProductVideo::getUrlProductVideo($productId);
        
        if ($existingUrl !== false) {
            // Si l'URL existe, mettez à jour
            ProductVideo::updateUrl($productId, pSQL($urlVideo));
        } else {
            // Sinon, insérez une nouvelle entrée
            ProductVideo::insertUrl($productId, pSQL($urlVideo));
        }
    }
    //

    //Product Tab
    public function hookDisplayProductExtraContent($params)
    {
        $existingUrl = ProductVideo::getUrlProductVideo($params["product"]->id);
        $urlYoutube = explode("=",$existingUrl);
        
        $return = [];

        if ($existingUrl !== false) {


            $this->smarty->assign(array(
                'url_video' => $urlYoutube[1]
            ));

            $content = $this->display(__FILE__, 'views/templates/hook/video_product_tab.tpl');
            $return [] = (new ProductExtraContent())
                ->setTitle($this->l('Video'))
                ->setContent($content);
        }

        return $return;
    }
    //
}