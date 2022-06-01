<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class francisco extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'francisco';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Francisco Navarro';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Módulo de Francisco');
        $this->description = $this->l('Esté modulo muestra un consejo sobre preparación de té al dia');
        $this->confirmUninstall = $this->l('¿Estás segur@ que quieres desinstalarlo?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayAfterProductThumbs') && $this->installDB();
    }

	public function installDB()
	{
		Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS '._DB_PREFIX_. $this->name . '_text (
			id int(11) NOT NULL AUTO_INCREMENT,
			text VARCHAR(255),
			PRIMARY KEY (`id`)
		  ) ENGINE='. _MYSQL_ENGINE_ .' DEFAULT CHARSET=utf8');
		return true;
	}

	public function uninstallDB()
	{
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_. $this->name . '_text');
		return true;
	}

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDB();
    }

	
	public function getContent()
	{
		$this->context->controller->addJS($this->local_path . 'views/js/admin.js');
		$ajax_token = Tools::encrypt($this->name.'/ajax.php');
		$shop = new ShopUrl($this->context->shop->id);

		$base_url = AdminController::$currentIndex . '&configure=' . $this->name . '&token='. Tools::getAdminTokenLite('AdminModules').'&mod_action=';
		$this->urls = [
			'ajax' => $shop->getURL((int)Configuration::get('PS_SSL_ENABLED')).'modules/'.$this->name.'/ajax.php?token='.$ajax_token,
			'add' => $base_url.'add',
			'edit' => $base_url.'edit&id=',
			'delete' => $base_url.'delete&id=',
		];

		$action = Tools::getValue('mod_action');
		return $this->postProcess() . $this->renderAdmin($action);
	}
	
	private function postProcess()
	{
		if (Tools::isSubmit('add')) {
			$insert = [
				'text' => pSQL(Tools::getValue('text'))
			];
			Db::getInstance()->insert($this->name . '_text', $insert);
			
			return $this->displayConfirmation($this->l('Guardado correctamente'));
		}
		if (Tools::isSubmit('edit')) {
			$id = (int)Tools::getValue('id');
			$update = [
				'text' => pSQL(Tools::getValue('text'))
			];
			Db::getInstance()->update($this->name . '_text', $update, 'id = ' .(int)$id);
			
			return $this->displayConfirmation($this->l('Editado correctamente'));
		}
	}
	
	private function getForm($action)
	{
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->languages = $this->context->controller->getLanguages();
		$helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
		$helper->default_form_language = $this->context->controller->default_form_language;
		$helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
		$helper->title = $this->displayName;
		$helper->submit_action = $action;
		
		if ($action == 'add') {
			
			$helper->fields_value = [
				'text' => '',
			];
			
			$form[] = [
				'form' => [
					'legend' => [
						'title' => $this->l('Añadir nuevo consejo')
					],
					'input' => [
						[
							'type' => 'text',
							'name' => 'text',
							'label' => $this->l('Nuevo consejo'),
						],
					],
					'submit' => [
						'title' => $this->l('Add'),
					],
				],
			
			];
		}
		if ($action == 'edit') {
			$id = (int)Tools::getValue('id');
			$helper->fields_value = [
				'id' => $id,
				'text' => $this->getText($id),
			];
			
			$form[] = [
				'form' => [
					'legend' => [
						'title' => $this->l('Edita el consejo del dia')
					],
					'input' => [
						[
							'type' => 'hidden',
							'name' => 'id',
						],
						[
							'type' => 'text',
							'name' => 'text',
							'label' => $this->l('Cambia el consejo'),
						],
					],
					'submit' => [
						'title' => $this->l('Edit'),
					],
				],
			
			];
		}

		
		return $helper->generateForm($form);
	}
	
	private function renderAdmin($action)
	{
		if ($action == '' || $action == 'home') {
			$this->context->smarty->assign([
				'textos' => $this->getTextos(),
				'urls' => $this->urls,
			]);
			return $this->context->smarty->fetch($this->local_path. 'views/templates/admin/admin.tpl');
		}
		if ($action == 'delete') {
			$this->context->smarty->assign([
				'textos' => $this->getTextos(),
				'urls' => $this->urls,
			]);
			$id = (int)Tools::getValue('id');
			Db::getInstance()->delete($this->name . '_text', 'id = ' .(int)$id);
				
			return $this->displayConfirmation($this->l('Eliminado correctamente'));
		}
		return $this->getForm($action);
	}
	
	private function getTextos()
	{
		return Db::getInstance()->executeS('SELECT *
			FROM ' ._DB_PREFIX_.$this->name.'_text');
	}

	private function getText($id)
	{
		return Db::getInstance()->getValue('SELECT text
			FROM ' ._DB_PREFIX_ . $this->name . '_text
			WHERE id = ' . (int)$id);
	}

    public function HookDisplayAfterProductThumbs()
    {
        $textos = $this->getTextos();
		$this->context->smarty->assign([
			'textos' => $textos,
		]);
		
		return $this->display(__FILE__, 'product.tpl');
    }
}
