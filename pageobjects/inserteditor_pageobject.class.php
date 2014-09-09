<?php
/*
 * Project:     EQdkp guildrequest
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-10-13 22:48:23 +0200 (Sa, 13. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest
 * @version     $Rev: 12273 $
 *
 * $Id: archive.php 12273 2012-10-13 20:48:23Z godmod $
 */


class inserteditor_pageobject extends pageobject {
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array();
   	return array_merge(parent::__shortcuts(), $shortcuts);
  }  
  
  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('mediacenter', PLUGIN_INSTALLED))
      message_die($this->user->lang('mc_plugin_not_installed'));
    
    $this->user->check_auth('u_mediacenter_view');
    
    $handler = array();
    parent::__construct(false, $handler);

    $this->process();
  }
  
  public function display(){
  	
  	if ($this->in->get('cid', 0)){
  		$intCategoryID = $this->in->get('cid', 0);
  		$arrAlbums = $this->pdh->get('mediacenter_albums', 'albums_for_category', array($intCategoryID));$view_list = $this->pdh->get('mediacenter_categories', 'id_list', array());
  		$arrPermissions = $this->pdh->get('mediacenter_categories', 'user_permissions', array($intCategoryID, $this->user->id));
  		if (!$arrPermissions['read']) $this->user->check_auth('u_mediacenter_something');
	  	
  		$hptt_page_settings = array(
  				'name'				=> 'hptt_mc_editor_medialist',
  				'table_main_sub'	=> '%intMediaID%',
  				'table_subs'		=> array('%intCategoryID%', '%intMediaID%'),
  				'page_ref'			=> $this->routing->simpleBuild('InsertMediaEditor'),
  				'show_numbers'		=> false,
  				'show_select_boxes'	=> true,
  				'selectboxes_checkall'=>true,
  				'show_detail_twink'	=> false,
  				'table_sort_dir'	=> 'asc',
  				'table_sort_col'	=> 1,
  				'table_presets'		=> array(
  						array('name' => 'mediacenter_media_previewimage',	'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
  						array('name' => 'mediacenter_media_name',		'sort' => true, 'th_add' => '', 'td_add' => ''),
  						array('name' => 'mediacenter_media_type','sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
  						array('name' => 'mediacenter_media_date','sort' => true, 'th_add' => 'width="20"', 'td_add' => 'nowrap="nowrap"'),
  				),
  		);
  		
  		$page_suffix = '&amp;start='.$this->in->get('start', 0).'&amp;simple_head=1';
  		$sort_suffix = '?sort='.$this->in->get('sort');
  		
  		$arrAlbums = $this->pdh->get('mediacenter_albums', 'albums_for_category', array($intCategoryID));$view_list = $this->pdh->get('mediacenter_categories', 'id_list', array());
  		foreach($arrAlbums as $intAlbumID){
  			$view_list = $this->pdh->get('mediacenter_media', 'id_list', array($intAlbumID, true));
  			$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_media.php', '%link_url_suffix%' => '&amp;upd=true'), $intCategoryID.'.'.$intAlbumID);
  		
  			$this->tpl->assign_block_vars('album_list', array(
  					'NAME'				=> $this->pdh->get('mediacenter_albums', 'name', array($intAlbumID)),
  					'S_PERSONAL'		=> $this->pdh->get('mediacenter_albums', 'personal_album', array($intAlbumID)) ? true : false,
  					'USER'				=> $this->pdh->get('user', 'name', array($this->pdh->get('mediacenter_albums', 'user_id', array($intAlbumID)))),
  					'ID'				=> $intAlbumID,
  					'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
  					'MEDIA_LIST'		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix,null,1,null,false, array('mediacenter_media', 'checkbox_check')),
  			));
  		}
  		
  		$this->tpl->assign_vars(array(
  			'S_MEDIA'		=> true,
  		));
  	} else {
  		
  		$view_list = $this->pdh->get('mediacenter_categories', 'published_id_list', array());
  		$hptt_page_settings = array(
  				'name'				=> 'hptt_mc_editor_categories_categorylist',
  				'table_main_sub'	=> '%intCategoryID%',
  				'table_subs'		=> array('%intCategoryID%', '%intArticleID%'),
  				'page_ref'			=> $this->routing->simpleBuild('InsertMediaEditor'),
  				'show_numbers'		=> false,
  				'show_select_boxes'	=> false,
  				'selectboxes_checkall'=>false,
  				'show_detail_twink'	=> false,
  				'table_sort_dir'	=> 'asc',
  				'table_sort_col'	=> 0,
  				'table_presets'		=> array(
  						array('name' => 'mediacenter_categories_name',		'sort' => true, 'th_add' => '', 'td_add' => ''),
  						array('name' => 'mediacenter_categories_album_count','sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
  						array('name' => 'mediacenter_categories_media_count','sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
  				),
  		);
  		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->simpleBuild('InsertMediaEditor'), '%link_url_suffix%' => '&amp;simple_head=1'));
  		$page_suffix = '&amp;start='.$this->in->get('start', 0).'&amp;simple_head=1';
  
  		
  		$this->tpl->assign_vars(array(
  				'CATEGORY_LIST'		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix,null,1,null,false, array('mediacenter_categories', 'checkbox_check')),
  				'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
  				'S_CATEGORIES'		=> true,
  		));
  		
  		
  	}
  	
  	
  	// -- EQDKP ---------------------------------------------------------------
  	$this->core->set_vars(array (
  			'page_title'    => $this->user->lang('mc_edit_media'),
  			'template_path' => $this->pm->get_data('mediacenter', 'template_path'),
  			'template_file' => 'insert_media_editor.html',
  			'display'       => true
  	));
  }
}
?>