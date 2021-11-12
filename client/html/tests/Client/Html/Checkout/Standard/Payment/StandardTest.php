<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Checkout\Standard\Payment;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Payment\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::view() );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();

		unset( $this->object, $this->context );
	}


	public function testHeader()
	{
		$view = \TestHelperHtml::view();
		$view->standardStepActive = 'payment';
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
		$view = \TestHelperHtml::view();
		$view->standardStepActive = 'payment';
		$view->standardSteps = array( 'before', 'payment', 'after' );
		$view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->create();
		$this->object->setView( $this->object->data( $view ) );

		$output = $this->object->body();
		$this->assertStringStartsWith( '<section class="checkout-standard-payment">', $output );
		$this->assertRegExp( '#<li class="row form-item form-group directdebit.accountowner mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="row form-item form-group directdebit.accountno mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="row form-item form-group directdebit.bankcode mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="row form-item form-group directdebit.bankname mandatory">#smU', $output );

		$this->assertGreaterThan( 0, count( $view->paymentServices ) );
	}


	public function testBodyOtherStep()
	{
		$view = \TestHelperHtml::view();
		$this->object->setView( $view );

		$output = $this->object->body();
		$this->assertEquals( '', $output );
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
		$this->object->init();

		$this->assertEquals( 'payment', $this->object->view()->get( 'standardStepActive' ) );
	}


	public function testInitExistingId()
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );

		if( ( $service = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'Service item not found' );
		}

		$view = \TestHelperHtml::view();

		$param = array(
			'c_paymentoption' => $service->getId(),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->init();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'unitpaymentcode', $basket->getService( 'payment', 0 )->getCode() );
	}


	public function testInitInvalidId()
	{
		$view = \TestHelperHtml::view();

		$param = array( 'c_paymentoption' => -1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->expectException( '\\Aimeos\\MShop\\Exception' );
		$this->object->init();
	}


	public function testInitNotExistingAttributes()
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );

		if( ( $service = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'Service item not found' );
		}

		$view = \TestHelperHtml::view();

		$param = array(
			'c_paymentoption' => $service->getId(),
			'c_payment' => array(
				$service->getId() => array(
					'notexisting' => 'invalid value',
				),
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->expectException( '\\Aimeos\\Controller\\Frontend\\Basket\\Exception' );
		$this->object->init();
	}
}
