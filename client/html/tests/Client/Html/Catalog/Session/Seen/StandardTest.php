<?php

namespace Aimeos\Client\Html\Catalog\Session\Seen;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Session\Seen\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::view() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testBody()
	{
		$seen = array( 1 => 'html product one', 2 => 'html product two' );
		$this->context->getSession()->set( 'aimeos/catalog/session/seen/list', $seen );

		$this->object->setView( $this->object->data( $this->object->view() ) );
		$output = $this->object->body();

		$this->assertRegExp( '#.*html product two.*html product one.*#smU', $output ); // list is reversed
		$this->assertStringStartsWith( '<section class="catalog-session-seen">', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
