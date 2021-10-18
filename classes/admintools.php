<?php

namespace mangascrape;

class AdminTools {

	/**
	 * Class Variables
	 */
	private array $results;
	private string $message;

	/**
	 * AdminTools constructor.
	 */
	function __construct() {
        $this->initialize_variables();
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Does what it says on the tin
	 */
    private function initialize_variables() {
	    $this->results = array();
	    $this->message = '';
    }

	/**
	 * Register Tools menu with WordPress
	 */
	public function admin_menu() {
		$hook = add_management_page( 'MangaScrape', 'MangaScrape', 'install_plugins', 'magascrape_admin', array(
			$this,
			'admin_page'
		), '' );
		add_action( "load-$hook", array( $this, 'admin_page_load' ) );
	}

	/**
	 * When starting to load Tools page, do some checks
	 */
	function admin_page_load() {
		// ...
		if ( isset( $_POST['ms_action'] ) ) {
			if ( 'start_scrape' === $_POST['ms_action'] ) {
				$this->start_scrape();
			} elseif ( 'start_manual_jpgs' === $_POST['ms_action'] ) {
				$this->manually_get_jpgs();
			} elseif ( 'start_explode_zips' === $_POST['ms_action'] ) {
				$this->explode_zips();
			} elseif ( 'start_make_pdfs' === $_POST['ms_action'] ) {
				$this->make_pdfs();
			}
		} elseif ( isset( $_GET['tab'] ) && 'kill_first_steps' === $_GET['tab'] ) {
			$this->kill_first_steps();
		}
	}

    private function kill_first_steps() {
	    // Check permissions
	    if ( ! current_user_can( 'manage_options' ) ) {
            wp_die('Invalid permissions');
	    }

	    // Collect vars
	    $folder_name    = $_GET['folder'];

	    // Confirm we have all the vars we expect
	    if ( ! $folder_name ) {
		    wp_die( 'Variables missing. Please try again' );
	    }

        // Get the zips/jpgs folder paths and delete them with their contents
	    $folder_name = MSHelpers::make_valid_foldername( $folder_name );
	    $manga_folder_root = MANGASCRAPE_UPLOAD_DIR . $folder_name;
	    $manga_folder_zips = $manga_folder_root . '/zips';
        $manga_folder_jpgs = $manga_folder_root . '/jpgs';

        if ( is_dir( $manga_folder_zips ) ) {
	        $this->delete_directory( $manga_folder_zips );
        } else {
	        // Echo error message
	        $this->message .= sprintf( '<p>Zips folder not found for `%s`</p>', $manga_folder_zips );
        }

	    if ( is_dir( $manga_folder_jpgs ) ) {
		    $this->delete_directory( $manga_folder_jpgs );
	    } else {
		    // Echo error message
		    $this->message .= sprintf( '<p>Jpgs folder not found for `%s`</p>', $manga_folder_jpgs );
	    }

        // Output final message
	    $this->message .= '<p>Finished deleting zips and jpgs!</p>';

    }


	private function delete_directory( $dir ) {
		if ( ! file_exists( $dir ) ) {
			return true;
		}

		if ( ! is_dir( $dir ) ) {
			return unlink( $dir );
		}

		foreach ( scandir( $dir ) as $item) {
			if ( $item == '.' || $item == '..' ) {
				continue;
			}

			if ( ! $this->delete_directory( $dir . DIRECTORY_SEPARATOR . $item ) ) {
				return false;
			}

		}

		return rmdir( $dir );
	}


	private function manually_get_jpgs() {
		// Check nonce
		check_admin_referer( 'manual_jpgs', 'manual_jpgs_nonce' );

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Collect vars
		$folder_name    = $_POST['folder_name'];
		$code_to_scrape = $_POST['code_to_scrape'];

		// Confirm we have all the vars we expect
		if ( ! $folder_name || ! $code_to_scrape ) {
			wp_die( 'Variables missing. Please try again' );
		}

		// TODO: Finish processing code
//		// Create our destination folder so we can save the PDF files somewhere
//		$manga_folder_root = MANGASCRAPE_UPLOAD_DIR . MSHelpers::make_valid_foldername( $folder_name );
//		$manga_folder_pdfs = $manga_folder_root . '/pdfs';
//		MSHelpers::create_dir( $manga_folder_pdfs, true );
//
//		// Make the PDF files
//		$manga_folder_jpgs = $manga_folder_root . '/jpgs';
//		$pdf_maker         = new MSPDFMaker( $manga_folder_jpgs, $manga_folder_pdfs );
//		$pdf_maker->make_pdfs();

		// Echo message to user when we're done
		$this->message .= 'Completed getting JPG files manually!';

    }


	/**
	 * Create PDF files from the JPG files we already downloaded
	 */
	private function make_pdfs() {

		// Check nonce
		check_admin_referer( 'make_pdfs', 'make_pdfs_nonce' );

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Collect vars
		$folder_name = $_POST['folder_name'];

		// Confirm we have all the vars we expect
		if ( ! $folder_name ) {
			wp_die( 'Variables missing. Please try again' );
		}

		// Create our destination folder so we can save the PDF files somewhere
		$folder_name = MSHelpers::make_valid_foldername( $folder_name );
		$manga_folder_root = MANGASCRAPE_UPLOAD_DIR . $folder_name;
		$manga_folder_pdfs = $manga_folder_root . '/pdfs';
		MSHelpers::create_dir( $manga_folder_pdfs, true );

		// Make the PDF files
		$manga_folder_jpgs = $manga_folder_root . '/jpgs';
		$pdf_maker         = new MSPDFMaker( $manga_folder_jpgs, $manga_folder_pdfs );
		$pdf_maker->make_pdfs();

		// Echo message to user when we're done
		$this->message .= 'Completed making PDF files!';
		$this->message .= sprintf( '<br /><br /><a href="?page=magascrape_admin&tab=kill_first_steps&folder=%s">Click here to kill the zips and jpgs</a>', $folder_name );

	}


	/**
	 * Unpack the zip files into folders with all the jpg files found within
	 */
	private function explode_zips() {

		// Check nonce
		check_admin_referer( 'explode_zips', 'explode_zips_nonce' );

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Collect vars
		$folder_name = $_POST['folder_name'];

		// Confirm we have all the vars we expect
		if ( ! $folder_name ) {
			wp_die( 'Variables missing. Please try again' );
		}

		// Create our destination folder so we can explode all the zip files somewhere
		$folder_name = MSHelpers::make_valid_foldername( $folder_name );
		$manga_folder_root = MANGASCRAPE_UPLOAD_DIR . $folder_name;
		$manga_folder_jpgs = $manga_folder_root . '/jpgs';
		MSHelpers::create_dir( $manga_folder_jpgs, true );

		// Explode all the zips now
		$manga_folder_zips = $manga_folder_root . '/zips';
		$exploder          = new MSExploder( $manga_folder_zips, $manga_folder_jpgs );
		$exploder->detonate();

		// Echo message to user when we're done
		$this->message .= 'Completed exploding zip files!';
		$this->message .= sprintf( '<br /><br /><a href="?page=magascrape_admin&tab=make_pdfs&folder=%s">Continue to making PDFs</a>', urlencode( $folder_name ) );

	}


	/**
	 * Start the scrape process
	 */
	private function start_scrape() {

		// Check nonce
		check_admin_referer( 'get_zips', 'get_zips_nonce' );

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Collect vars
		$folder_name    = $_POST['folder_name'];
		$code_to_scrape = $_POST['code_to_scrape'];

		// Confirm we have all the vars we expect
		if ( ! $folder_name || ! $code_to_scrape ) {
			wp_die( 'Variables missing. Please try again' );
		}

		// Parse the links from the provided markup
		$parser        = new MSParseMarkup( $code_to_scrape );
		$this->results = $parser->get_results();

		// Only process if we have results in our markup
		if ( sizeof( $this->results ) > 0 ) {

			// Create our destination folders
			$folder_name = MSHelpers::make_valid_foldername( $folder_name );
			$manga_folder_root = MANGASCRAPE_UPLOAD_DIR . $folder_name;
			MSHelpers::create_dir( $manga_folder_root );

			$manga_folder_zips = $manga_folder_root . '/zips';
			MSHelpers::create_dir( $manga_folder_zips );

			// Download the files
			$downloader    = new MSDownloader( $this->results, $manga_folder_zips );
			$downloader->process_downloads();

			// Echo message to user when we're done
			$this->message .= 'Completed parsing HTML and download zip files!';
			$this->message .= sprintf( '<br /><br /><a href="?page=magascrape_admin&tab=explode_zips&folder=%s">Continue to extracting zips</a>', urlencode( $folder_name ) );

		}

	}

	/**
	 * Callback function to render the Tools page in the admin
	 */
	public function admin_page() {

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			echo 'You do not have permissions to view this page, cheater';
			return;
		}

		echo '<h1>MangaScrape</h1>';
		echo '<p>Download manga with the tool below!</p>';
		if ( isset( $this->results ) ) {
			echo '<p style="color:red;">';
			foreach ( $this->results as $result ) {
				echo basename( $result ) . '<br>';
			}
			echo '</p>';
		}
		if ( isset( $this->message ) && '' !== $this->message ) {
			echo '<p style="color:red;">';
			echo $this->message;
			echo '</p>';
		}

        // Prefill the folder name if one was passed
		$folder_name = ( isset( $_GET['folder'] ) ) ? htmlspecialchars( $_GET['folder'] ) : '';

		// See if one of the tabs is currently selected, but only allow approved values
		$tab = '';
		if ( isset( $_GET['tab'] ) ) {
			switch ( strtolower( $_GET['tab'] ) ) {
				case 'manual_jpgs':
			    case 'explode_zips':
				case 'make_pdfs':
                case 'kill_first_steps':
					$tab = strtolower( $_GET['tab'] );
					break;
			}
		}

		$tab_class = array();
        $tab_class['default'] = ( '' === $tab ) ? 'nav-tab-active' : '';
        $tab_class['manual_jpgs'] = ( 'manual_jpgs' === $tab ) ? 'nav-tab-active' : '';
        $tab_class['explode_zips'] = ( 'explode_zips' === $tab ) ? 'nav-tab-active' : '';
        $tab_class['make_pdfs'] = ( 'make_pdfs' === $tab ) ? 'nav-tab-active' : '';
        $tab_class['kill_first_steps'] = ( 'kill_first_steps' === $tab ) ? 'nav-tab-active' : '';
		?>

        <nav class="nav-tab-wrapper">
            <a href="?page=magascrape_admin" class="nav-tab <?php echo $tab_class['default']; ?>">Download Zips</a>
            <a href="?page=magascrape_admin&tab=manual_jpgs" class="nav-tab <?php echo $tab_class['manual_jpgs']; ?>">Manually Get JPGs</a>
            <a href="?page=magascrape_admin&tab=explode_zips" class="nav-tab <?php echo $tab_class['explode_zips']; ?>">Explode Zips</a>
            <a href="?page=magascrape_admin&tab=make_pdfs" class="nav-tab <?php echo $tab_class['make_pdfs']; ?>">Make PDFs</a>
            <?php if ( '' != $tab_class['kill_first_steps'] ) { ?>
                <a href="?page=magascrape_admin&tab=kill_first_steps" class="nav-tab <?php echo $tab_class['kill_first_steps']; ?>">Final Step</a>
            <?php } ?>
        </nav>

        <div class="tab_download_zips" style="display:<?php if ( '' === $tab ) {
			echo 'block';
		} else {
			echo 'none';
		} ?>">
            <form method="post">
				<?php wp_nonce_field( 'get_zips', 'get_zips_nonce' ); ?>
                <input type="hidden" name="ms_action" value="start_scrape"/>
                <div style="padding-top:20px;">
                    <label>
                        <input type="text" name="folder_name" style="width:50em;"
                               placeholder="Folder to Save To (eg: `The_Promised_Neverland`)"
                               value="<?php echo $folder_name; ?>"
                               required="required" aria-required="true">
                    </label>
                </div>
                <div style="padding-top:20px;">
                    <label>
<textarea name="code_to_scrape" placeholder="Copy/paste the `manga_series_list` element here"
          style="width:50em;height:20em;" required="required" aria-required="true"></textarea>
                    </label>
                </div>
                <div style="padding-top:20px;">
                    <input type="submit" name="submit" value="Parse HTML and Download"/>
                </div>
            </form>
        </div>

