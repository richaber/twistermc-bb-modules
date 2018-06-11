<?php
/**
 * TwisterMC_BB_DOMDocument_Utility class file.
 *
 * @package TwisterMC_BB_Modules
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class TwisterMC_BB_DOMDocument_Utility
 *
 * A convenience wrapper class for working with DOMDocument and partial markup strings.
 *
 * @property string            OPEN_HTML_TAG
 * @property string            OPEN_BODY_TAG
 * @property string            CLOSE_BODY_TAG
 * @property string            CLOSE_HTML_TAG
 * @property string            $original_content
 * @property string            $processed_content
 * @property string            $character_encoding
 * @property \DOMDocument|bool $dom_document
 * @property \DOMXPath|bool    $dom_xpath
 */
class TwisterMC_BB_DOMDocument_Utility {

	/**
	 * @var string OPEN_HTML_TAG
	 */
	const OPEN_HTML_TAG = '<html>';

	/**
	 * @var string OPEN_BODY_TAG
	 */
	const OPEN_BODY_TAG = '<body>';

	/**
	 * @var string CLOSE_BODY_TAG
	 */
	const CLOSE_BODY_TAG = '</body>';

	/**
	 * @var string CLOSE_HTML_TAG
	 */
	const CLOSE_HTML_TAG = '</html>';

	/**
	 * The character set of the incoming content string.
	 *
	 * @var string
	 */
	public $character_encoding = 'UTF-8';

	/**
	 * A string of pre-processed original content, else empty string.
	 *
	 * @var string
	 */
	public $original_content = '';

	/**
	 * A string of post-processed content, else empty string.
	 *
	 * @var string
	 */
	public $processed_content = '';

	/**
	 * A DOMDocument constructed from the content, else false.
	 *
	 * @var \DOMDocument|bool
	 */
	public $dom_document = false;

	/**
	 * A DOMXPath for use with DOMDocument, else false.
	 *
	 * @var \DOMXPath|bool
	 */
	public $dom_xpath = false;

	/**
	 * TwisterMC_BB_DOMDocument_Utility constructor.
	 *
	 * WordPress up to version 2.1.3 used the latin1 (iso-8859-1) character set.
	 * WordPress >= 2.2 defaults to using utf8 (UTF-8) for the character set.
	 *
	 * @link http://www.php.net/manual/en/mbstring.supported-encodings.php PHP supported character encodings.
	 *
	 * @param string $content            Required. The partial HTML markup string to load into DOMDocument.
	 * @param string $character_encoding Optional. The encoding of the characters in the HTML markup string. Defaults to 'UTF-8'.
	 */
	public function __construct( $content = '', $character_encoding = 'UTF-8' ) {

		/**
		 * Set the original content.
		 */
		$this->set_original_content( $content );

		/**
		 * Prime the processed content in case we need to return early!
		 */
		$this->set_processed_content( $content );

		/**
		 * Set the character encoding.
		 */
		$this->set_character_encoding( $character_encoding );

		/**
		 * Setup the DOMDocument.
		 */
		$this->set_dom_document();

		/**
		 * Load the content into the DOMDocument.
		 */
		$this->load_html();

		/**
		 * Setup the DOMXpath.
		 */
		$this->set_dom_xpath();
	}

	/**
	 * Set the character encoding.
	 *
	 * @param string $character_encoding
	 */
	public function set_character_encoding( $character_encoding ) {
		$this->character_encoding = $character_encoding;
	}

	/**
	 * Get the character encoding.
	 *
	 * @return string
	 */
	public function get_character_encoding() {
		return $this->character_encoding;
	}

	/**
	 * Set the original content.
	 *
	 * @param string $content
	 */
	public function set_original_content( $content ) {
		$this->original_content = $content;
	}

	/**
	 * Get the original content.
	 *
	 * @return string
	 */
	public function get_original_content() {
		return $this->original_content;
	}

	/**
	 * Set our processed content.
	 *
	 * @param string $content
	 */
	public function set_processed_content( $content ) {
		$this->processed_content = $content;
	}

	/**
	 * Get our processed content.
	 *
	 * @return string
	 */
	public function get_processed_content() {
		return $this->processed_content;
	}

	/**
	 * Setup our DOMDocument.
	 *
	 * @link http://php.net/manual/en/class.domdocument.php
	 */
	public function set_dom_document() {

		$this->dom_document = new DOMDocument( '1.0', $this->get_character_encoding() );

		/**
		 * Disable strict error checking that can throw a DOMException.
		 */
		$this->dom_document->strictErrorChecking = false; // @codingStandardsIgnoreLine

		/**
		 * Preserve whitespace.
		 */
		$this->dom_document->preserveWhiteSpace = true; // @codingStandardsIgnoreLine
	}

	/**
	 * Get our DOMDocument.
	 *
	 * @return bool|\DOMDocument
	 */
	public function get_dom_document() {
		return $this->dom_document;
	}

	/**
	 * Load the content into our DOMDocument.
	 *
	 * @link http://php.net/manual/en/domdocument.loadhtml.php
	 */
	public function load_html() {

		/**
		 * Enable user error handling.
		 *
		 * DOMDocument::loadHTML throws warnings/errors on malformed/imperfect markup, this will silence that.
		 */
		libxml_use_internal_errors( true );

		/**
		 * Load the content into the DOMDocument, in a UTF-8 safe way to avoid mangling characters.
		 */
		$this->dom_document->loadHTML(
			mb_convert_encoding(
				self::OPEN_HTML_TAG . self::OPEN_BODY_TAG . $this->get_original_content() . self::CLOSE_BODY_TAG . self::CLOSE_HTML_TAG,
				'HTML-ENTITIES',
				$this->get_character_encoding()
			)
		);

		/**
		 * Disable user error handling. Disabling also clears any existing libxml errors.
		 */
		libxml_use_internal_errors( false );
	}

	/**
	 * Set our DOMXPath.
	 *
	 * Use DOMXPath to make locating elements by DOM ID, CSS Class, or any other valid Xpath query, easier.
	 *
	 * @link http://php.net/manual/en/class.domxpath.php
	 * @link https://www.w3.org/TR/1999/REC-xpath-19991116/
	 */
	public function set_dom_xpath() {
		/**
		 * @var \DOMXPath $xpath
		 */
		$this->dom_xpath = new DOMXPath( $this->dom_document );
	}

	/**
	 * Get our DOMXPath.
	 *
	 * @return bool|\DOMXPath
	 */
	public function get_dom_xpath() {
		return $this->dom_xpath;
	}

	/**
	 * Save the DOMDocument to a string.
	 *
	 * @Link http://php.net/manual/en/domdocument.savehtml.php
	 *
	 * @return string
	 */
	public function save_dom_document() {

		/**
		 * Write the DOMDocument to a string.
		 *
		 * @var string $processed_content
		 */
		$processed_content = $this->dom_document->saveHTML();

		/**
		 * Replace the unnecessary doctype element.
		 */
		$processed_content = preg_replace( '/^<!DOCTYPE.+?>/', '', $processed_content );

		/**
		 * Replace unnecessary html and body elements.
		 */
		$processed_content = str_replace(
			array(
				self::OPEN_HTML_TAG,
				self::CLOSE_HTML_TAG,
				self::OPEN_BODY_TAG,
				self::CLOSE_BODY_TAG,
			),
			array( '', '', '', '' ),
			$processed_content
		);

		/**
		 * Trim away any leading/trailing whitespace.
		 */
		return trim( $processed_content );
	}
}
