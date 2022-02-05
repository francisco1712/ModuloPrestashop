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
        return parent::install() && $this->registerHook('displayAfterProductThumbs');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

	
	public function getContent()
	{
		return $this->postProcess() . $this->getForm();
	}
	
	private function postProcess()
	{
		if (Tools::isSubmit('cambiar_nombre')) {
			$texto_nuevo = Tools::getValue('texto_nuevo');
			$enlace_video = Tools::getValue('enlace_video');
			Configuration::updateValue('fran_module_text', $texto_nuevo);
		}
	}
	
	private function getForm()
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
		$helper->submit_action = 'cambiar_nombre';
		
		$helper->fields_value = [
			'texto_nuevo' => Configuration::get('fran_module_text'),
			'enlace_video' => Configuration::get('fran_module_text'),
		];
		
		$form[] = [
			'form' => [
				'legend' => [
					'title' => $this->l('Cambia el consejo del dia')
				],
				'input' => [
					[
						'type' => 'textarea',
						'name' => 'texto_nuevo',
						'label' => $this->l('Nuevo consejo'),
					],
				],
				'submit' => [
					'title' => $this->l('Save'),
				],
			],
		
		];
		
		return $helper->generateForm($form);
	}
	
    public function HookDisplayAfterProductThumbs()
    {
        $texto = Configuration::get('fran_module_text');
		$this->context->smarty->assign([
			'texto' => $texto,
			'enlace' => $enlace,
		]);
		
		return $this->display(__FILE__, 'product.tpl');
    }
}
