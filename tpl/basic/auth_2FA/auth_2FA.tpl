<link rel="stylesheet" type="text/css" href="{$template_path}css/auth.css" />
<link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700&subset=cyrillic,latin' rel=stylesheet type='text/css'>
<link rel="stylesheet" href="{$template_path}font/css/font-awesome.min.css">
<title>{$lang.auth_title}</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<div class="wrapper">
    <div class="container">
        
		
		<div class="form-group">
           
			<img class="logo" src="/tpl/basic/img/auth/logo.png" alt="Brainy">
			
            <form class="auth-form" action="auth_2FA.php" method="post">
				
				 {if $failedlog==1}
            <div class="auth-err"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
 {$lang.common_wrongname_pass}</div>
            {/if}
				
				<div>
					<i class="fa fa-lock" aria-hidden="true"></i><input type="text" name="otp" placeholder="2FA код" />
				</div>
                <input type="hidden" name="login_panel" value="{$login_panel}">
                <input type="hidden" name="password_panel" value="{$password_panel}">
                <input type="hidden" name="lan_panel" value="{$lan_panel}">
 				<input type="submit" id="log2brainy" value="Войти" />
			</form>
        </div>

    </div>
</div>


{literal}
<script>
	$('.auth-form input').keydown(function(){$('.auth-err').fadeOut();})
</script>
{/literal}
