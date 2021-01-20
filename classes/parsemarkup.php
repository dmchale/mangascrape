<?php

namespace mangascrape;

class ParseMarkup {

	private $results = array();

	function __construct( $code_to_scrape ) {
		$this->parse_markup( $code_to_scrape );
	}

	private function parse_markup( $code_to_scrape ) {
		require_once( plugin_dir_path( __FILE__ ) . '../lib/simple_html_dom.php' );

		$html = str_get_html( $code_to_scrape );

		foreach ($html->find('a') as $item) {
			if ( isset( $item->attr['download'] ) ) {
				$this->results[] = $item->attr['href'];
			}
		}

	}

	public function get_results() {
		return array_map( array( $this, 'clean_text' ), $this->results );
	}

	private function clean_text( $str ) {
		return str_replace( '\\"', '', $str );
	}

}