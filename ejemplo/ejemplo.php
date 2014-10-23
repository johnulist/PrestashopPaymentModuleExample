<?php
/**
* 2007-2014 PrestaShop
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
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Ejemplo extends PaymentModule
{
	protected $config_form = false;

	public function __construct()//constructor
	{
		//acerca del modulo en si
		$this->name = 'ejemplo';
		$this->tab = 'payments_gateways';
		$this->version = '0.0.1';
		$this->author = 'nsiksnys';
		$this->need_instance = 0;

		$this->bootstrap = true;//para que use bootstrap

		parent::__construct();

		//lo que se muestra en el listado de modulos en el backoffice
		$this->displayName = $this->l('Ejemplo');//nombre
		$this->description = $this->l('Modulo de ejemplo para prestashop 1.6');//descripcion

		$this->confirmUninstall = $this->l('Est&aacute; seguro de desinstalar este modulo?');//mensaje que aparece al momento de desinstalar el modulo
	}

	/**
	 * Instalacion del modulo
	 * No olvidar de crear los metodos de actualizacion (si son necesarios)
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install()
	{
		//valores de configuracion (se pueden modificar en el backoffice)
		Configuration::updateValue('MODULE_EJEMPLO_CONFIG_VALUE', 'Mensaje de prueba');
		Configuration::updateValue('MODULE_EJEMPLO_CONFIG_VALUE_DISPLAY',false);
		
		//creo el order state
		$this->createOrderState('PS_OS_EJEMPLO','Ejemplo');

		include(dirname(__FILE__).'/sql/install.php');//script sql

		return parent::install() &&	$this->registerHook('displayPayment') && $this->registerHook('displayPaymentReturn');
	}

	/**
	 * Desinstalacion del modulo
	 */
	public function uninstall()
	{
		// borro los valores de configuracion
		Configuration::deleteByName('MODULE_EJEMPLO_CONFIG_VALUE');
		Configuration::deleteByName('MODULE_EJEMPLO_CONFIG_VALUE_DISPLAY');
		
		//deshabilito el order state
		$this->deleteOrderState('PS_OS_EJEMPLO', 'Ejemplo');

		//include(dirname(__FILE__).'/sql/uninstall.php'); // no va porque no borro las tablas

		return parent::uninstall();
	}
	
	/**
	 * Crea un OrdenState y lo guarda en la base de datos
	 * @param Nombre con el que se guarda en la base de datos $db_name
	 * @param Nombre del OrderState $name
	 */
	 public function createOrderState($db_name,$name)
	{
	if (!Configuration::get($name))//si el status no existe ya
		{
			$orderState = new OrderState();
			$orderState->name =  array('name' => $name);
			$orderState->send_email = false;
			$orderState->color = 'royalblue';
			$orderState->hidden = false;
			$orderState->delivery = false;
			$orderState->logable = false;
			$orderState->invoice = false;
			$orderState->unremovable = false;
			if ($orderState->add())
			{
				Configuration::updateValue($db_name, (int)$orderState->id);
			}
		}
		else
		{//si existe, lo reactiva
			Db::getInstance()->update('order_state', array('deleted' => 0), ' id_order_state='.Configuration::get($db_name));
		}
	}
	
	/**
	 * Desactiva un OrderState
	 * @param int $id_order_state el id
	 */
	public function deleteOrderState($db_name, $name)
	{
		$orderStateId = (int)Configuration::get($db_name);
			
		Db::getInstance()->update('order_state', array('deleted' => 1), ' id_order_state='.$orderStateId);
	}
	
	/**
	 * Carga el formulario de configuration del modulo.
	 * Mas informacion en http://doc.prestashop.com/display/PS16/Adding+a+configuration+page
	 */
	public function getContent()
	{
		//Si se enviaron valores via formulario, que se procesen
		if (Tools::isSubmit('btnSubmit'))
			$this->_postProcess();

		$this->context->smarty->assign('module_dir', $this->_path);

		$output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');//recupero el template de configuracion

		return $output.$this->renderForm();//cargo el formulario
	}

	/**
	 * Crea el formulario que se muestra cuando uno quiere configurar el modulo
	 * Mas informacion en http://doc.prestashop.com/display/PS16/Adding+a+configuration+page
	 */
	protected function renderForm()
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;//no mostrar el toolbar
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;//el idioma por defecto es el que esta configurado en prestashop
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), //Recupera los valores de configuracion de la base de datos
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);

		return $helper->generateForm(array($this->getConfigForm()));//genera el formulario en si
	}

	//estructura del formulario
	protected function getConfigForm()
	{
		return array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Settings'),//titulo del form
				'icon' => 'icon-cogs',//icono
				),
				'input' => array(//valor de entrada (por el momento uno solo)
					array(
						'type' => 'text',
						'label' => $this->l('Valor de prueba'),
						'name' => 'mensaje',
						//'desc' => $this->l('Este valor se mostrara en el checkout'),
						'required' => true
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Mostrar'),
						'name' => 'activo',
						'is_bool' => true,
						'desc' => $this->l('Mostrar o no el valor de prueba en el checkout'),
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
						)
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'button'
				),
			),
		);
	}

	protected function getConfigFormValues()
	{//recupera los valores de configuracion
		return array(
			'mensaje' => Configuration::get('MODULE_EJEMPLO_CONFIG_VALUE'),
			'activo' => Configuration::get('MODULE_EJEMPLO_CONFIG_VALUE_DISPLAY')
		);
	}
	
	//recupero y guardo los valores de configuracion
	protected function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			$form_values = array(
					'mensaje' => Tools::getValue('mensaje'),
					'activo' => Tools::getValue('activo')
			);//recupero los valores

			foreach (array_keys($form_values) as $key)//los guardo
				Configuration::updateValue($key, Tools::getValue($key));
		}
	}
	
