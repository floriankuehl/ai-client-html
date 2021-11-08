<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process;


// Strings for translation
sprintf( 'process' );


/**
 * Default implementation of checkout process HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/checkout/standard/process/subparts
	 * List of HTML sub-clients rendered within the checkout standard process section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2014.03
	 * @category Developer
	 */
	private $subPartPath = 'client/html/checkout/standard/process/subparts';

	/** client/html/checkout/standard/process/account/name
	 * Name of the account part used by the checkout standard process client implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Client\Html\Checkout\Standard\Process\Account\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2017.04
	 * @category Developer
	 */

	/** client/html/checkout/standard/process/address/name
	 * Name of the address part used by the checkout standard process client implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Client\Html\Checkout\Standard\Process\Address\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2017.04
	 * @category Developer
	 */
	private $subPartNames = array( 'account', 'address' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		$view = $this->getView();

		if( $view->get( 'standardStepActive' ) !== 'process' ) {
			return '';
		}

		$html = '';
		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->body( $uid );
		}
		$view->processBody = $html;

		/** client/html/checkout/standard/process/template-body
		 * Relative path to the HTML body template of the checkout standard process client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in client/html/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page body
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/standard/process/template-header
		 */
		$tplconf = 'client/html/checkout/standard/process/template-body';
		$default = 'checkout/standard/process-body-standard';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		$view = $this->getView();

		if( $view->get( 'standardStepActive' ) !== 'process' ) {
			return '';
		}

		return parent::header( $uid );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( string $type, string $name = null ) : \Aimeos\Client\Html\Iface
	{
		/** client/html/checkout/standard/process/decorators/excludes
		 * Excludes decorators added by the "common" option from the checkout standard process html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/html/common/decorators/default" before they are wrapped
		 * around the html client.
		 *
		 *  client/html/checkout/standard/process/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/standard/process/decorators/global
		 * @see client/html/checkout/standard/process/decorators/local
		 */

		/** client/html/checkout/standard/process/decorators/global
		 * Adds a list of globally available decorators only to the checkout standard process html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
		 *
		 *  client/html/checkout/standard/process/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/standard/process/decorators/excludes
		 * @see client/html/checkout/standard/process/decorators/local
		 */

		/** client/html/checkout/standard/process/decorators/local
		 * Adds a list of local decorators only to the checkout standard process html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\Html\Checkout\Decorator\*") around the html client.
		 *
		 *  client/html/checkout/standard/process/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\Html\Checkout\Decorator\Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/standard/process/decorators/excludes
		 * @see client/html/checkout/standard/process/decorators/global
		 */

		return $this->createSubClient( 'checkout/standard/process/' . $type, $name );
	}


	/**
	 * Processes the input, e.g. store given order.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function init()
	{
		$view = $this->getView();
		$context = $this->getContext();

		if( $view->param( 'c_step' ) !== 'process'
			|| $view->get( 'standardErrorList', [] ) !== []
			|| $view->get( 'standardStepActive' ) !== null
		) {
			return true;
		}

		try
		{
			$orderCntl = \Aimeos\Controller\Frontend::create( $context, 'order' );
			$basketCntl = \Aimeos\Controller\Frontend::create( $context, 'basket' );


			if( $view->param( 'cs_order', null ) !== null )
			{
				$basket = $basketCntl->store();
				$orderItem = $orderCntl->add( $basket->getId(), ['order.type' => 'web'] )->store();

				$context->getSession()->set( 'aimeos/orderid', $orderItem->getId() );
				parent::init();
			}
			elseif( ( $orderid = $context->getSession()->get( 'aimeos/orderid' ) ) !== null )
			{
				$parts = \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL;
				$orderItem = $orderCntl->get( $orderid, false );
				$basket = $basketCntl->load( $orderItem->getBaseId(), $parts, false );
			}
			else
			{
				return;
			}

			if( ( $form = $this->processPayment( $basket, $orderItem ) ) === null )
			{
				$services = $basket->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );
				$args = ( $service = reset( $services ) ) ? ['code' => $service->getCode()] : [];

				$orderCntl->save( $orderItem->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED ) );
				$view->standardUrlNext = $this->getUrlConfirm( $view, $args, ['absoluteUri' => true] );
				$view->standardMethod = 'POST';
			}
			else // no payment service available
			{
				$view = $this->addFormData( $view, $form );
			}
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$error = array( $context->translate( 'client', $e->getMessage() ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
		}
		catch( \Aimeos\Controller\Frontend\Exception $e )
		{
			$error = array( $context->translate( 'controller/frontend', $e->getMessage() ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$error = array( $context->translate( 'mshop', $e->getMessage() ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
		}
		catch( \Exception $e )
		{
			$error = array( $context->translate( 'client', 'A non-recoverable error occured' ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
			$this->logException( $e );
		}
	}


	/**
	 * Adds the required data for the payment form to the view
	 *
	 * @param \Aimeos\MW\View\Iface $view View object to assign the data to
	 * @param \Aimeos\MShop\Common\Helper\Form\Iface $form Form helper object including the form data
	 * @return \Aimeos\MW\View\Iface View object with assigned data
	 */
	protected function addFormData( \Aimeos\MW\View\Iface $view, \Aimeos\MShop\Common\Helper\Form\Iface $form )
	{
		$url = $form->getUrl();

		if( $form->getMethod() === 'GET' )
		{
			$urlParams = [];

			foreach( $form->getValues() as $item )
			{
				foreach( (array) $item->getDefault() as $key => $value ) {
					$urlParams[$item->getInternalCode()][$key] = $value;
				}
			}

			$url .= strpos( $url, '?' ) ? '&' : '?' . map( $urlParams )->toUrl();
		}

		$public = $hidden = [];

		foreach( $form->getValues() as $key => $item )
		{
			if( $item->isPublic() ) {
				$public[$key] = $item;
			} else {
				$hidden[$key] = $item;
			}
		}

		$view->standardUrlNext = $url;
		$view->standardProcessPublic = $public;
		$view->standardProcessHidden = $hidden;
		$view->standardProcessParams = $form->getValues();
		$view->standardUrlExternal = $form->getExternal();
		$view->standardMethod = $form->getMethod();
		$view->standardHtml = $form->getHtml();

		return $view;
	}


	/**
	 * Returns the form helper object for building the payment form in the frontend
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Saved basket object including payment service object
	 * @param \Aimeos\MShop\Order\Item\Iface $orderItem Saved order item created for the basket object
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface|null Form object with URL, parameters, etc.
	 * 	or null if no form data is required
	 */
	protected function processPayment( \Aimeos\MShop\Order\Item\Base\Iface $basket, \Aimeos\MShop\Order\Item\Iface $orderItem ) : ?\Aimeos\MShop\Common\Helper\Form\Iface
	{
		if( $basket->getPrice()->getValue() + $basket->getPrice()->getCosts() <= 0
			&& $this->isSubscription( $basket->getProducts() ) === false
		) {
			return null;
		}

		$services = $basket->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );

		if( ( $service = reset( $services ) ) === false ) {
			return null;
		}

		$view = $this->getView();
		$conf = ['absoluteUri' => true];
		$args = ['code' => $service->getCode()];
		$urls = [
			'payment.url-self' => $this->getUrlSelf( $view, ['c_step' => 'process'], $conf ),
			'payment.url-update' => $this->getUrlUpdate( $view, $args + ['orderid' => $orderItem->getId()], $conf ),
			'payment.url-success' => $this->getUrlConfirm( $view, $args, $conf ),
		];

		$params = array_merge(
			(array) $view->param(),
			(array) $view->request()->getQueryParams(),
			(array) $view->request()->getParsedBody(),
			(array) $view->request()->getAttributes()
		);

		foreach( $service->getAttributeItems() as $item ) {
			$params[$item->getCode()] = $item->getValue();
		}

		$serviceCntl = \Aimeos\Controller\Frontend::create( $this->getContext(), 'service' );
		return $serviceCntl->init( $orderItem, $service->getServiceId(), $urls, $params );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames() : array
	{
		return $this->getContext()->getConfig()->get( $this->subPartPath, $this->subPartNames );
	}


	/**
	 * Returns the URL to the confirm page.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $params Parameters that should be part of the URL
	 * @param array $config Default URL configuration
	 * @return string URL string
	 */
	protected function getUrlConfirm( \Aimeos\MW\View\Iface $view, array $params, array $config ) : string
	{
		/** client/html/checkout/confirm/url/target
		 * Destination of the URL where the controller specified in the URL is known
		 *
		 * The destination can be a page ID like in a content management system or the
		 * module of a software development framework. This "target" must contain or know
		 * the controller that should be called by the generated URL.
		 *
		 * @param string Destination of the URL
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/confirm/url/controller
		 * @see client/html/checkout/confirm/url/action
		 * @see client/html/checkout/confirm/url/config
		 */
		$target = $view->config( 'client/html/checkout/confirm/url/target' );

		/** client/html/checkout/confirm/url/controller
		 * Name of the controller whose action should be called
		 *
		 * In Model-View-Controller (MVC) applications, the controller contains the methods
		 * that create parts of the output displayed in the generated HTML page. Controller
		 * names are usually alpha-numeric.
		 *
		 * @param string Name of the controller
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/confirm/url/target
		 * @see client/html/checkout/confirm/url/action
		 * @see client/html/checkout/confirm/url/config
		 */
		$cntl = $view->config( 'client/html/checkout/confirm/url/controller', 'checkout' );

		/** client/html/checkout/confirm/url/action
		 * Name of the action that should create the output
		 *
		 * In Model-View-Controller (MVC) applications, actions are the methods of a
		 * controller that create parts of the output displayed in the generated HTML page.
		 * Action names are usually alpha-numeric.
		 *
		 * @param string Name of the action
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/confirm/url/target
		 * @see client/html/checkout/confirm/url/controller
		 * @see client/html/checkout/confirm/url/config
		 */
		$action = $view->config( 'client/html/checkout/confirm/url/action', 'confirm' );

		/** client/html/checkout/confirm/url/config
		 * Associative list of configuration options used for generating the URL
		 *
		 * You can specify additional options as key/value pairs used when generating
		 * the URLs, like
		 *
		 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
		 *
		 * The available key/value pairs depend on the application that embeds the e-commerce
		 * framework. This is because the infrastructure of the application is used for
		 * generating the URLs. The full list of available config options is referenced
		 * in the "see also" section of this page.
		 *
		 * @param string Associative list of configuration options
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/confirm/url/target
		 * @see client/html/checkout/confirm/url/controller
		 * @see client/html/checkout/confirm/url/action
		 * @see client/html/url/config
		 */
		$config = $view->config( 'client/html/checkout/confirm/url/config', $config );

		return $view->url( $target, $cntl, $action, $params, [], $config );
	}


	/**
	 * Returns the URL to the current page.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $params Parameters that should be part of the URL
	 * @param array $config Default URL configuration
	 * @return string URL string
	 */
	protected function getUrlSelf( \Aimeos\MW\View\Iface $view, array $params, array $config ) : string
	{
		/** client/html/checkout/standard/url/target
		 * Destination of the URL where the controller specified in the URL is known
		 *
		 * The destination can be a page ID like in a content management system or the
		 * module of a software development framework. This "target" must contain or know
		 * the controller that should be called by the generated URL.
		 *
		 * @param string Destination of the URL
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/standard/url/controller
		 * @see client/html/checkout/standard/url/action
		 * @see client/html/checkout/standard/url/config
		 */
		$target = $view->config( 'client/html/checkout/standard/url/target' );

		/** client/html/checkout/standard/url/controller
		 * Name of the controller whose action should be called
		 *
		 * In Model-View-Controller (MVC) applications, the controller contains the methods
		 * that create parts of the output displayed in the generated HTML page. Controller
		 * names are usually alpha-numeric.
		 *
		 * @param string Name of the controller
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/standard/url/target
		 * @see client/html/checkout/standard/url/action
		 * @see client/html/checkout/standard/url/config
		 */
		$cntl = $view->config( 'client/html/checkout/standard/url/controller', 'checkout' );

		/** client/html/checkout/standard/url/action
		 * Name of the action that should create the output
		 *
		 * In Model-View-Controller (MVC) applications, actions are the methods of a
		 * controller that create parts of the output displayed in the generated HTML page.
		 * Action names are usually alpha-numeric.
		 *
		 * @param string Name of the action
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/standard/url/target
		 * @see client/html/checkout/standard/url/controller
		 * @see client/html/checkout/standard/url/config
		 */
		$action = $view->config( 'client/html/checkout/standard/url/action', 'index' );

		/** client/html/checkout/standard/url/config
		 * Associative list of configuration options used for generating the URL
		 *
		 * You can specify additional options as key/value pairs used when generating
		 * the URLs, like
		 *
		 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
		 *
		 * The available key/value pairs depend on the application that embeds the e-commerce
		 * framework. This is because the infrastructure of the application is used for
		 * generating the URLs. The full list of available config options is referenced
		 * in the "see also" section of this page.
		 *
		 * @param string Associative list of configuration options
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/standard/url/target
		 * @see client/html/checkout/standard/url/controller
		 * @see client/html/checkout/standard/url/action
		 * @see client/html/url/config
		 */
		$config = $view->config( 'client/html/checkout/standard/url/config', $config );

		return $view->url( $target, $cntl, $action, $params, [], $config );
	}


	/**
	 * Returns the URL to the update page.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $params Parameters that should be part of the URL
	 * @param array $config Default URL configuration
	 * @return string URL string
	 */
	protected function getUrlUpdate( \Aimeos\MW\View\Iface $view, array $params, array $config ) : string
	{
		/** client/html/checkout/update/url/target
		 * Destination of the URL where the controller specified in the URL is known
		 *
		 * The destination can be a page ID like in a content management system or the
		 * module of a software development framework. This "target" must contain or know
		 * the controller that should be called by the generated URL.
		 *
		 * @param string Destination of the URL
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/update/url/controller
		 * @see client/html/checkout/update/url/action
		 * @see client/html/checkout/update/url/config
		 */
		$target = $view->config( 'client/html/checkout/update/url/target' );

		/** client/html/checkout/update/url/controller
		 * Name of the controller whose action should be called
		 *
		 * In Model-View-Controller (MVC) applications, the controller contains the methods
		 * that create parts of the output displayed in the generated HTML page. Controller
		 * names are usually alpha-numeric.
		 *
		 * @param string Name of the controller
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/update/url/target
		 * @see client/html/checkout/update/url/action
		 * @see client/html/checkout/update/url/config
		 */
		$cntl = $view->config( 'client/html/checkout/update/url/controller', 'checkout' );

		/** client/html/checkout/update/url/action
		 * Name of the action that should create the output
		 *
		 * In Model-View-Controller (MVC) applications, actions are the methods of a
		 * controller that create parts of the output displayed in the generated HTML page.
		 * Action names are usually alpha-numeric.
		 *
		 * @param string Name of the action
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/update/url/target
		 * @see client/html/checkout/update/url/controller
		 * @see client/html/checkout/update/url/config
		 */
		$action = $view->config( 'client/html/checkout/update/url/action', 'update' );

		/** client/html/checkout/update/url/config
		 * Associative list of configuration options used for generating the URL
		 *
		 * You can specify additional options as key/value pairs used when generating
		 * the URLs, like
		 *
		 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
		 *
		 * The available key/value pairs depend on the application that embeds the e-commerce
		 * framework. This is because the infrastructure of the application is used for
		 * generating the URLs. The full list of available config options is referenced
		 * in the "see also" section of this page.
		 *
		 * @param string Associative list of configuration options
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/update/url/target
		 * @see client/html/checkout/update/url/controller
		 * @see client/html/checkout/update/url/action
		 * @see client/html/url/config
		 */
		$config = $view->config( 'client/html/checkout/update/url/config', $config );

		return $view->url( $target, $cntl, $action, $params, [], $config );
	}


	/**
	 * Tests if one of the products is a subscription
	 *
	 * @param \Aimeos\Map $products Ordered products implementing \Aimeos\MShop\Order\Item\Base\Product\Iface
	 * @return bool True if at least one product is a subscription, false if not
	 */
	protected function isSubscription( \Aimeos\Map $products ) : bool
	{
		foreach( $products as $orderProduct )
		{
			if( $orderProduct->getAttributeItem( 'interval', 'config' ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	public function data( \Aimeos\MW\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\MW\View\Iface
	{
		$view->standardUrlPayment = $this->getUrlSelf( $view, array( 'c_step' => 'payment' ), [] );

		return parent::data( $view, $tags, $expire );
	}
}
