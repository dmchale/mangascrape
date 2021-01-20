<?php
namespace mangascrape;

class AdminTools {

	/**
	 * Class Variables
	 */
	private $result = null;
	private $message = '';

	/**
	 * AdminTools constructor.
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Register Tools menu with WordPress
	 */
	public function admin_menu() {
		$hook = add_management_page( 'MangaScrape', 'MangaScrape', 'install_plugins', 'magascrape_admin', array( $this, 'admin_page' ), '' );
		add_action( "load-$hook", array( $this, 'admin_page_load' ) );
	}

	/**
	 * When starting to load Tools page, do some checks
	 */
	function admin_page_load() {
		// ...
		if ( isset( $_POST['ms_action'] ) && 'start_scrape' == $_POST['ms_action'] ) {
			$this->start_scrape();
		}
	}

	/**
	 * Start the scrape process
	 */
	private function start_scrape() {
		check_admin_referer( 'get_url', 'get_url_nonce' );

		$code_to_scrape = $_POST['code_to_scrape'];
		if ( $code_to_scrape ) {
			$obj = new ParseMarkup( $code_to_scrape );
		    $this->result = $obj->get_results();
		}

	}

	/**
	 * Callback function to render the Tools page in the admin
	 */
	public function admin_page() {

		echo '<h1>MangaScrape</h1>';
		echo '<p>Download manga with the tool below!</p>';
		if ( $this->result ) {
			echo '<p style="color:red;">';
            foreach ( $this->result as $result ) {
	            echo $result . '<br>';
            }
            echo '</p>';
		}
		?>
		<form method="post">
			<?php wp_nonce_field( 'get_url', 'get_url_nonce' ); ?>
			<input type="hidden" name="ms_action" value="start_scrape" />
			<div>
				<textarea name="code_to_scrape" placeholder="Copy/paste the `manga_series_list` element here" style="width:50em;height:20em;"></textarea>
			</div>
			<div style="padding-top:20px;">
				<input type="submit" name="submit" value="Scrape!" />
			</div>
		</form>
		<?php

	}

}