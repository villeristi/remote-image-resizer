<?php

namespace RIR;

require_once __DIR__ . '/../vendor/autoload.php';

use Eventviva\ImageResize as Image;
use Symfony\Component\HttpFoundation\Request;

class Resizer {

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Image
	 */
	private $image;

	/**
	 * @var String
	 */
	private $imageSrc;

	/**
	 * @var array
	 */
	private $config;

	/**
	 * @var int
	 */
	private $width;

	/**
	 * @var int
	 */
	private $height;

	/**
	 * @var string
	 */
	private $method;

	/**
	 * @var array
	 */
	private $args;

	public function __construct() {
		$this->request = Request::createFromGlobals();
		$this
			->configure()
			->setImageSrc( $this->request->query->get( 'src' ) )
			->guardHotlinking()
			->parseRequest()
			->assignMethod();

		if ( @getimagesize( $this->getImageSrc() ) ) {
			$this->image = new Image( $this->getImageSrc() );
		} else {
			$this->image = new Image( $this->getConfig( 'notFoundImage' ) );
		}
	}

	/**
	 * @return $this
	 */
	private function parseRequest() {
		$query = $this->request->query;
		$sizes = $query->has( 'size' ) ? array_map( 'intval', preg_split( '/x/', $query->get( 'size' ) ) ) : null;

		// If we have sizes => assign them
		if ( $sizes ) {
			$this->setWidth( $sizes[0] );
			$this->setHeight( count( $sizes ) > 1 ? $sizes[1] : $sizes[0] );
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public static function serve() {
		$instance = new static();

		if ( method_exists( $instance->image, $instance->getMethod() ) ) {
			call_user_func_array( [ $instance->image, $instance->getMethod() ], $instance->getArgs() );
		}

		return $instance->image->output();
	}

	/**
	 * @return $this
	 */
	protected function assignMethod() {
		switch ( true ) {
			case $this->getWidth() && $this->getHeight():
				$this->setMethod( 'crop' );
				$this->setArgs( [
					$this->getWidth(),
					$this->getHeight(),
					true // Allow enlarging
				] );

				return $this;
			case $this->getWidth() && ! $this->getHeight():
				$this->setMethod( 'resizeToWidth' );
				$this->setArgs( [
					$this->getWidth(),
					true // Allow enlarging
				] );

				return $this;
			case ! $this->getWidth() && $this->getHeight():
				$this->setMethod( 'resizeToHeight' );
				$this->setArgs( [
					$this->getHeight(),
					true // Allow enlarging
				] );

				return $this;
			default:
				$this->setMethod( null );

				return $this;
		}
	}

	/**
	 * @return $this
	 */
	protected function guardHotlinking() {
		if ( ! $this->isHostAllowed() ) {
			$this->setImageSrc( $this->getConfig( 'notAllowedImage' ) );
		}

		return $this;
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
	 * @return $this
	 */
	protected function configure() {
		if ( is_file( __DIR__ . '/../config.php' ) ) {
			$this->config = require_once __DIR__ . '/../config.php';
		};

		return $this;
	}

	/**
	 * @param null $key
	 *
	 * @return mixed
	 */
	protected function getConfig( $key = null ) {
		return isset( $key ) ? $this->config[ $key ] : (object) $this->config;
	}

	/**
	 * @return int
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * @param $width
	 */
	public function setWidth( $width ) {
		$this->width = $width;
	}

	/**
	 * @return int
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * @param mixed $height
	 */
	public function setHeight( $height ) {
		$this->height = $height;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param $method
	 */
	public function setMethod( $method ) {
		$this->method = $method;
	}

	/**
	 * @return array
	 */
	public function getArgs() {
		return $this->args;
	}

	/**
	 * @param array $args
	 */
	public function setArgs( array $args ) {
		$this->args = $args;
	}

	/**
	 * @return String
	 */
	public function getImageSrc() {
		return $this->imageSrc;
	}

	/**
	 * @param String $imageSrc
	 *
	 * @return $this
	 */
	public function setImageSrc( $imageSrc ) {
		$this->imageSrc = $imageSrc;

		return $this;
	}
}