/**
	* Opcional: para archivos CSS y JavaScript que se usaran en el BackOffice de este modulo.
	*/
/*	public function hookBackOfficeHeader()
	{
		$this->context->controller->addJS($this->_path.'js/back.js');
		$this->context->controller->addCSS($this->_path.'css/back.css');
	}*/

	/**
	 * Opcional: para archivos CSS y JavaScript que se usaran en el FrontOffice de este modulo.
	 */
/*	public function hookHeader()
	{
		$this->context->controller->addJS($this->_path.'/js/front.js');
		$this->context->controller->addCSS($this->_path.'/css/front.css');
	}*/
	
	public function checkCurrency($cart)
	{
		$currency_order = new Currency($cart->id_currency);
		$currencies_module = $this->getCurrency($cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}
	
	public function hookDisplayPayment()
	{
		if (!$this->active)
			return;
		
		$tooltip='Metodo de pago de prueba';
		if (Configuration::get('MODULE_EJEMPLO_CONFIG_VALUE_DISPLAY'))
			$tooltip = Configuration::get('MODULE_EJEMPLO_CONFIG_VALUE');//recupero el valor de configuracion que el admin ingreso en el backoffice
		
		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_ejemplo' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
			'tooltip' => $tooltip
		));
		return $this->display(__FILE__, 'payment.tpl');//redirecciona al template de payment
	}

	public function hookDisplayPaymentReturn($params)
	{
		/* $params (desde OrderConfirmationController):
		 * 		getOrdersTotalPaid()
		 * 		currency -> sign
		 * 		order
		 * 		currency
		 */
		
		if (!$this->active)
			return;

		$state = $params['objOrder']->getCurrentState();//recupero el estado de la orden
		
		$config_value='Metodo de pago de prueba';
		if (Configuration::get('MODULE_EJEMPLO_CONFIG_VALUE_DISPLAY'))
			$config_value = Configuration::get('MODULE_EJEMPLO_CONFIG_VALUE');//recupero el valor de configuracion que el admin ingreso en el backoffice
		
		if (!in_array($state, array(Configuration::get('PS_OS_OUTOFSTOCK'), Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'))))
		{
			$this->smarty->assign(array(//muestro los items: total a pagar, status e id de orden
				'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
				'status' => 'ok', //seteo el status como ok
				'config_value' => $config_value
			));
			if (isset($params['objOrder']->reference) && strcasecmp($params['objOrder']->reference,"") != 0)//si la referencia de la orden esta seteada y no es vacia
				$this->smarty->assign( array(
						'reference' => $params['objOrder']->reference)
				);
		}
		else
		{//si por alguna razon no hay stock de alguno de los productos elegidos
			$this->smarty->assign(array(
				'status' => 'failed', //seteo el status como fallido
				'status_desc' => $state //este mensaje se mostrara al usuario
				)
			);
		}
		return $this->display(__FILE__, 'payment_return.tpl');
	}
}