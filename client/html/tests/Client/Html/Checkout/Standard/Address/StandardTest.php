<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Checkout\Standard\Address;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' )->getId() );

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Address\Standard( $this->context );
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
		$view->standardStepActive = 'address';
		$this->object->setView( $this->object->data( $view ) );

		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testHeaderSkip()
	{
		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testHeaderOtherStep()
	{
		$view = $this->object->view();
		$view->standardStepActive = 'xyz';
		$this->object->setView( $this->object->data( $view ) );

		$output = $this->object->header();
		$this->assertEquals( '', $output );
	}


	public function testBody()
	{
		$item = $this->getCustomerItem();
		$this->context->setUserId( $item->getId() );

		$view = $this->object->view();
		$view->standardStepActive = 'address';
		$view->standardSteps = array( 'address', 'after' );
		$view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->create();
		$this->object->setView( $this->object->data( $view ) );

		$output = $this->object->body();
		$this->assertStringStartsWith( '<section class="checkout-standard-address">', $output );

		$this->assertGreaterThanOrEqual( 0, count( $view->addressLanguages ) );
		$this->assertGreaterThanOrEqual( 0, count( $view->addressCountries ) );
	}


	public function testBodyOtherStep()
	{
		$view = $this->object->view();
		$view->standardStepActive = 'xyz';
		$this->object->setView( $this->object->data( $view ) );

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

		$this->assertEquals( 'address', $this->object->view()->get( 'standardStepActive' ) );
	}


	/**
	 * Returns the customer item for the given code
	 *
	 * @param string $code Unique customer code
	 * @throws \Exception If no customer item is found
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item object
	 */
	protected function getCustomerItem( $code = 'test@example.com' )
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'Customer item not found' );
		}

		return $item;
	}
}