        <div class="tab_manual_jpgs" style="display:<?php if ( 'manual_jpgs' === $tab ) {
			echo 'block';
		} else {
			echo 'none';
		} ?>">
            <form method="post">
				<?php wp_nonce_field( 'manual_jpgs', 'manual_jpgs_nonce' ); ?>
                <input type="hidden" name="ms_action" value="start_manual_jpgs"/>
                <div style="padding-top:20px;">
                    <label>
                        <input type="text" name="folder_name" style="width:50em;"
                               placeholder="Folder the jpgs will save to, INCLUDING CHAPTER (eg: `Alice_In_Borderland/Imawa_No_Kuni_No_Alice_22`)"
                               required="required"
                               value="<?php echo $folder_name; ?>"
                               aria-required="true">
                    </label>
                </div>
                <div style="padding-top:20px;">
                    <label>
<textarea name="code_to_scrape" placeholder="Copy/paste the `slideshow_container` element here"
       style="width:50em;height:20em;" required="required" aria-required="true"></textarea>
                    </label>
                </div>
                <div style="padding-top:20px;">
                    <input type="submit" name="submit" value="Manually get JPGs"/>
                </div>
            </form>
        </div>

        <div class="tab_explode_zips" style="display:<?php if ( 'explode_zips' === $tab ) {
			echo 'block';
		} else {
			echo 'none';
		} ?>">
            <form method="post">
				<?php wp_nonce_field( 'explode_zips', 'explode_zips_nonce' ); ?>
                <input type="hidden" name="ms_action" value="start_explode_zips"/>
                <div style="padding-top:20px;">
                    <label>
                        <input type="text" name="folder_name" style="width:50em;"
                               placeholder="Folder the zips are in (eg: `The_Promised_Neverland`)"
                               required="required"
                               value="<?php echo $folder_name; ?>"
                               aria-required="true">
                    </label>
                </div>
                <div style="padding-top:20px;">
                    <input type="submit" name="submit" value="Explode!"/>
                </div>
            </form>
        </div>

        <div class="tab_make_pdfs" style="display:<?php if ( 'make_pdfs' === $tab ) {
			echo 'block';
		} else {
			echo 'none';
		} ?>">
            <form method="post">
				<?php wp_nonce_field( 'make_pdfs', 'make_pdfs_nonce' ); ?>
                <input type="hidden" name="ms_action" value="start_make_pdfs"/>
                <div style="padding-top:20px;">
                    <label>
                        <input type="text" name="folder_name" style="width:50em;"
                               placeholder="Folder the jpgs are in (eg: `The_Promised_Neverland`)"
                               required="required"
                               value="<?php echo $folder_name; ?>"
                               aria-required="true">
                    </label>
                </div>
                <div style="padding-top:20px;">
                    <input type="submit" name="submit" value="Make PDF files"/>
                </div>
            </form>
        </div>

        <div class="tab_kill_first_steps" style="display:<?php if ( 'kill_first_steps' === $tab ) {
			echo 'block';
		} else {
			echo 'none';
		} ?>">
            <p>Nothing to show here, chief. You just finished, congratulations!</p>
        </div>

		<?php

	}

}