var $leaky_paywall_sm_settings = jQuery.noConflict();

$leaky_paywall_sm_settings(document).ready(function($) {
	$( '.add_lp_meta_key' ).on( 'click', function( e ) {
		e.preventDefault();
        var new_row = '<tr>';
        new_row += '<td><input type="text" name="meta_keys['+subscriber_meta_key_count+'][name]" value="" /></td>';
        new_row += '<td><input type="checkbox" name="meta_keys['+subscriber_meta_key_count+'][checked]" /></td>';
        new_row += '<td><a href="#" class="delete_lp_meta_key">x</a></td>';
        new_row += '</tr>';
        subscriber_meta_key_count++;
        $( 'table#issuem_leaky_paywall_subscriber_meta_wrapper' ).append( new_row );
        
	});
	$( '.delete_lp_meta_key' ).live( 'click', function( e ) {
		e.preventDefault();
		$( this ).closest( 'tr' ).remove();
	});
	
});