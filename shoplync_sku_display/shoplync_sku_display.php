<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Shoplync_sku_display extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'shoplync_sku_display';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Shoplync';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('SKU Display');
        $this->description = $this->l('A SMS Pro add-on module. Designed to show the corresponding SMS Pro SKU in the product view page. So customers can easily reference your internal SKU numbers which are all in a consistent format (0000-12345).');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('SHOPLYNC_SKU_COMBINATION_HIDE', false);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('productActions') &&
            $this->registerHook('displayProductAdditionalInfo') &&
            $this->registerHook('displayProductButtons');
    }

    public function uninstall()
    {
        Configuration::deleteByName('SHOPLYNC_SKU_COMBINATION_HIDE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitShoplync_sku_displayModule')) == true) {
            $this->postProcess();
        }
        else if(((bool)Tools::isSubmit('submitShoplync_sku_displayUpdateHide')) == true){
            //update hide mpn/sku db page
            error_log('New Values Submitted');
            $this->ClearCurrentValues();
            
            if((bool)Tools::isSubmit('mpn') || (bool)Tools::isSubmit('sku'))
            {                
                $mpn_var = Tools::getValue('mpn');
                if(is_array($mpn_var))
                    $this->UpdateList($mpn_var, 'mpn');
                
                $sku_var = Tools::getValue('sku');
                if(is_array($sku_var))
                    $this->UpdateList($sku_var, 'sku');
            }
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        
        //The submit value for any admin update form
        $this->context->smarty->assign('action_link', $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
        $this->context->smarty->assign('token', '&token='.Tools::getAdminTokenLite('AdminModules'));
        
        $this->context->smarty->assign('update_action', 'submitShoplync_sku_displayUpdateHide');
        $this->context->smarty->assign('modul_action', 'submitShoplync_sku_displayModule');

        $this->context->smarty->assign('module_settings', $this->renderForm());

        $brandsList = $this->GetBrandsList();
        if (!empty($brandsList))
            $this->context->smarty->assign('brands',$brandsList);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }
    protected function UpdateList($brand_list, $type = '')
    {
        if(!is_array($brand_list) || !is_string($type) || strlen($type) !== 3)
            return false;
        
        if(strcmp($type, 'mpn') !== 0 && strcmp($type, 'sku') !== 0)
            return false;
        
        $column = strcmp($type, 'mpn') === 0 ? 'mpn_hide' : 'sku_hide';
        foreach($brand_list as $brand_id)
        {
           //update /insert into mpn or sku
           $query = 'INSERT INTO `' . _DB_PREFIX_ . 'shoplync_sku_display` (`brand_id`, `'.$column.'`)
           VALUES('.$brand_id.', TRUE) ON DUPLICATE KEY UPDATE `'.$column.'`= TRUE;';
           
           Db::getInstance()->execute($query);
        }
    }
    protected function ClearCurrentValues()
    {
        $query = 'TRUNCATE `' . _DB_PREFIX_ . 'shoplync_sku_display`;';
        if (Db::getInstance()->execute($query) == false) {
            error_log('Failed To Clear DB Values.');
        }
    }
    protected function GetBrandsList()
    {
        $brands = Manufacturer::getManufacturers(
            true,
            $this->context->language->id,
            $active = true,
            $p = false,
            $n = false,
            $allGroup = false,
            $group_by = false,
            $withProduct = false
        );
        $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'shoplync_sku_display`;';
        $hidden_brands = $this->processResult(Db::getInstance()->executeS($query));
        
        if (!empty($brands) && !empty($hidden_brands) && is_array($hidden_brands))
        {
            //error_log(print_r($hidden_brands, true));
            foreach($brands as &$brand)
            {
                if(array_key_exists($brand['id_manufacturer'], $hidden_brands))
                {
                    $brand['mpn_hide'] = $hidden_brands[$brand['id_manufacturer']]['mpn_hide'];
                    $brand['sku_hide'] = $hidden_brands[$brand['id_manufacturer']]['sku_hide'];
                }
            }
            unset($brand);
        }
        return $brands;
    }
        
    protected function processResult($result)
    {
        if(empty($result) || !is_array($result))
            return null;
        
        $newValues = array();
        foreach($result as $row)
        {
            $newValues[$row['brand_id']] = $row;
        }
        
        return $newValues;
    }
    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitShoplync_sku_displayModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        
        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l(''),
                //'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Hide For Combinations'),
                        'name' => 'SHOPLYNC_SKU_COMBINATION_HIDE',
                        'is_bool' => true,
                        'desc' => $this->l('Will hide SKU on products with unselected options. (For developers only, must be disabled via JavaScript on the front end)'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'SHOPLYNC_SKU_COMBINATION_HIDE' => Configuration::get('SHOPLYNC_SKU_COMBINATION_HIDE', true),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        $brand = "";
        $sku_reference = null;
        $has_combinations = false;
        if(isset($params['product']))
        {
            
            $mpn = $params['product']->getReferenceToDisplay();
            $product = new Product($params['product']->getId());
            
            if(isset($product))
            {
                $mfg = new Manufacturer($product->id_manufacturer); 
                $brand = $mfg->name;
                
                if($product->hasAttributes() > 0)
                {
                    $has_combinations = true;
                    $combination = $params['product']->getAttributes();
                    $combination = array_pop($combination);
                    if(is_array($combination) && array_key_exists('mpn', $combination) && isset($combination['mpn']) && strlen($combination['mpn'] > 0))
                    {
                        $sku_reference = $combination['mpn'];
                    }
                }
                else{ $sku_reference = $product->mpn; }
            }
        }
        $hide_sku = '';
        
        $brand_id = $product->id_manufacturer;
        $result = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'shoplync_sku_display` WHERE brand_id = '.$brand_id.';');
        
        if(!empty($result) && is_array($result) && array_key_exists('mpn_hide',$result) && array_key_exists('sku_hide',$result))
        {
            $hide_sku = '<style>'.($result['mpn_hide'] > 0 ? ' .product-reference_top.product-reference{ display:none!important; height:0!important;} ' : '').
            ($result['sku_hide'] > 0 || !isset($sku_reference) || strlen($sku_reference) < 1 ? ' #product_sku{ display:none!important; height:0!important; } ' : '').'</style>';
        }
        else if(empty($result) && !isset($sku_reference) || strlen($sku_reference) < 1)
        {
             $hide_sku = '<style>#product_sku{ display:none!important; height:0!important; }</style>';
        }
        
        return '<div id="product_sku" '.($has_combinations && Configuration::get('SHOPLYNC_SKU_COMBINATION_HIDE') == 1 ? 'style="display:none; height:0;"' : '').'><label class="label">SKU: </label>'.
               '<span>'.$sku_reference.'</span></div>'.'<!-- '.$mpn.' '.$brand.' -->'.$hide_sku;
    }
    public function hookDisplayProductButtons(){} 
    public function hookProductActions(){}
}
