<?php
namespace mangascrape;

class AdminTools {

	/**
	 * Class Variables
	 */
	private $results = null;
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
		if ( isset( $_POST['ms_action'] ) && 'start_scrape' === $_POST['ms_action'] ) {
			$this->start_scrape();
		}
	}

	/**
	 * Start the scrape process
	 */
	private function start_scrape() {
		check_admin_referer( 'get_url', 'get_url_nonce' );

		$folder_name    = $_POST['folder_name'];
		$code_to_scrape = $_POST['code_to_scrape'];

		if ( ! $folder_name || ! $code_to_scrape ) {
			wp_die( 'Variables missing. Please try again' );
		}

        // Parse the links from the provided markup
        $parser = new MSParseMarkup( $code_to_scrape );
        $this->results = $parser->get_results();

        // Only process if we have results in our markup
        if ( sizeof( $this->results ) > 0 ) {

            // Create our destination folders
            $manga_folder_root = MANGASCRAPE_UPLOAD_DIR . MSHelpers::make_valid_foldername( $folder_name );
            MSHelpers::create_dir( $manga_folder_root );

            $manga_folder_zips = $manga_folder_root . '/zips';
            MSHelpers::create_dir( $manga_folder_zips );

            // Download the files
            $downloader = new MSDownloader( $this->results, $manga_folder_zips );
            $downloader->process_downloads();

        }

	}

	/**
	 * Callback function to render the Tools page in the admin
	 */
	public function admin_page() {

		echo '<h1>MangaScrape</h1>';
		echo '<p>Download manga with the tool below!</p>';
		if ( $this->results ) {
			echo '<p style="color:red;">';
            foreach ( $this->results as $result ) {
	            echo basename( $result ) . '<br>';
            }
            echo '</p>';
		}
		?>
		<form method="post">
			<?php wp_nonce_field( 'get_url', 'get_url_nonce' ); ?>
			<input type="hidden" name="ms_action" value="start_scrape" />
            <div>
                <input type="text" name="folder_name" placeholder="Folder to Save To" required="required" aria-required="true">
            </div>
            <div style="padding-top:20px;">
				<textarea name="code_to_scrape" placeholder="Copy/paste the `manga_series_list` element here" style="width:50em;height:20em;" required="required" aria-required="true"></textarea>
			</div>
			<div style="padding-top:20px;">
				<input type="submit" name="submit" value="Scrape!" />
			</div>
		</form>
		<?php

	}

}