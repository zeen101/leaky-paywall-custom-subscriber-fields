<?php
/**
 * Registers zeen101's Leaky Paywall class
 *
 * @package zeen101's Leaky Paywall
 * @since 1.0.0
 */

/**
 * This class registers the main issuem functionality
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Leaky_Paywall_Subscriber_Meta' ) ) {
	
	class Leaky_Paywall_Subscriber_Meta {
		
		/**
		 * Class constructor, puts things in motion
		 *
		 * @since 1.0.0
		 */
		function __construct() {
					
			$settings = $this->get_settings();
			
			add_action( 'admin_init', array( $this, 'upgrade' ) );
			add_action( 'admin_notices', array( $this, 'update_notices' ) );
					
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_wp_enqueue_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'admin_wp_print_styles' ) );
			
			add_action( 'leaky_paywall_settings_form', array( $this, 'settings_div' ) );
			add_action( 'leaky_paywall_update_settings', array( $this, 'update_settings_div' ) );
			
			add_filter( 'leaky_paywall_subscribers_columns', array( $this, 'leaky_paywall_subscribers_columns' ) );
			//add_filter( 'leaky_paywall_subscribers_sortable_columns', array( $this, 'leaky_paywall_subscribers_sortable_columns' ) );
			add_filter( 'manage_leaky_paywall_subscribers_custom_column', array( $this, 'manage_leaky_paywall_subscribers_custom_column' ), 10, 3 );
			
			add_action( 'update_leaky_paywall_subscriber_form', array( $this, 'update_leaky_paywall_subscriber_form' ) );
			add_action( 'update_leaky_paywall_subscriber', array( $this, 'update_leaky_paywall_subscriber' ) );
			add_action( 'add_leaky_paywall_subscriber_form', array( $this, 'add_leaky_paywall_subscriber_form' ) );
			add_action( 'add_leaky_paywall_subscriber', array( $this, 'add_leaky_paywall_subscriber' ) );
			add_action( 'bulk_add_leaky_paywall_subscriber', array( $this, 'bulk_add_leaky_paywall_subscriber' ), 10, 3 );
			
			//add_filter( 'leaky_paywall_subscriber_query_join', array( $this, 'leaky_paywall_subscriber_query_join' ) );
			//add_filter( 'issuem_leaky_paywall_search_susbcriber_where_array', array( $this, 'issuem_leaky_paywall_search_susbcriber_where_array' ), 10, 3 );
			add_filter( 'leaky_paywall_bulk_add_headings', array( $this, 'leaky_paywall_bulk_add_headings' ) );
			
		}
		
		/**
		 * Prints backend styles
		 *
		 * @since 1.0.0
		 */
		function admin_wp_print_styles() {
		
			global $hook_suffix;
			
			if ( 'leaky-paywall_page_leaky-paywall-subscribers' === $hook_suffix
				|| 'toplevel_page_issuem-leaky-paywall' === $hook_suffix )
				wp_enqueue_style( 'leaky_paywall_sm_settings_style', LP_SM_URL . 'css/issuem-leaky-paywall-settings.css', '', LP_SM_VERSION );
			
		}
	
		/**
		 * Enqueues backend styles
		 *
		 * @since 1.0.0
		 */
		function admin_wp_enqueue_scripts( $hook_suffix ) {
			
			if ( 'leaky-paywall_page_leaky-paywall-subscribers' === $hook_suffix
				|| 'toplevel_page_issuem-leaky-paywall' === $hook_suffix )
				wp_enqueue_script( 'leaky_paywall_sm_settings_js', LP_SM_URL . 'js/issuem-leaky-paywall-settings.js', array( 'jquery' ), LP_SM_VERSION );
				
			
		}
		
		/**
		 * Initialize pigeonpack Admin Menu
		 *
		 * @since 1.0.0
		 * @uses add_menu_page() Creates Pigeon Pack menu
		 * @uses add_submenu_page() Creates Settings submenu to Pigeon Pack menu
		 * @uses add_submenu_page() Creates Help submenu to Pigeon Pack menu
		 * @uses do_action() To call 'pigeonpack_admin_menu' for future addons
		 */
		function admin_menu() {
														
			add_submenu_page( false, __( 'Update', 'issuem-lp-sm' ), __( 'Update', 'issuem-lp-sm' ), apply_filters( 'manage_leaky_paywall_settings', 'manage_options' ), 'leaky-paywall-subscriber-meta-update', array( $this, 'update_page' ) );
			
		}
		
		/**
		 * Get zeen101's Leaky Paywall - Subscriber Meta options
		 *
		 * @since 1.0.0
		 */
		function get_settings() {
			
			$defaults = array( 
				'meta_keys' => array(),
			);
		
			$defaults = apply_filters( 'leaky_paywall_subscriber_meta_default_settings', $defaults );
			
			$settings = get_option( 'issuem-leaky-paywall-subscriber-meta' );
												
			return wp_parse_args( $settings, $defaults );
			
		}
		
		/**
		 * Update zeen101's Leaky Paywall options
		 *
		 * @since 1.0.0
		 */
		function update_settings( $settings ) {
			
			update_option( 'issuem-leaky-paywall-subscriber-meta', $settings );
			
		}
		
		/**
		 * Create and Display settings page
		 *
		 * @since 1.0.0
		 */
		function settings_div() {
			
			// Get the user options
			$settings = $this->get_settings();
			
			// Display HTML form for the options below
			?>
            <div id="modules" class="postbox">
            
                <div class="handlediv" title="Click to toggle"><br /></div>
                
                <h3 class="hndle"><span><?php _e( 'Custom Subscriber Meta Fields', 'issuem-lp-sm' ); ?></span></h3>
                
                <div class="inside">
                
                <table id="issuem_leaky_paywall_subscriber_meta_wrapper">
                
                    <tr>
	                    <td><?php _e( 'Meta Key Name', 'issuem-lp-sm' ); ?></td>
						<td><?php _e( 'Show on Subscribers Page?', 'issuem-lp-sm' ); ?></td>
						<td><?php _e( 'Delete?', 'issuem-lp-sm' ); ?></td>
                    </tr>
                    
                    <?php
                    $count = 0;
                    if ( !empty( $settings['meta_keys'] ) ) {
	                    foreach ( $settings['meta_keys'] as $meta_key ) {
	                    $name = !empty( $meta_key['name'] ) ? $meta_key['name'] : '';
	                    $checked = !empty( $meta_key['checked'] ) ? $meta_key['checked'] : 'off';
	                    ?>
	                    <tr>
			                <td><input type="text" name="meta_keys[<?php echo $count; ?>][name]" value="<?php echo $name; ?>" /></td>
			                <td><input type="checkbox" name="meta_keys[<?php echo $count; ?>][checked]" value="on" <?php checked( 'on', $checked ); ?> /></td>
			                <td><a href="#" class="delete_lp_meta_key">x</a></td>
	                    </tr>
	                    <?php
	                    $count++;
	                    }
                    }
                    ?>
                    
                </table>
                
				<script type="text/javascript" charset="utf-8">
				var subscriber_meta_key_count = <?php echo $count; ?>;
				</script>
                                                           
                <p class="submit">
                    <input class="button button-small add_lp_meta_key" type="submit" name="add_lp_meta_key" value="<?php _e( 'Add Meta Key', 'issuem-lp-sm' ) ?>" />
                </p>
                                                              
                <p class="submit">
                    <input class="button-primary" type="submit" name="update_leaky_paywall_settings" value="<?php _e( 'Save Settings', 'issuem-lp-sm' ) ?>" />
                </p>

                </div>
                
            </div>
			<?php
			
		}
		
		function update_settings_div() {
		
			$settings = $this->get_settings();
				
			if ( !empty( $_REQUEST['meta_keys'] ) )
				$settings['meta_keys'] = $_REQUEST['meta_keys'];
			else
				$settings['meta_keys'] = '';
			
			$this->update_settings( $settings );
			
		}
		
		function leaky_paywall_subscribers_columns( $columns ) {
		
			$settings = $this->get_settings();
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['checked'] ) && 'on' === $meta_key['checked'] ) {
                		$label = $meta_key['name'];
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
                		$columns[$meta_key] = $label;
            		}
                }
            }
            
            return $columns;
		}
		
		function leaky_paywall_subscribers_sortable_columns( $columns ) {
		
			$settings = $this->get_settings();
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['checked'] ) && 'on' === $meta_key['checked'] ) {
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
                		$columns[$meta_key] = array( 'lpsm.meta_key', false );
            		}
                }
            }
            
            return $columns;
		}
		
		function manage_leaky_paywall_subscribers_custom_column( $output, $column, $hash ) {
			global $is_leaky_paywall, $which_leaky_paywall;
			$lp_settings = get_leaky_paywall_settings();
			$mode = 'off' === $lp_settings['test_mode'] ? 'live' : 'test';
			
        	$subscriber = get_leaky_paywall_subscriber_by_hash( $hash, $mode );
			if ( !empty( $subscriber ) ) 
				return get_user_meta( $subscriber->ID, $which_leaky_paywall . '_leaky_paywall_' . $mode . '_subscriber_meta_' . $column, true );
			else
				return '';
			
		}
		
		function update_leaky_paywall_subscriber_form( $subscriber_id ) {
			global $is_leaky_paywall, $which_leaky_paywall;
			$settings = $this->get_settings();
        	
			$lp_settings = get_leaky_paywall_settings();
			$mode = 'off' === $lp_settings['test_mode'] ? 'live' : 'test';
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['checked'] ) && 'on' === $meta_key['checked'] ) {
                		$label = $meta_key['name'];
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
						           	
						$meta_value = get_user_meta( $subscriber_id, $which_leaky_paywall . '_leaky_paywall_' . $mode . '_subscriber_meta_' . $meta_key, true );
						?>
                    	<p>
                        <label for="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key" style="display:table-cell"><?php echo $label; ?></label>
                        <input id="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key" class="subscriber-meta-key subscriber-<?php echo $meta_key; ?>-meta-key" type="text" value="<?php echo $meta_value; ?>" name="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key"  />
                        </p>
						<?php
                	}
                }
            }
		}
		
		function update_leaky_paywall_subscriber( $subscriber_id ) {
			global $is_leaky_paywall, $which_leaky_paywall;
			$settings = $this->get_settings();
        	
			$lp_settings = get_leaky_paywall_settings();
			$mode = 'off' === $lp_settings['test_mode'] ? 'live' : 'test';

            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['name'] ) ) {
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
	                	if ( !empty( $_REQUEST['leaky-paywall-subscriber-' . $meta_key . '-meta-key'] ) ) {
	                	
							update_user_meta( $subscriber_id, $which_leaky_paywall . '_leaky_paywall_' . $mode . '_subscriber_meta_' . $meta_key, $_REQUEST['leaky-paywall-subscriber-' . $meta_key . '-meta-key'] );
	                	}
                	}
                }
            }
		}
		
		function add_leaky_paywall_subscriber_form() {
			$settings = $this->get_settings();
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['checked'] ) && 'on' === $meta_key['checked'] ) {
                		$label = $meta_key['name'];
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
	                ?>
                    	<p>
                        <label for="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key" style="display:table-cell"><?php echo $label; ?></label>
                        <input id="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key" class="subscriber-meta-key subscriber-<?php echo $meta_key; ?>-meta-key" type="text" value="" name="leaky-paywall-subscriber-<?php echo $meta_key; ?>-meta-key"  />
                        </p>
	                <?php
                	}
                }
            }
		}
		
		function add_leaky_paywall_subscriber( $subscriber_id ) {
			global $is_leaky_paywall, $which_leaky_paywall;
			$settings = $this->get_settings();
        	
			$lp_settings = get_leaky_paywall_settings();
			$mode = 'off' === $lp_settings['test_mode'] ? 'live' : 'test';
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['name'] ) ) {
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );			
	                	if ( !empty( $_REQUEST['leaky-paywall-subscriber-' . $meta_key . '-meta-key'] ) ) {
							update_user_meta( $subscriber_id, $which_leaky_paywall . '_leaky_paywall_' . $mode . '_subscriber_meta_' . $meta_key, $_REQUEST['leaky-paywall-subscriber-' . $meta_key . '-meta-key'] );
	                	}
                	}
                }
            }
		}
		
		function bulk_add_leaky_paywall_subscriber( $subscriber_id, $keys, $import ) {
			global $is_leaky_paywall, $which_leaky_paywall;
			$settings = $this->get_settings();
			
			$lp_settings = get_leaky_paywall_settings();
			$mode = 'off' === $lp_settings['test_mode'] ? 'live' : 'test';
						
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['name'] ) ) {
	                	$meta_key = sanitize_title_with_dashes( $meta_key['name'] );
	                	if ( array_key_exists( $meta_key, $keys ) ) {
							update_user_meta( $subscriber_id, $which_leaky_paywall . '_leaky_paywall_' . $mode . '_subscriber_meta_' . $meta_key, trim( $import[$keys[$meta_key]] ) );
	                	}
                	}
                }
            }
		}
		
		function leaky_paywall_subscriber_query_join( $join ) {
			global $wpdb;
			return $join . ' LEFT JOIN ' . $wpdb->prefix . 'issuem_leaky_paywall_subscriber_meta AS lpsm ON lpsm.hash = lps.hash ';
		}
		
		function issuem_leaky_paywall_search_susbcriber_where_array( $where_array, $search_type, $search  ) {
			$where_array[] .= sprintf( "lpsm.`meta_value` %s '%s'", $search_type, $search );
            return $where_array;
		}
		
		function leaky_paywall_bulk_add_headings( $headings ) {
			$settings = $this->get_settings();
			
            if ( !empty( $settings['meta_keys'] ) ) {
                foreach ( $settings['meta_keys'] as $meta_key ) {
                	if ( !empty( $meta_key['name'] ) )
	                	$headings[] = sanitize_title_with_dashes( $meta_key['name'] );
                }
            }
            
            return $headings;
		}
		
		function update_page() {
			// Display HTML form for the options below
			?>
			<div class=wrap>
            <div style="width:70%;" class="postbox-container">
            <div class="metabox-holder">	
            <div class="meta-box-sortables ui-sortable">
            
                <form id="issuem" method="post" action="">
            
                    <h2 style='margin-bottom: 10px;' ><?php _e( "Leaky Paywall - Subscriber Meta Updater", 'issuem-lp-sm' ); ?></h2>
                    
					<?php
					
					$manual_update_version = get_option( 'leaky_paywall_subscriber_meta_manual_update_version' );
					$manual_update_version = '1.2.0'; //CHANGEME
										
					if ( version_compare( $manual_update_version, '2.0.0', '<' ) )
						$this->update_2_0_0();
									
					?>
                                        
					<?php wp_nonce_field( 'issuem_leaky_paywall_subscriber_meta_update', 'issuem_leaky_paywall_subscriber_meta_update_nonce' ); ?>
                    
                </form>
                
            </div>
            </div>
            </div>
			</div>
			<?php
		}
		
		/**
		 * Upgrade function, tests for upgrade version changes and performs necessary actions
		 *
		 * @since 1.0.0
		 */
		function upgrade() {
			
			$settings = $this->get_settings();
			
			if ( isset( $settings['version'] ) )
				$old_version = $settings['version'];
			else
				$old_version = 0;
				
			/* Table Version Changes */
			if ( isset( $settings['db_version'] ) )
				$old_db_version = $settings['db_version'];
			else
				$old_db_version = 0;
			

			$settings['version'] = LP_SM_VERSION;
			$settings['db_version'] = LP_SM_DB_VERSION;
			
			$this->update_settings( $settings );
			
		}
				
		function update_2_0_0() {
			global $wpdb, $is_leaky_paywall, $which_leaky_paywall;
			echo '<h3>' . __( 'Version 2.0.0 Update Process', 'issuem-lp-sm' ) . '</h1>';
			echo '<p>' . __( 'We have decided to use the WordPress Users table to instead of maintaining our own subscribers table. This process will copy all existing leaky paywall subscriber meta data to individual WordPress users meta.', 'issuem-lp-sm' ) . '</p>';
			
            $n = ( isset($_GET['n']) ) ? intval($_GET['n']) : 0;

			$sql = "SELECT lps.* FROM " . $wpdb->prefix . "issuem_leaky_paywall_subscriber_meta as lps LIMIT " . $n . ", 5";

            $subscriber_meta = $wpdb->get_results( $sql );
            
            echo "<ul>";
            foreach ( (array) $subscriber_meta as $meta ) {
            
            	foreach ( array( 'live', 'test' ) as $mode ) {
		            	
	            	$subscriber = get_leaky_paywall_subscriber_by_hash( $meta->hash, $mode );
	            	
	            	if ( !empty( $subscriber ) ) {
	            	
		                echo '<li>' . sprintf( __( 'Copying user meta data for %s (%s mode user)...', 'issuem-lp-sm' ), $subscriber->data->user_email, $mode );
						update_user_meta( $subscriber->ID, $which_leaky_paywall . '_leaky_paywall_' . $mode . '_subscriber_meta_' . $meta->meta_key, $meta->meta_value );
		                echo __( 'completed.', 'issuem-leaky-paywall' ) . '</li>';
		            	
	            	} else {
	            	
		                echo '<li>' . sprintf( __( 'No valid subscriber found with hash %s in %s mode.', 'issuem-lp-sm' ), $meta->hash, $mode ) . '</li>';
		                
		            }
	            	
            	}
                
            }
            echo "</ul>";
            
            if ( empty( $subscriber_meta ) || 5 > count( $subscriber_meta ) ) {
            
                echo '<p>' . __( 'Finished Migrating Subscriber Meta!', 'issuem-lp-sm' ) . '</p>';
                echo '<p>' . __( 'Updating Settings...', 'issuem-lp-sm' ) . '</p>';
                
                $settings = $this->get_settings();
                
                echo '<p>' . __( 'All Done!', 'issuem-lp-sm' ) . '</p>';
				update_option( 'leaky_paywall_subscriber_meta_manual_update_version', '2.0.0' );
                return;
                
            } else {
	            
	            ?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="admin.php?page=leaky-paywall-update&amp;n=<?php echo ($n + 5) ?>"><?php _e( 'Next Subscriber Meta set', 'issuem-lp-sm' ); ?></a></p>
	            <script type='text/javascript'>
	            <!--
	            function nextpage() {
	                location.href = "admin.php?page=leaky-paywall-subscriber-meta-update&n=<?php echo ($n + 5) ?>";
	            }
	            setTimeout( "nextpage()", 250 );
	            //-->
	            </script><?php
	            
            }

		}
		
		function update_notices() {
		
			global $hook_suffix;
			
			$manual_update_version = get_option( 'leaky_paywall_manual_update_version' );
						
			if ( version_compare( $manual_update_version, '2.0.0', '<' ) ) {
			
				?>
				<div id="leaky-paywall-2-0-0-update-nag" class="update-nag">
					<?php _e( 'You cannot use the Subscriber Meta plugin until you update Leaky Paywall Database to version 2.' ); ?>
				</div>
				<?php
				
			} else {
				
				$settings = $this->get_settings();

				if ( isset( $settings['version'] ) )
					$old_version = $settings['version'];
				else
					$old_version = 0;
					
				if ( !empty( $old_version ) ) { //new installs shouldn't see this notice
					if ( current_user_can( 'manage_options' ) ) {
						if ( 'admin_page_leaky-paywall-update' !== $hook_suffix && 'leaky-paywall_page_leaky-paywall-update' !== $hook_suffix ) {
											
							$manual_update_version = get_option( 'leaky_paywall_subscriber_meta_manual_update_version' );
												
							if ( version_compare( $manual_update_version, '2.0.0', '<' ) ) {
								?>
								<div id="leaky-paywall-subscriber-meta-2-0-0-update-nag" class="update-nag">
									<?php
									$update_link    = add_query_arg( array( 'page' => 'leaky-paywall-subscriber-meta-update' ), admin_url( 'admin.php' ) );
									printf( __( 'You must update the Leaky Paywall Subscriber Meta Database to version 2 to continue using this plugin... %s', 'issuem-leaky-paywall' ), '<a class="btn" href="' . esc_url( $update_link ) . '">' . __( 'Update Now', 'issuem-leaky-paywall' ) . '</a>' );
									?>
								</div>
								<?php
							}
						}
					}
				}
			
			}

		}
		
	}
	
}
