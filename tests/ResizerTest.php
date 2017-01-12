<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Villeristi\Resizer;

class ResizerTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
	}

	/** @test */
	public function it_should_be_instantiable() {
		$instance = new Resizer();
		$this->assertInstanceOf( 'Villeristi\Resizer', $instance );
	}
}
