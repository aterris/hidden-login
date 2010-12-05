/* Hidden Login Javascript */
/* Andrew Terris */

//Setup Hidden Login
function hidden_login(first_command,second_command,key,logged_in,admin_url)
{
	
	jQuery(document).keydown(function(e) 
	{
		//Check Command 1	
		var command1 = false;
		switch(first_command)
		{
			case 'ctrl':
				if(e.ctrlKey)
				command1=true;
				break;
			case 'shift':
				if(e.shiftKey)
				command1=true;
				break;
			case 'alt':
				if(e.altKey)
				command1=true;
				break;
			default:
				break;
		}
		
		//Check Command 2
		var command2 = false;
		switch(second_command)
		{
			case 'ctrl':
				if(e.ctrlKey)
				command2=true;
				break;
			case 'shift':
				if(e.shiftKey)
				command2=true;
				break;
			case 'alt':
				if(e.altKey)
				command2=true;
				break;
			case '':
				command2=true;
				break;
			default:
				break;
		}
		
		//Check Key Press
		if(e.keyCode == (key.toUpperCase()).charCodeAt(0) && command1 && command2) 
		{
			if(logged_in)
			{
				//Redirect To Admin
				window.location.href = $admin_url;
			}
			else
			{
				//Toggle Hidden Login Window
				hidden_login_toggle();
			}
           	return false;
       	}
    });
	
	//Toggle Hidden Login Window
	function hidden_login_toggle()
	{    
       	//If Open, Close Login window
		if(jQuery('#hidden-login-window').is(":visible"))
		{
			jQuery('#hidden-login-window').fadeOut(200);
			jQuery('#hidden-login-mask').fadeOut(200);   
		}
		else //If Closed, Open Login Window
		{		
			//Use Document Width if Greater Than Window Width
			if(jQuery(window).width()<jQuery(document).width())
				$maskWidth = jQuery(document).width();
			else
				$maskWidth = jQuery(window).width();
				
			//Set Mask Size
			jQuery('#hidden-login-mask').css({'width':$maskWidth,'height':jQuery(document).height()}); 
			  
			//Show Mask     
			jQuery('#hidden-login-mask').fadeTo(200,0.8);         
		  
			//Position Div
			jQuery('#hidden-login-window').css('top', jQuery(window).height()/2-jQuery('#hidden-login-window').height()/2);
			jQuery('#hidden-login-window').css('left', jQuery(window).width()/2-jQuery('#hidden-login-window').width()/2); 
		  
			//Show Div  
			jQuery('#hidden-login-window').fadeIn(200);
			
			//Set Focus
			jQuery('#user_login').focus();
		}
	}
      
   	//Click on Mask - Close Window 
   	jQuery('#hidden-login-mask').click(function () 
	{  
       	hidden_login_toggle();
   	});  
	
	//Handle Window Resizing
	jQuery(window).resize(function () 
	{
		//Use Document Width if Greater Than Window Width (Set Mask to 0px width to get actual document width)
		jQuery('#hidden-login-mask').css({'width':'0px'});
		if(jQuery(window).width()<jQuery(document).width())
				$maskWidth = jQuery(document).width();
		else
				$maskWidth = jQuery(window).width();
				
		//Set Mask Size
		jQuery('#hidden-login-mask').css({'width':$maskWidth,'height':jQuery(document).height()});

		//Position Div
		jQuery('#hidden-login-window').css('top', jQuery(window).height()/2-jQuery('#hidden-login-window').height()/2);
		jQuery('#hidden-login-window').css('left', jQuery(window).width()/2-jQuery('#hidden-login-window').width()/2);
	});   
}

