<?php

namespace mangascrape;

class MSParseMarkup {

	private array $results = array();

	function __construct( $code_to_scrape ) {
		$this->parse_markup( $code_to_scrape );
	}

	private function parse_markup( $code_to_scrape ) {
		require_once( MANGASCRAPE_DIR_PATH . 'lib/simple_html_dom.php' );

		$html = str_get_html( $code_to_scrape );

		foreach ( $html->find( 'a' ) as $item ) {
			if ( isset( $item->attr['download'] ) ) {
				$this->results[] = $item->attr['href'];
			}
		}

	}

	/**
	 * @return array
	 */
	public function get_results(): array {
		return array_map( array( $this, 'clean_text' ), $this->results );
	}

	/**
	 * @param $str
	 *
	 * @return string|string[]
	 */
	private function clean_text( $str ) {
		return str_replace( '\\"', '', $str );
	}

}