<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Process\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::view() );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();
		unset( $this->object, $this->context );
	}


	public function testHeader()
	{
		$view = $this->object->view();
		$view->standardStepActive = 'process';
		$this->object->setView( $this->object->data( $view ) );

		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testHeaderSkip()
	{
		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testBody()
	{
		$view = $this->object->view();
		$view->standardStepActive = 'process';
		$this->object->setView( $this->object->data( $view ) );

		$output = $this->object->body();
		$this->assertStringStartsWith( '<div class="checkout-standard-process">', $output );
		$this->assertEquals( 'http://baseurl/checkout/index/?c_step=payment', $view->standardUrlPayment );
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testInit()
	{
		$view = $this->object->view();
		$param = array( 'c_step' => 'process', 'cs_order' => 1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Process\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['processPayment'] )
			->getMock();
		$object->setView( $view );

		$basketMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Basket\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$orderMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Order\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$form = new \Aimeos\MShop\Common\Helper\Form\Standard( 'url', 'POST', [], true );
		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE', ['price'] );
		$service = \Aimeos\MShop::create( $this->context, 'service' )->find( 'paypalexpress' );

		$basketMock->addProduct( $product );
		$basketMock->addService( $service );
		$object->expects( $this->once() )->method( 'processPayment' )->will( $this->returnValue( $form ) );
		$basketMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $basketMock->get()->setId( '123' ) ) );
		$orderMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $orderItem->setId( '123' ) ) );

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', $basketMock );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', $orderMock );

		$object->init();

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', null );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', null );

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', [] ) ) );
		$this->assertEquals( 'url', $view->standardUrlNext );
		$this->assertEquals( 'POST', $view->standardMethod );
		$this->assertEquals( [], $view->standardProcessParams );
		$this->assertEquals( true, $view->standardUrlExternal );
	}


	public function testInitNoPayment()
	{
		$view = $this->object->view();
		$param = array( 'c_step' => 'process', 'cs_order' => 1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Process\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['processPayment'] )
			->getMock();
		$object->setView( $view );

		$basketMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Basket\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$orderMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Order\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store', 'save'] )
			->getMock();

		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$basketMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $basketMock->get()->setId( '123' ) ) );
		$orderMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $orderItem->setId( '123' ) ) );
		$orderMock->expects( $this->once() )->method( 'save' )->will( $this->returnValue( $orderItem ) );

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', $basketMock );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', $orderMock );

		$object->init();

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', null );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', null );

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', [] ) ) );
		$this->assertEquals( 'http://baseurl/checkout/confirm/', $view->standardUrlNext );
	}


	public function testInitNoService()
	{
		$view = $this->object->view();
		$param = array( 'c_step' => 'process', 'cs_order' => 1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Process\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['processPayment'] )
			->getMock();
		$object->setView( $view );

		$basketMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Basket\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$orderMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Order\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['save', 'store'] )
			->getMock();

		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE', ['price'] );
		$service = \Aimeos\MShop::create( $this->context, 'service' )->find( 'paypalexpress' );

		$basketMock->addProduct( $product );
		$basketMock->addService( $service );
		$object->expects( $this->once() )->method( 'processPayment' )->will( $this->returnValue( null ) );
		$basketMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $basketMock->get()->setId( '123' ) ) );
		$orderMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $orderItem->setId( '123' ) ) );
		$orderMock->expects( $this->once() )->method( 'save' )->will( $this->returnValue( $orderItem ) );

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', $basketMock );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', $orderMock );

		$object->init();

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', null );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', null );

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', [] ) ) );
		$this->assertTrue( isset( $view->standardUrlNext ) );
		$this->assertEquals( 'POST', $view->standardMethod );
	}


	public function testInitNoStep()
	{
		$this->assertTrue( $this->object->init() );
	}


	public function testInitHtmlException()
	{
		$view = $this->object->view();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['c_step' => 'process', 'cs_order' => 1] );
		$view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Basket\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'store' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', $mock );
		$this->object->init();
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', null );

		$this->assertIsArray( $view->standardErrorList );
	}


	public function testInitFrontendException()
	{
		$view = $this->object->view();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['c_step' => 'process', 'cs_order' => 1] );
		$view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Basket\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'store' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', $mock );
		$this->object->init();
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', null );

		$this->assertIsArray( $view->standardErrorList );
	}


	public function testInitMShopException()
	{
		$view = $this->object->view();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['c_step' => 'process', 'cs_order' => 1] );
		$view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Basket\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'store' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', $mock );
		$this->object->init();
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', null );

		$this->assertIsArray( $view->standardErrorList );
	}


	public function testInitException()
	{
		$view = $this->object->view();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['c_step' => 'process', 'cs_order' => 1] );
		$view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Basket\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'store' )
			->will( $this->throwException( new \RuntimeException() ) );

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', $mock );
		$this->object->init();
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', null );

		$this->assertIsArray( $view->standardErrorList );
	}


	/**
	 * @param string $date
	 */
	protected function getOrder( $date )
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $date ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No order found' );
		}

		return $item;
	}
}
