<?php
class Home extends Dashboard_Controller 
{ 
    function __construct() 
    {
        parent::__construct();
    }
 
 	// Home Feed
 	function index()
 	{	
		if ($this->uri->total_segments() == 1)
		{
	 	    $this->data['page_title'] 		= 'Home';
			$this->data['home_greeting']	= random_element(config_item('home_greeting'));
			$this->data['status_updater']	= $this->load->view(config_item('dashboard_theme').'/partials/status_updater', $this->data, true); 	    
 	    
 	    	$feed_module = NULL;
 	    }
 	    // Fix For MODULE Checking
 	    else
 	    {
	 	    $this->data['page_title'] 		= ucwords($this->uri->segment(2));
 			$this->data['sub_title']		= 'Recent';
 
 	    	$feed_module = $this->uri->segment(2);
 	    }
 	    
		// Feed 
		$timeline 							= $this->social_igniter->get_timeline(10, $feed_module);
		$timeline_view 						= NULL;
		 			
		if (!empty($timeline))
		{
			foreach ($timeline as $activity)
			{			
				// Item
				$this->data['item_id']				= $activity->activity_id;
				$this->data['item_type']			= item_type_class($activity->type);
				
				// Contributor
				$this->data['item_user_id']			= $activity->user_id;
				$this->data['item_avatar']			= $this->social_igniter->profile_image($activity->user_id, $activity->image, $activity->email);
				$this->data['item_contributor']		= $activity->name;
				$this->data['item_profile']			= base_url().'profile/'.$activity->username;
				
				// Activity
				$this->data['item_content']			= $this->social_igniter->render_item($activity);
				$this->data['item_content_id']		= $activity->content_id;
				$this->data['item_date']			= format_datetime(config_item('home_date_style'), $activity->created_at);

		 		// Actions
			 	$this->data['item_comment']			= base_url().'comment/item/'.$activity->activity_id;
			 	$this->data['item_comment_avatar']	= $this->data['logged_image'];
			 	
			 	$this->data['item_can_modify']		= $this->social_tools->has_access_to_modify($activity->type, $activity->activity_id);
				$this->data['item_edit']			= base_url().'home/'.$activity->module.'/manage/'.$activity->content_id;
				$this->data['item_delete']			= base_url().'status/delete/'.$activity->activity_id;

				// View
				$timeline_view .= $this->load->view(config_item('dashboard_theme').'/partials/item_timeline.php', $this->data, true);
	 		}
	 	}
	 	else
	 	{
	 		$timeline_view = '<li>Nothing to show from anyone!</li>';
 		}

		// Final Output
		$this->data['timeline_view'] 	= $timeline_view;
		$this->render();
 	}   

 	function friends()
 	{ 	
 	    $this->data['page_title'] 		= "Friends";
		 	 	
		$this->render();
 	}

 	function mentions()
 	{
 	    $this->data['page_title'] 		= "@ Replies";
		 	 	
		$this->render();
 	}

	function likes()
	{
 	    $this->data['page_title'] 		= "Likes";		
	
		$this->render();
	}
	
	function view()
	{
		$this->render();
	}

	// Dashboard Comments Section
 	function comments()
 	{
 	    $this->data['page_title'] 	= "Comments";
 	    $this->data['sub_title'] 	= "Recent";
 		$this->data['navigation']	= $this->load->view(config_item('dashboard_theme').'/partials/navigation_comments.php', $this->data, true);

		$comments 					= $this->social_tools->get_comments(config_item('site_id'), $this->session->userdata('user_id'), $this->uri->segment(3));		
		$comments_view 				= NULL;
		$this->data['feed_type']	= 'comments';
    	$this->data['item_verb']	= item_type($this->lang->line('object_types'), 'comment');
	
		if (empty($comments))
		{
			 $comments_view = '<li>No comments to show!</li>';
	 	}
	 	else
	 	{
			foreach ($comments as $comment)
			{
				// Item
				$this->data['item_id']				= $comment->comment_id;
				$this->data['item_type']			= item_type_class($comment->type);
				$this->data['item_viewed']			= item_viewed('item', $comment->viewed);
				
				// Contributor
				$this->data['item_avatar']			= $this->social_igniter->profile_image($comment->user_id, $comment->image, $comment->email);
				$this->data['item_contributor']		= $comment->name;
				$this->data['item_profile']			= base_url().'profile/'.$comment->username;

				// Activity
				if ($comment->title)
				{
					$this->data['item_article']		= '';
					$this->data['item_object']		= $comment->title;
				}
				else
				{
					$this->data['item_article']		= item_type($this->lang->line('object_articles'), $comment->type);
					$this->data['item_object']		= $comment->type;
				}
				
				$this->data['item_text']			= $comment->comment;
				$this->data['item_date']			= human_date('SIMPLE', mysql_to_unix($comment->created_at));
				$this->data['item_approval']		= $comment->approval;
		
		 		// Actions
				$this->data['item_view'] 			= base_url().$comment->module.'/view/'.$comment->content_id.'/'.$comment->comment_id;
				$this->data['item_reply'] 			= base_url().$comment->module.'/reply/id/'.$comment->content_id.'/'.$comment->comment_id;
				$this->data['item_approve']			= base_url().'api/comments/approve/id/'.$comment->comment_id;
				$this->data['item_delete']			= base_url().'api/comments/destroy/id/'.$comment->comment_id;

				// Alerts
				$this->data['item_alerts']			= item_alerts_comment($comment);

				// Load Partial For Items
				$comments_view 				   	   .= $this->load->view(config_item('dashboard_theme').'/partials/item_comments.php', $this->data, true);
	 		}
 		}
		
		$this->data['comments_view'] = $comments_view;	
				
		$this->render();
	}
	
