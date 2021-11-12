<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Account\Review\Todo;


/**
 * Default implementation of acount review todo HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Summary\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/account/review/todo/subparts
	 * List of HTML sub-clients rendered within the account review todo section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The todo of the HTML sub-clients
	 * determines the todo of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the todo of the output by retodoing the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or retodoing content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2019.07
	 * @category Developer
	 */
	private $subPartPath = 'client/html/account/review/todo/subparts';
	private $subPartNames = [];


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		$view = $this->view();

		$html = '';
		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->body( $uid );
		}
		$view->todoBody = $html;

		/** client/html/account/review/todo/template-body
		 * Relative path to the HTML body template of the account review todo client.
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
		 * @since 2019.07
		 * @category Developer
		 * @see client/html/account/review/todo/template-header
		 */
		$tplconf = 'client/html/account/review/todo/template-body';
		$default = 'account/review/todo-body-standard';

		return $view->render( $view->config( $tplconf, $default ) );
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
		/** client/html/account/review/todo/decorators/excludes
		 * Excludes decorators added by the "common" option from the account review todo html client
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
		 *  client/html/account/review/todo/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2019.07
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/account/review/todo/decorators/global
		 * @see client/html/account/review/todo/decorators/local
		 */

		/** client/html/account/review/todo/decorators/global
		 * Adds a list of globally available decorators only to the account review todo html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
		 *
		 *  client/html/account/review/todo/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2019.07
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/account/review/todo/decorators/excludes
		 * @see client/html/account/review/todo/decorators/local
		 */

		/** client/html/account/review/todo/decorators/local
		 * Adds a list of local decorators only to the account review todo html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\Html\Account\Decorator\*") around the html client.
		 *
		 *  client/html/account/review/todo/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\Html\Account\Decorator\Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2019.07
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/account/review/todo/decorators/excludes
		 * @see client/html/account/review/todo/decorators/global
		 */

		return $this->createSubClient( 'account/review/todo/' . $type, $name );
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init()
	{
		$view = $this->view();

		if( ( $reviews = $view->param( 'review-todo', [] ) ) !== [] )
		{
			$context = $this->getContext();
			$cntl = \Aimeos\Controller\Frontend::create( $context, 'review' );
			$addr = \Aimeos\Controller\Frontend::create( $context, 'customer' )->get()->getPaymentAddress();

			foreach( $reviews as $values ) {
				$cntl->save( $cntl->create( $values )->setDomain( 'product' )->setName( $addr->getFirstName() ) );
			}

			$view->reviewInfoList = [$view->translate( 'client', 'Thank you for your review!' )];
		}

		parent::init();
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
		$products = [];
		$context = $this->getContext();
		$config = $context->getConfig();

		/** client/html/account/review/todo/size
		 * Maximum number of products shown for review
		 *
		 * After customers bought products, they can write a review for those items.
		 * The products bought last will be displayed first for review and this
		 * setting limits the number of products shown in the account page.
		 *
		 * @param int Number of products
		 * @since 2020.10
		 * @see client/html/account/review/todo/days-after
		 */
		$size = $config->get( 'client/html/account/review/todo/size', 10 );

		/** client/html/account/review/todo/days-after
		 * Number of days after the product can be reviewed
		 *
		 * After customers bought products, they can write a review for those items.
		 * To avoid fake or revenge reviews, the option for reviewing the products is
		 * shown after the configured number of days to customers.
		 *
		 * @param int Number of days
		 * @since 2020.10
		 * @see client/html/account/review/todo/size
		 */
		$days = $config->get( 'client/html/account/review/todo/days-after', 0 );

		$orders = \Aimeos\Controller\Frontend::create( $context, 'order' )
			->compare( '>', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_PENDING )
			->compare( '<', 'order.base.ctime', date( 'Y-m-d H:i:s', time() - $days * 86400 ) )
			->uses( ['order/base', 'order/base/product'] )
			->sort( '-order.base.ctime' )
			->slice( 0, $size )
			->search();

		$prodMap = $orders->getBaseItem()->getProducts()->flat()
			->col( 'order.base.product.id', 'order.base.product.productid' );

		$exclude = \Aimeos\Controller\Frontend::create( $context, 'review' )
			->for( 'product', $prodMap->keys()->toArray() )
			->slice( 0, $prodMap->count() )
			->list()->getRefId();

		if( ( $prodIds = $prodMap->keys()->diff( $exclude )->toArray() ) !== [] )
		{
			$productItems = \Aimeos\Controller\Frontend::create( $context, 'product' )
				->uses( ['text' => ['name'], 'media' => ['default']] )
				->product( $prodIds )
				->search();

			foreach( $prodMap as $prodId => $ordProdId )
			{
				if( $item = $productItems->get( $prodId ) ) {
					$products[$prodId] = $item->set( 'orderProductId', $ordProdId );
				}
			}
		}

		$view->todoProductItems = map( $products )->filter()->take( $size );

		return parent::data( $view, $tags, $expire );
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
}
