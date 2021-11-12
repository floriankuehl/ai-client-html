<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


namespace Aimeos\Client\Html\Catalog\Detail\Service;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Service\Standard( \TestHelperHtml::getContext() );
		$this->object->setView( \TestHelperHtml::view() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testBody()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->object->view(), $tags, $expire ) );
		$output = $this->object->body();

		$this->assertEquals( '', $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 3, count( $tags ) );

		$rendered = $this->object->view()->block()->get( 'catalog/detail/service' );
		$this->assertStringStartsWith( '<div class="catalog-detail-service', $rendered );
	}

	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