	function manage()
	{
		$content_module		= $this->social_igniter->get_content_view('module', $this->uri->segment(2));
		$manage_view 		= NULL;

		// Title Stuff
		$this->data['page_title']	= ucwords($this->uri->segment(2));
		$this->data['sub_title']	= 'Manage';
		 
		foreach($content_module as $content):
		
			$this->data['item_id'] 				= $content->content_id;
			$this->data['item_type']			= $content->type;
			$this->data['item_viewed']			= item_viewed('item_manage', $content->viewed);

			$this->data['title']				= item_title($content->title, $content->type);
			$this->data['title_link']			= base_url().$content->module.'/view/'.$content->content_id;
			$this->data['comments_count']		= manage_comments_count($content->comments_count);
			$this->data['publish_date']			= manage_published_date($content->created_at, $content->updated_at);
			$this->data['item_status']			= display_content_status($content->status);
			
			$this->data['item_approval']		= $content->approval;
			
			// Actions
			$this->data['item_approve']			= base_url().'api/content/approve/id/'.$content->content_id;
			$this->data['item_edit']			= base_url().'home/'.$content->module.'/manage/'.$content->content_id;
			$this->data['item_delete']			= base_url().'api/content/destroy/id/'.$content->content_id;

			// Alerts
			$this->data['item_alerts']			= item_alerts_content($content);
			
			// View
			$manage_view .= $this->load->view(config_item('dashboard_theme').'/partials/item_manage.php', $this->data, true);			

		endforeach;	

		// Final Output
		$this->data['timeline_view'] 	= $manage_view;				
		
		$this->render('dashboard_wide');
	}
	
	
	/* Partials */
	function item_timeline()
	{
		$this->data['item_id']				= '{ITEM_ID}';
		$this->data['item_type']			= '{ACTIVITY_TYPE}';
		
		// Contributor
		$this->data['item_user_id']			= '{ITEM_USER_ID}';
		$this->data['item_avatar']			= '{ITEM_AVATAR}';
		$this->data['item_contributor']		= '{ITEM_CONTRIBUTOR}';
		$this->data['item_profile']			= base_url().'profiles/{ITEM_PROFILE}';
		
		// Activity
		$this->data['item_content']			= '{ITEM_CONTENT}';
		$this->data['item_content_id']		= '{ITEM_CONTENT_ID}';
		$this->data['item_date']			= '{ITEM_DATE}';

 		// Actions
		$this->data['item_comment']			= base_url().'comment/item/{ACTIVITY_ID}';
		$this->data['item_comment_avatar']	= '{ITEM_COMMENT_AVATAR}';
		
	 	
	 	$this->data['item_can_modify']		= '{ITEM_CAN EDIT}';
		$this->data['item_edit']			= base_url().'home/{ACTIVITY_MODULE}/manage/{ITEM_CONTENT_ID}';
		$this->data['item_delete']			= base_url().'status/delete/{ACTIVITY_ID}';			
	
		$this->load->view(config_item('dashboard_theme').'/partials/item_timeline', $this->data);
	}
	
	function item_manage()
	{
		$this->data['item_id'] 				= '{ITEM_ID}';
		$this->data['comments_count']		= '{COMMENTS_COUNT}';
		$this->data['item_type']			= '{ACTIVITY_TYPE}';
		$this->data['title']				= '{ITEM_TITLE}';
		$this->data['title_link']			= base_url().'{MODULE}/view/{ITEM_ID}';
		$this->data['publish_date']			= '{PUBLISHED_DATE}';
		$this->data['status']				= '{ITEM_STATUS}';
		
		$this->data['item_approval']		= '{ITEM_APPROVAL}';
	
		// Actions
		$this->data['item_approve']			= base_url().'api/content/approve/id/{ITEM_ID}';
		$this->data['item_edit']			= base_url().'home/{MODULE}/manage/{ITEM_ID}';
		$this->data['item_delete']			= base_url().'home/{MODULE}/manage/{ITEM_ID}';	
	
		$this->load->view(config_item('dashboard_theme').'/partials/item_manage', $this->data);
	}
	
	function category_editor()
	{
		$this->load->view(config_item('dashboard_theme').'/partials/category_editor');
	}

}