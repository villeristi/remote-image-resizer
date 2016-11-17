<?php

namespace RIR;

require_once __DIR__ . '/../vendor/autoload.php';

use Exception;
use Eventviva\ImageResize as Image;
use Symfony\Component\HttpFoundation\Request;

class Resizer {

	private $request;
	private $image;
	private $config;

	public function __construct() {
		$this->request = Request::createFromGlobals();
		$this->configure();
		$this->guardHotlinking();
		$this->image = new Image( $this->request->query->get( 'src' ) );
	}

	public function serve() {
		// Check for method
		// Check width & height
		// Ouput
		return $this->image->output();
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	protected function guardHotlinking() {
		if ( ! $this->isHostAllowed() ) {
			throw new Exception( 'Not valid' );
		}

		return true;
	}

	/**
	 * @return bool
	 */
	protected function isHostAllowed() {

		if ( in_array( '*', $this->getConfig( 'allowedHosts' ) ) ) {
			return true;
		}

		return in_array( $this->request->headers->get( 'host' ), $this->getConfig( 'allowedHosts' ) );
	}

	/**
	 * @return bool|mixed
	 */
	private function configure() {
		if ( is_file( __DIR__ . '/../config.php' ) ) {
			$this->config = require_once __DIR__ . '/../config.php';
		};

		return false;
	}

	/**
	 * @param null $key
	 *
	 * @return mixed
	 */
	public function getConfig( $key = null ) {
		return isset( $key ) ? $this->config[ $key ] : (object) $this->config;
	}
}