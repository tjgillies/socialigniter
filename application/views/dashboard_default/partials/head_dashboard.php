<link rel="alternate" type="application/rss+xml" title="<?= $site_title ?> RSS 2.0 Feed" href="<?= base_url() ?>feed" />

<link type="text/css" href="<?= base_url() ?>css/common.css" rel="stylesheet" media="screen" />
<link type="text/css" href="<?= $dashboard_assets ?>style.css" rel="stylesheet" media="screen" />
<link type="text/css" href="<?= base_url() ?>css/uniform.default.css" rel="stylesheet" media="screen" charset="utf-8" />
<link type="text/css" href="<?= base_url() ?>css/jplayer.css" rel="stylesheet" media="screen" charset="utf-8" />
<link type="text/css" href="<?= base_url() ?>css/fancybox.css" rel="stylesheet" media="screen" charset="utf-8" />

<script type="text/javascript" src="<?= base_url() ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>js/jquery.NobleCount.min.js"></script>  
<script type="text/javascript" src="<?= base_url() ?>js/jquery.uniform.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>js/jquery.timers.js"></script>
<script type="text/javascript" src="<?= base_url() ?>js/jquery.fancybox.js"></script>
<script type="text/javascript" src="<?= base_url() ?>js/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>js/jquery.url.js"></script>
<script type="text/javascript" src="<?= base_url() ?>js/oauth.js"></script>
<script type="text/javascript" src="<?= base_url() ?>js/sha1.js"></script>
<script type="text/javascript">
//Global User Data:
var user_data = {
	 'user_id':<?= $logged_user_id ?>,
	 'username':<?= $logged_username ?>,
	 'user_level_id':<?= $logged_user_level_id ?>,
	 'name':<?= $logged_name ?>,
	 'image':<?= $logged_image ?>,
	 'location':<?= $logged_location ?>,
	 'geo_enabled':<?= $logged_geo_enabled ?>,
	 'privacy':<?= $logged_privacy ?>
}

// oauthAjax plugin allows ajax request to be signed with oauth token
(function($)
{
	$.fn.oauthAjax = function(settings)
	{
		var oauth_consumer_key 		= '<?= $oauth_consumer_key ?>';
		var oauth_consumer_secret 	= '<?= $oauth_consumer_secret ?>';
		var oauth_token				= '<?= $oauth_token ?>';
		var oauth_token_secret 		= '<?= $oauth_token_secret ?>';		

		var accessor = { 
			consumerSecret	: oauth_consumer_secret,
			tokenSecret		: oauth_token_secret,
		};	
		
		var parameters = [
			["oauth_consumer_key", oauth_consumer_key],
			["oauth_token", oauth_token]
		];
		
		// only works if settings.data is a map (i.e., { "foo": "bar", "blah" : "yuck" })
		for (var name in settings.data)
			parameters.push([name, settings.data[name]]);
				
		var message = {
			method: settings.type || "GET",
			action: settings.url,
			parameters: parameters
		}
		
		OAuth.setTimestampAndNonce(message);
		OAuth.SignatureMethod.sign(message, accessor);
		
		var oldBeforeSend = settings.beforeSend;
		settings.beforeSend = function(xhr)
		{
			xhr.setRequestHeader("Authorization", OAuth.getAuthorizationHeader("", message.parameters))
			if (oldBeforeSend) oldBeforeSend(xhr);
		};
	
		jQuery.ajax(settings);

		var element = this;
	};
})(jQuery);
</script>

<script type="text/javascript" src="<?= base_url() ?>js/social.core.js"></script>
<script type="text/javascript" src="<?= $dashboard_assets ?>dashboard.js"></script>