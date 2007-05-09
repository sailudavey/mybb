<?php
/**
 * MyBB 1.2
 * Copyright � 2007 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.net
 * License: http://www.mybboard.net/license.php
 *
 * $Id$
 */


$page->add_breadcrumb_item("Smilies", "index.php?".SID."&amp;module=config/smilies");


if($mybb->input['action'] == "add")
{
	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['name']))
		{
			$errors[] = "You did not enter a name for this smilie";
		}

		if(!trim($mybb->input['find']))
		{
			$errors[] = "You did not enter a text replacement for this smilie";
		}

		if(!trim($mybb->input['image']))
		{
			$errors[] = "You did not enter a path for this smilie";
		}

		if(!trim($mybb->input['disporder']))
		{
			$errors[] = "You did not enter a display order for this smilie";
		}
		
		if(!trim($mybb->input['showclickable']))
		{
			$errors[] = "You did not select Yes or No for the \"Show Clickable\" option";
		}

		if(!$errors)
		{
			$new_smilie = array(
				"name" => $db->escape_string($mybb->input['name']),
				"find" => $db->escape_string($mybb->input['find']),
				"image" => $db->escape_string($mybb->input['image']),
				"disporder" => intval($mybb->input['disporder']),
				"showclickable" => $db->escape_string($mybb->input['showclickable'])
			);
			
			$db->insert_query("smilies", $new_smilie);

			$cache->update_smilies();
			
			flash_message("The smilie has been added successfully.", 'success');
			admin_redirect("index.php?".SID."&module=config/smilies");
		}
	}
	
	$page->add_breadcrumb_item("Add Smilie");
	$page->output_header("Smilies - Add Smilie");
	
	$sub_tabs['add_smilie'] = array(
		'title' => "Add Smilie",
		'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=add",
		'description' => 'Here you can add a new smilie.'
	);
	$sub_tabs['add_multiple_smilies'] = array(
		'title' => "Add Multiple Smilies",
		'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=add_multiple",
	);
	
	$page->output_nav_tabs($sub_tabs, 'add_smilie');
	$form = new Form("index.php?".SID."&amp;module=config/smilies&amp;action=add", "post", "add");
	
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	else
	{
		$mybb->input['image'] = 'images/smilies/';
		$mybb->input['showclickable'] = 'yes';
	}
	
	if(!$mybb->input['disporder'])
	{
		$query = $db->simple_select("smilies", "max(disporder) as dispordermax");
		$mybb->input['disporder'] = $db->fetch_field($query, "dispordermax")+1;
	}
	
	$form_container = new FormContainer("Add Smilie");
	$form_container->output_row("Name", "", $form->generate_text_box('name', $mybb->input['name'], array('id' => 'name')), 'name');
	$form_container->output_row("Text to Replace", "", $form->generate_text_box('find', $mybb->input['find'], array('id' => 'find')), 'find');
	$form_container->output_row("Image Path", "This is the path to the smilie image.", $form->generate_text_box('image', $mybb->input['image'], array('id' => 'image')), 'image');
	$form_container->output_row("Display Order", "The order on the smilies list that this will appear. This number should not be the same as another smilie's.", $form->generate_text_box('disporder', $mybb->input['disporder'], array('id' => 'disporder')), 'disporder');
	$form_container->output_row("Show on clickable list?", "Do you want this smilie to show on the clickable smilie list on the post editor?", $form->generate_yes_no_radio('showclickable', $mybb->input['showclickable']), 'showclickable');
	$form_container->end();

	$buttons[] = $form->generate_submit_button("Save Smilie");

	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "edit")
{
	$query = $db->simple_select("smilies", "*", "sid='".intval($mybb->input['sid'])."'");
	$smilie = $db->fetch_array($query);

	// Does the smilie not exist?
	if(!$smilie['sid'])
	{
		flash_message('The specified smilie does not exist.', 'error');
		admin_redirect("index.php?".SID."&module=config/smilies");
	}

	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['name']))
		{
			$errors[] = "You did not enter a name for this smilie";
		}

		if(!trim($mybb->input['find']))
		{
			$errors[] = "You did not enter a text replacement for this smilie";
		}

		if(!trim($mybb->input['image']))
		{
			$errors[] = "You did not enter a path for this smilie";
		}

		if(!isset($mybb->input['disporder']))
		{
			$errors[] = "You did not enter a display order for this smilie";
		}
		
		if(!trim($mybb->input['showclickable']))
		{
			$errors[] = "You did not select Yes or No for the \"Show Clickable\" option";
		}

		if(!$errors)
		{
			$smilie = array(
				"name" => $db->escape_string($mybb->input['name']),
				"find" => $db->escape_string($mybb->input['find']),
				"image" => $db->escape_string($mybb->input['image']),
				"disporder" => intval($mybb->input['disporder']),
				"showclickable" => $db->escape_string($mybb->input['showclickable'])
			);
			
			$db->update_query("smilies", $smilie, "sid = '".intval($mybb->input['sid'])."'");
			
			$cache->update_smilies();

			flash_message("The smilie has successfully been updated.", 'success');
			admin_redirect("index.php?".SID."&module=config/smilies");
		}
	}
	
	$page->add_breadcrumb_item("Edit Smilie");
	$page->output_header("Smilies - Edit Smilie");
	
	$sub_tabs['edit_smilie'] = array(
		'title' => "Edit Smilie",
		'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=edit",
		'description' => 'Here you can edit a smilie.'
	);
	$sub_tabs['mass_edit'] = array(
		'title' => "Mass Edit",
		'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=mass_edit",
	);
	
	$page->output_nav_tabs($sub_tabs, 'edit_smilie');
	$form = new Form("index.php?".SID."&amp;module=config/smilies&amp;action=edit", "post", "edit");
	
	echo $form->generate_hidden_field("sid", $smilie['sid']);
	
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	else
	{
		$mybb->input = $smilie;
	}

	$form_container = new FormContainer("Edit Smilie");
	$form_container->output_row("Name", "", $form->generate_text_box('name', $mybb->input['name'], array('id' => 'name')), 'name');
	$form_container->output_row("Text to Replace", "", $form->generate_text_box('find', $mybb->input['find'], array('id' => 'find')), 'find');
	$form_container->output_row("Image Path", "This is the path to the smilie image.", $form->generate_text_box('image', $mybb->input['image'], array('id' => 'image')), 'image');
	$form_container->output_row("Display Order", "The order on the smilies list that this will appear. This number should not be the same as another smilie's.", $form->generate_text_box('disporder', $mybb->input['disporder'], array('id' => 'disporder')), 'disporder');
	$form_container->output_row("Show on clickable list?", "Do you want this smilie to show on the clickable smilie list on the post editor?", $form->generate_yes_no_radio('showclickable', $mybb->input['showclickable']), 'showclickable');
	$form_container->end();

	$buttons[] = $form->generate_submit_button("Save Smilie");
	$buttons[] = $form->generate_reset_button("Reset");

	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "delete")
{
	$query = $db->simple_select("smilies", "*", "sid='".intval($mybb->input['sid'])."'");
	$smilie = $db->fetch_array($query);

	// Does the smilie not exist?
	if(!$smilie['sid'])
	{
		flash_message('The specified smilie does not exist.', 'error');
		admin_redirect("index.php?".SID."&module=config/smilies");
	}

	// User clicked no
	if($mybb->input['no'])
	{
		admin_redirect("index.php?".SID."&module=config/smilies");
	}

	if($mybb->request_method == "post")
	{
		// Delete the smilie
		$db->delete_query("smilies", "sid='{$smilie['sid']}'");

		$cache->update_smilies();

		flash_message('The specified smilie has been deleted.', 'success');
		admin_redirect("index.php?".SID."&module=config/smilies");
	}
	else
	{
		$page->output_confirm_action("index.php?".SID."&amp;module=config/smilies&amp;action=delete&amp;sid={$smilie['sid']}", "Are you sure you wish to delete this smilie?");
	}}

