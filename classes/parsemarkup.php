<?php

namespace mangascrape;

class MSParseMarkup {

	private $results = array();
	private $code_to_scrape = '';

	function __construct( $code_to_scrape ) {
		$this->code_to_scrape = $code_to_scrape;
	}

	private function parse_markup( $mode ) {
		require_once( plugin_dir_path( __FILE__ ) . '../lib/simple_html_dom.php' );

		$html = str_get_html( $this->code_to_scrape );

		if ( 'zips' === $mode ) {
			foreach ( $html->find( 'a' ) as $item ) {
				if ( isset( $item->attr['download'] ) ) {
					$this->results[] = $item->attr['href'];
				}
			}
		} elseif ( 'jpgs' === $mode ) {
			foreach ( $html->find( 'img' ) as $item ) {
				if ( isset( $item->attr['id'] ) && '\"gohere\"' === $item->attr['id'] ) {
					$this->results[] = $item->attr['src'];
				}
			}
		}

	}

	public function get_results( $mode = 'zips' ): array {
		$this->parse_markup( $mode );
		return array_map( array( $this, 'clean_text' ), $this->results );
	}

	private function clean_text( $str ) {
		return str_replace( '\\"', '', $str );
	}

}