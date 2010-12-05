<?php
/*
Plugin Name: Hidden Login
Plugin URI: http://www.andrewterris.com/projects/hidden-login/
Description: Hidden Login provides easy and discrete access to the admin functionality of your site.  Pressing the set keystroke combination will activate Hidden Login, either displaying a modal login window or redirected to the admin area if you are already logged in.  Visit the Hidden Login Options to set what pages are enabled (All Pages or Homepage Only) and set your desired activation keystroke combination.
Version: 1.0
Author: Andrew Terris
Author URI: http://www.andrewterris.com
*/


//**** FRONT END ****//
//** Add Hidden Login **//
add_action('get_header','hidden_login_frontend');
function hidden_login_frontend()
{
	if(is_front_page() || get_option('hidden_login_active_pages')==='all')
	{
		add_action('wp_print_styles','hidden_login_css');
		add_action('wp_print_scripts','hidden_login_js_file');
		add_action('wp_footer','hidden_login_js_call');
		add_action('wp_footer','hidden_login_html');
	}
}

//** Insert CSS **//
function hidden_login_css()
{
	wp_enqueue_style( 'hidden_login', WP_PLUGIN_URL . '/hidden-login/hidden-login.css');
}

//** Insert Javascript File **//
function hidden_login_js_file()
{
	wp_enqueue_script('hidden_login', WP_PLUGIN_URL . '/hidden-login/hidden-login.js', array('jquery'), '1.0', true);
}

//** Insert Javascript Call **//
function hidden_login_js_call()
{
	//Check If Currently Logged In
	$logged_in = ( is_user_logged_in() ) ? 'true' : 'false' ; 
	
	//Call Hidden Login JS
	?>
    <!-- Hidden Login Javascript -->
    <script>
	jQuery(document).ready(function() 
	{
		hidden_login('<?php echo get_option('hidden_login_command1'); ?>','<?php echo get_option('hidden_login_command2'); ?>','<?php echo get_option('hidden_login_keystroke'); ?>',<?php echo $logged_in ?>,'<?php echo get_admin_url ?>');
	});  
	</script>
    
    <?php
}

//** Insert HTML **//
function hidden_login_html()
{
	?>
    <!-- Hidden Login Window -->
	<div id="hidden-login-window">
	    <form name="loginform" id="loginform" action="<?php echo site_url('wp-login.php', 'login_post') ?>" method="post">
	<p>
		<label><?php _e('Username') ?><br />
		<input type="text" name="log" id="user_login" class="input" value="<?php echo esc_attr($user_login); ?>" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label><?php _e('Password') ?><br />
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
	</p>
	<?php do_action('login_form'); ?>

	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Log In'); ?>" tabindex="100" />
<?php	if ( $interim_login ) { ?>
		<input type="hidden" name="interim-login" value="1" />
<?php	} else { ?>
		<input type="hidden" name="redirect_to" value="<?php bloginfo('wpurl'); ?>/wp-admin/" />
<?php 	} ?>
		<input type="hidden" name="testcookie" value="1" />
	</p>
</form>

	</div><!-- End Hidden Login Window -->
        
	<!-- Hidden Login Mask -->	
 	<div id="hidden-login-mask"></div>
	<?php
}



//**** BACKEND ****//
//** Setup Inital Settings **//
register_activation_hook(__FILE__,'hidden_login_init');
function hidden_login_init()
{
	add_option('hidden_login_active_pages','all');
	add_option('hidden_login_command1','ctrl');
	add_option('hidden_login_command2','shift');
	add_option('hidden_login_keystroke','z');
	
}

//** Setup Options Panel **//
add_action('admin_menu', 'hidden_login_options_page_setup');
function hidden_login_options_page_setup()
{
	add_options_page("Hidden Login Options", "Hidden Login", 1, "hidden_login", "hidden_login_options_page_create");
} 

//** Create Options **//
add_action('admin_init', 'hidden_login_options_create' );
function hidden_login_options_create()
{
	register_setting('hidden_login_options','hidden_login_active_pages');
	register_setting('hidden_login_options','hidden_login_command1');
	register_setting('hidden_login_options','hidden_login_command2');
	register_setting('hidden_login_options','hidden_login_keystroke','hidden_login_validate_keystroke');	
}

//** Create Options Panel **//
function hidden_login_options_page_create()
{
	?>
	<div class="wrap">
		<h2>Hidden Login Options</h2>
		
		<form method="post" action="options.php">
        <?php settings_fields( 'hidden_login_options' ); ?>
		
		<table class="form-table">
			
            <tr valign="top"> 
			<th scope="row"><label for="hidden_login_active_pages">Active On</label></th> 
			<td> 
				<select name='hidden_login_active_pages' id='hidden_login_active_pages' class='postform' > 
				<?php
				$activepagetype = array('all' => array('all','All Pages'), 'home' => array('home','Homepage Only'));
				$activepage = get_option('hidden_login_active_pages');
			
				foreach($activepagetype as $pagetype)
				{
					if($pagetype[0] === $activepage)
					{
						echo '<option value="' . $pagetype[0] . '" selected="selected">' . $pagetype[1] . '</option>';
					}
					else
					{
						echo '<option value="' . $pagetype[0] . '">' . $pagetype[1] . '</option>';
					}
				}
				?>
				</select> 
			</td> 
			</tr> 
            
            
			
            <tr valign="top"> 
			<th scope="row"><label for="hidden_login_keystroke">Activation Keystroke</label></th> 
			<td> 
				<select name='hidden_login_command1' id='hidden_login_command1' class='postform' > 
				<?php
				$commandtypes = array('ctrl' => array('ctrl','Ctrl'), 'shift' => array('shift','Shift'), 'alt' => array('alt','Alt'));
				$command = get_option('hidden_login_command1');
			
				foreach($commandtypes as $commandtype)
				{
					if($commandtype[0] === $command)
					{
						echo '<option value="' . $commandtype[0] . '" selected="selected">' . $commandtype[1] . '</option>';
					}
					else
					{
						echo '<option value="' . $commandtype[0] . '">' . $commandtype[1] . '</option>';
					}
				}
				?>
				</select> 
                +
                <select name='hidden_login_command2' id='hidden_login_command2' class='postform' > 
				<?php
				$commandtypes = array('' => array('',''),'ctrl' => array('ctrl','Ctrl'), 'shift' => array('shift','Shift'), 'alt' => array('alt','Alt'));
				$command = get_option('hidden_login_command2');
			
				foreach($commandtypes as $commandtype)
				{
					if($commandtype[0] === $command)
					{
						echo '<option value="' . $commandtype[0] . '" selected="selected">' . $commandtype[1] . '</option>';
					}
					else
					{
						echo '<option value="' . $commandtype[0] . '">' . $commandtype[1] . '</option>';
					}
				}
				?>
				</select> 
                +
                <input type="text" style="width: 5%;" name="hidden_login_keystroke" value="<?php echo get_option('hidden_login_keystroke'); ?>" /> <span style="font-size:9px;"> (A-Z , 0-9)</span>
			</td> 
			</tr>
		</table>
		
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
		</form>
	</div>
	
	<?php
}

//** Validate Options **//
function hidden_login_validate_keystroke($input)
{
	$input['hidden_login_keystroke'] = ( ctype_alnum($input['hidden_login_keystroke']) ) ? $input['hidden_login_keystroke'] : ' ';
	
	return $input;
}
?>