if($mybb->input['action'] == "add_multiple")
{
	if($mybb->request_method == "post")
	{
		if($mybb->input['step'] == 1)
		{
			if(!trim($mybb->input['pathfolder']))
			{
				$errors[] = "You did not enter a path";
			}
			
			$path = $mybb->input['pathfolder'];
			$dir = @opendir(MYBB_ROOT.$path);
			
			if(!$dir)
			{
				$errors[] = "You did not enter a valid path";
			}
			
			if($path)
			{
				if(substr($path, -1, 1) !== "/")
				{
					$path .= "/";
				}
				
				$query = $db->simple_select("smilies");
				while($smilie = $db->fetch_array($query))
				{
					$asmilies[$smilie['image']] = 1;
				}
				
				while($file = readdir($dir))
				{
					if($file != ".." && $file != ".")
					{
						$ext = get_extension($file);
						if($ext == "gif" || $ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "bmp")
						{
							if(!$asmilies[$path.$file])
							{
								$smilies[] = $file;
							}
						}
					}
				}
				closedir($dir);
				
				if(count($smilies) == 0)
				{
					$errors[] = "There are no smilies in the specified directory, or all smilies in the directory have already been added.";
				}
			}
			
			if(!$errors)
			{
				$page->add_breadcrumb_item("Add Multiple Smilie");
				$page->output_header("Smilies - Add Multiple Smilies");
				
				$sub_tabs['add_smilie'] = array(
					'title' => "Add Smilie",
					'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=add"
				);
				$sub_tabs['add_multiple_smilies'] = array(
					'title' => "Add Multiple Smilies",
					'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=add_multiple",
					'description' => 'Here you can add multiple new smilie.'
				);
				
				$page->output_nav_tabs($sub_tabs, 'add_multiple_smilies');
				$form = new Form("index.php?".SID."&amp;module=config/smilies&amp;action=add_multiple", "post", "add_multiple");
				echo $form->generate_hidden_field("step", "2");
				echo $form->generate_hidden_field("pathfolder", $path);
				
				$form_container = new FormContainer("Add Multiple Smilies");
				$form_container->output_row_header("Image", array("class" => "align_center", 'width' => '10%'));
				$form_container->output_row_header("Name");
				$form_container->output_row_header("Text to Replace", array('width' => '20%'));
				$form_container->output_row_header("Include?", array("class" => "align_center", 'width' => '5%'));			
		
				foreach($smilies as $key => $file)
				{
					$ext = get_extension($file);
					$find = str_replace(".".$ext, "", $file);
					$name = ucfirst($find);
				
					$form_container->output_cell("<img src=\"../".$path.$file."\" alt=\"\" /><br /><small>{$file}</small>", array("class" => "align_center", "width" => 1));
					$form_container->output_cell($form->generate_text_box("name[{$file}]", $name, array('id' => 'name', 'style' => 'width: 98%')));
					$form_container->output_cell($form->generate_text_box("find[{$file}]", ":".$find.":", array('id' => 'find', 'style' => 'width: 95%')));
					$form_container->output_cell($form->generate_check_box("include[{$file}]", "yes", "", array('checked' => 1)), array("class" => "align_center"));
					$form_container->construct_row();
				}
				
				if(count($form_container->container->rows) == 0)
				{
					flash_message('There are no images in the specified directory', 'error');
					admin_redirect("index.php?".SID."&module=config/smilies&action=add_multiple");
				}
				
				$form_container->end();
				
				$buttons[] = $form->generate_submit_button("Add Smilies");
			
				$form->output_submit_wrapper($buttons);
				$form->end();
				
				$page->output_footer();
				exit;
			}
		}
		else
		{
			$path = $mybb->input['pathfolder'];
			reset($mybb->input['include']);
			$find = $mybb->input['find'];
			$name = $mybb->input['name'];
			
			if(empty($mybb->input['include']))
			{
				flash_message('You did not select any smilies to include.', 'error');
				admin_redirect("index.php?".SID."&module=config/smilies&action=add_multiple");
			}
			
			foreach($mybb->input['include'] as $image => $insert)
			{
				if($insert)
				{
					$new_smilie = array(
						"name" => $db->escape_string($name[$image]),
						"find" => $db->escape_string($find[$image]),
						"image" => $db->escape_string($path.$image),
						"showclickable" => "yes"
					);
					$db->insert_query("smilies", $new_smilie);
				}
			}

			$cache->update_smilies();
			
			flash_message('The selected smilies have successfully been created.', 'success');
			admin_redirect("index.php?".SID."&module=config/smilies");
		}
	}
	
	$page->add_breadcrumb_item("Add Multiple Smilie");
	$page->output_header("Smilies - Add Multiple Smilies");
	
	$sub_tabs['add_smilie'] = array(
		'title' => "Add Smilie",
		'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=add"
	);
	$sub_tabs['add_multiple_smilies'] = array(
		'title' => "Add Multiple Smilies",
		'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=add_multiple",
		'description' => 'Here you can add multiple new smilie.'
	);
	
	$page->output_nav_tabs($sub_tabs, 'add_multiple_smilies');
	$form = new Form("index.php?".SID."&amp;module=config/smilies&amp;action=add_multiple", "post", "add_multiple");
	echo $form->generate_hidden_field("step", "1");
	
	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$form_container = new FormContainer("Add Multiple Smilies");
	$form_container->output_row("Path to Images", "This is the path to the folder that the images are in.", $form->generate_text_box('pathfolder', $mybb->input['pathfolder'], array('id' => 'pathfolder')), 'pathfolder');
	$form_container->end();

	$buttons[] = $form->generate_submit_button("Show Smilies");

	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "mass_edit")
{
	if($mybb->request_method == "post")
	{
		foreach($mybb->input['name'] as $sid => $name)
		{
			if($mybb->input['delete'][$sid] == "yes")
			{
				$db->delete_query("smilies", "sid = '{$sid}'", 1);
			}
			else
			{
				$smilie = array(
					"name" => $db->escape_string($mybb->input['name'][$sid]),
					"find" => $db->escape_string($mybb->input['find'][$sid]),
					"disporder" => intval($mybb->input['disporder'][$sid]),
					"showclickable" => $db->escape_string($mybb->input['showclickable'][$sid])
				);
					
				$db->update_query("smilies", $smilie, "sid = '{$sid}'");
			}
		}
		
		$cache->update_smilies();

		flash_message("The smilies have successfully been updated.", 'success');
		admin_redirect("index.php?".SID."&module=config/smilies");
	}
	
	$page->add_breadcrumb_item("Mass Edit");
	$page->output_header("Smilies - Mass Edit");

	$sub_tabs['mass_edit'] = array(
		'title' => "Mass Edit",
		'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=mass_edit",
		'description' => 'Here you can easily edit all your smilies in one go.'
	);
	
	$page->output_nav_tabs($sub_tabs, 'mass_edit');
	
	$form = new Form("index.php?".SID."&amp;module=config/smilies&amp;action=mass_edit", "post", "mass_edit");
	
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	else
	{
		$mybb->input['path'] = 'images/smilies/';
		$mybb->input['showclickable'] = 'yes';
	}
	
	if(!$mybb->input['disporder'])
	{
		$query = $db->simple_select("smilies", "max(disporder) as dispordermax");
		$mybb->input['disporder'] = $db->fetch_field($query, "dispordermax")+1;
	}
	
	$form_container = new FormContainer("Manage Smilie");
	$form_container->output_row_header("Image", array("class" => "align_center", 'width' => '1'));
	$form_container->output_row_header("Name");
	$form_container->output_row_header("Text to Replace", array('width' => '20%'));
	$form_container->output_row_header("Order", array('width' => '5%'));
	$form_container->output_row_header("Show on Clickable?", array("width" => 135));
	$form_container->output_row_header("Delete?", array("class" => "align_center", 'width' => '5%'));
	
	$query = $db->simple_select("smilies", "*", "", array('order_by' => 'disporder'));
	while($smilie = $db->fetch_array($query))
	{
		$smilie['image'] = str_replace("{theme:imgdir}", $theme['imgdir'], $smilie['image']);
		if(my_strpos($smilie['image'], "p://") || substr($smilie['image'], 0, 1) == "/") 
		{
			$image = $smilie['image'];
		}
		else
		{
			$image = "../".$smilie['image'];
		}
		
		$form_container->output_cell("<img src=\"{$image}\" alt=\"\" />", array("class" => "align_center", "width" => 1));
		$form_container->output_cell($form->generate_text_box("name[{$smilie['sid']}]", $smilie['name'], array('id' => 'name', 'style' => 'width: 98%')));
		$form_container->output_cell($form->generate_text_box("find[{$smilie['sid']}]", $smilie['find'], array('id' => 'find', 'style' => 'width: 95%')));
		$form_container->output_cell($form->generate_text_box("disporder[{$smilie['sid']}]", $smilie['disporder'], array('id' => 'disporder', 'style' => 'width: 80%')));
		$form_container->output_cell($form->generate_yes_no_radio("showclickable[{$smilie['sid']}]", $smilie['showclickable']), array("class" => "align_center"));
		$form_container->output_cell($form->generate_check_box("delete[{$smilie['sid']}]", "yes", $mybb->input['delete']), array("class" => "align_center"));
		$form_container->construct_row();
	}
	
	if(count($form_container->container->rows) == 0)
	{
		$form_container->output_cell("There are no smilies on your forum at this time.", array('colspan' => 6));
		$form_container->construct_row();
	}
	
	$form_container->end();
	
	$buttons[] = $form->generate_submit_button("Save Smilies");
	$buttons[] = $form->generate_reset_button("Reset");

	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if(!$mybb->input['action'])
{
	$page->output_header("Manage Smilies");

	$sub_tabs['manage_smilies'] = array(
		'title' => "Manage Smilies",
		'link' => "index.php?".SID."&amp;module=config/smilies",
		'description' => "This section allows you to edit, delete, and manage your smilies."
	);
	$sub_tabs['add_smilie'] = array(
		'title' => "Add Smilie",
		'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=add",
	);
	$sub_tabs['add_multiple_smilies'] = array(
		'title' => "Add Multiple Smilies",
		'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=add_multiple",
	);
	$sub_tabs['mass_edit'] = array(
		'title' => "Mass Edit",
		'link' => "index.php?".SID."&amp;module=config/smilies&amp;action=mass_edit",
	);
	
	$page->output_nav_tabs($sub_tabs, 'manage_smilies');
	
	$pagenum = intval($mybb->input['page']);	
	if($pagenum)
	{
		$start = ($pagenum-1) * 20;
	}
	else
	{
		$start = 0;
		$pagenum = 1;
	}
	
	
	$table = new Table;
	$table->construct_header("Image", array("class" => "align_center", "width" => 1));
	$table->construct_header("Name", array("width" => "40%"));
	$table->construct_header("Text to Replace", array("width" => "40%"));
	$table->construct_header("Controls", array("class" => "align_center", "colspan" => 2));
	
	$query = $db->simple_select("smilies", "*", "", array('limit_start' => $start, 'limit' => 20, 'order_by' => 'disporder'));
	while($smilie = $db->fetch_array($query))
	{
		$smilie['image'] = str_replace("{theme:imgdir}", $theme['imgdir'], $smilie['image']);
		if(my_strpos($smilie['image'], "p://") || substr($smilie['image'], 0, 1) == "/") 
		{
			$image = $smilie['image'];
		}
		else
		{
			$image = "../".$smilie['image'];
		}
		
		$table->construct_cell("<img src=\"{$image}\" alt=\"\" />", array("class" => "align_center"));
		$table->construct_cell("{$smilie['name']}");
		$table->construct_cell($smilie['find']);
		
		$table->construct_cell("<a href=\"index.php?".SID."&amp;module=config/smilies&amp;action=edit&amp;sid={$smilie['sid']}\">Edit</a>", array("class" => "align_center"));
		$table->construct_cell("<a href=\"index.php?".SID."&amp;module=config/smilies&amp;action=delete&amp;sid={$smilie['sid']}\" onclick=\"return AdminCP.deleteConfirmation(this, 'Are you sure you wish to delete this smilie?')\">Delete</a>", array("class" => "align_center"));
		$table->construct_row();
	}
	
	if(count($table->rows) == 0)
	{
		$table->construct_cell("There are no smilies on your forum at this time.", array('colspan' => 4));
		$table->construct_row();
	}
	
	$table->output("Manage Smilies");
	
	$query = $db->simple_select("smilies", "COUNT(sid) as smilies");
	$total_rows = $db->fetch_field($query, "smilies");
	
	echo "<br />".draw_admin_pagination($pagenum, "20", $total_rows, "index.php?".SID."&amp;module=config/smilies&amp;page={page}");

	$page->output_footer();
}
?>