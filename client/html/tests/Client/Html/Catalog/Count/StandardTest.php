<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Count;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Count\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$this->object->setView( $this->object->data( \TestHelperHtml::getView() ) );

		$output = $this->object->header();

		$this->assertNotNull( $output );
	}


	public function testGetHeaderException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Count\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertEquals( null, $object->header() );
	}


	public function testGetBody()
	{
		$this->object->setView( $this->object->data( \TestHelperHtml::getView() ) );

		$output = $this->object->body();

		$this->assertStringContainsString( 'var catalogCounts', $output );
	}


	public function testGetBodyException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Count\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertEquals( null, $object->body() );
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'tree', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\HTML\\Iface', $client );
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

}
