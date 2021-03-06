<?php

class pz_address_screen{

	function __construct($address) 
	{
		$this->address = $address;
	}

	// ---------------------------------------------------------------- VIEWS

	/*
	function getMatrixView($p = array()) 
	{
    
		$customer_name = rex_i18n::msg("no_customer");
    
		$return = '
		      <article>
            <header>
              <figure><img src="'.pz_user::getDefaultImage().'" width="40" height="40" alt="" /></figure>
              <hgroup>
                <h2 class="hl7"><span class="name">'.$customer_name.'</span><span class="info">'.$this->address->getVar("created", 'datetime').'</span></h2>
                <h3 class="hl7"><a href="'.pz::url("screen","addresses","view",array("address_id"=>$this->address->getId())).'"><span class="title">'.$this->address->getVar("name").'</span></a></h3>
              </hgroup>
            </header>
            
            <section class="content">
			<!-- TODO: Meldungen etc reinsetzen ? -->
            </section>
            
            <footer>
              <ul class="sl2">
                <li class="selected option"><span class="selected option">Optionen</span>
                  <div class="flyout">
                    <div class="content">
                      <ul class="entries">
                        <li class="entry first"><a href=""><span class="title">Spam</span></a></li>
                        <li class="entry"><a href=""><span class="title">Ham</span></a></li>
                        <li class="entry"><a href=""><span class="title">Trash</span></a></li>
                      </ul>
                    </div>
                  </div>
                </li>
              </ul>
              <span class="label labelc'.$this->address->getVar('label_id').'">Label</span>
            </footer>
          </article>
        ';
	
		return $return;
	}
	*/

	static function getTableListView($addresses,$p = array())
	{
		$content = "";
		$p["layer"] = 'addresses_list';
		
		$paginate_screen = new pz_paginate_screen($addresses);
		$paginate = $paginate_screen->getPlainView($p);
		
		foreach($paginate_screen->getCurrentElements() as $address) {
			$ps = new pz_address_screen($address);
			$content .= $ps->getTableView($p);
		}
		$content = $paginate.'
          <table class="projects tbl1">
          <thead><tr>
              <th></th>
              <th>'.rex_i18n::msg("address_name").'</th>
              <th>'.rex_i18n::msg("address_telephone").'</th>
              <th>'.rex_i18n::msg("address_emails").'</th>
              <th class="label"></th>
          </tr></thead>
          <tbody>
            '.$content.'
          </tbody>
          </table>';
		
		// <th>'.rex_i18n::msg("address").'</th>
		
		$f = new rex_fragment();
		$f->setVar('title', $p["title"], false);
		$f->setVar('content', $content , false);
		return '<div id="addresses_list" class="design2col">'.$f->parse('pz_screen_list').'</div>';
	}


	function getTableView($p = array())
	{
		$edit_link = pz::url("screen","addresses",$p["function"],array("address_id"=>$this->address->getId(),"mode"=>"edit_address"));

		$name = $this->address->getFullName();
		$name = '<a href="javascript:void(0)" onclick="pz_loadPage(\'address_form\',\''.$edit_link.'\')"><span class="title">'.htmlspecialchars($name).'</span></a>';
		$company = $this->address->getCompany();
		if($company != "")
			$name .= '<br />'.htmlspecialchars($company);
		
		$emails = array();
		$phones = array();
		$addresses = array();
		foreach($this->address->getFields() as $field)
		{
			switch($field->getVar("type")) {
				case("ADR"):
					$f = explode(";",$field->getVar("value"));
					$strasse = $f[2];
					$plz_ort = ", ".$f[5]." ".$f[3].", ".$f[6]." / ".$f[4];
					$v = $strasse.$plz_ort;
					$addresses[] = ' '.htmlspecialchars($v).' ['.htmlspecialchars($field->getVar("label")).']';
					break;	
				case("TEL"):
					$phones[] = ' <nobr>'.htmlspecialchars($field->getVar("value")).' ['.htmlspecialchars($field->getVar("label")).']</nobr>';	
					break;
				case("EMAIL"):
					$emails[] = ' <nobr> '.htmlspecialchars($field->getVar("value")).' ['.htmlspecialchars($field->getVar("label")).']</nobr>';	
					break;
			}
		}

		$return = '
              <tr>
                <td class="img1"><a href="javascript:void(0)" onclick="pz_loadPage(\'address_form\',\''.$edit_link.'\')"><img src="'.$this->address->getInlineImage().'" width="40" height="40" alt="" /></a></td>
                <td><span class="name">'.$name.'</span></td>
                <td>'.implode("<br />",$phones).'</td>
                <td>'.pz_email_screen::prepareOutput(implode("<br />",$emails), FALSE).'</td>
                <td class="label labelc'.$this->address->getVar('label_id').'"></td>
              </tr>            
        ';
	
		// <td>'.implode("<br />",$addresses).'</td>
	
		return $return;
	}

	function makeInlineImage($image_path, $size = "m") {

		$src = @imagecreatefrompng($image_path);
	    if($src) {
	    	imagealphablending($src, true);
			imagesavealpha($src, true);
	    	list($width, $height) = getimagesize($image_path);

	    	$new_width = 25;
	    	$new_height = 25;
	    	if($size == "m") {
		    	$new_width = 40;
		    	$new_height = 40;
	    	}

	    	$tmp = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($tmp, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			ob_start();
			imagePNG($tmp,NULL);
			$image = ob_get_contents();
			ob_end_clean();

			$base64_img = 'data:image/png;base64,'.base64_encode($image);
			return $base64_img;
	    }

	}

	public function getDetailView($p = array())
	{
		return "";
		/*
		$address_array = array(
			"id",
			"name",
			"firstname",
			"created",
			"updated",
			"created_user_id",
			"updated_user_id",
			"company",
			"birthday",
			"photo"
		);
		*/
	
		$return = "";
	
		foreach($this->address->getVars() as $k => $v)
		{
			if($k != "photo" && in_array($k,$address_array) && $v != "")
			$return .= '<br />'.$k.' - '.htmlspecialchars($v);	
		}

		$field_types = array("ADR","EMAIL","TEL","X-ABRELATEDNAMES");
		// value - ;;ArbeitStrasse 19;ffm;bundesland/bezirk/provinz;60311;deutschland
		// ; ignorieren / , ignorieren - macht addressbuch auch .. 
		
		foreach($this->address->getFields() as $field)
		{
			$vars = $field->getVars();
			if(in_array($vars["type"],$field_types)) {
				foreach($field->getVars() as $k => $v) {
					$return .= '<br />'.$k.' - '.htmlspecialchars($v);	
				}
			}
		}
	
		return '<div id="address_form">DETAIL VIEW'.$return.'</div>';
	
	}





	// ----------------------------------- Form

	static function getAddressesSearchForm ($p)
	{
		
    $return = '
        <header>
          <div class="header">
            <h1 class="hl1">'.rex_i18n::msg("search_for_addresses").'</h1>
          </div>
        </header>';
		
		$xform = new rex_xform;
		$xform->setObjectparams("real_field_names",TRUE);
		$xform->setObjectparams("form_name",'pz_address_search_form');
		$xform->setObjectparams("form_showformafterupdate", TRUE);
		
		$xform->setObjectparams("form_action", "javascript:pz_loadFormPage('addresses_list','addresses_search_form','".pz::url('screen','addresses',$p["function"],array("mode"=>'list'))."')");
		$xform->setObjectparams("form_id", "addresses_search_form");
		$xform->setValueField('objparams',array('fragment', 'pz_screen_xform', 'runtime'));
		$xform->setValueField("text",array("search_name",rex_i18n::msg("project_name")));
		$xform->setValueField("submit",array('submit',rex_i18n::msg('search'), '', 'search'));
		$return .= $xform->getForm();
		
		$return = '<div id="addresses_search" class="design1col xform-search">'.$return.'</div>';
		return $return;

	}

	static function getAddForm($p = array())
	{

		$header = '
	        <header>
	          <div class="header">
	            <h1 class="hl1">'.rex_i18n::msg("add_address").'</h1>
	          </div>
	        </header>';
	
		$xform = new rex_xform;
		// $xform->setDebug(TRUE);
		$xform->setObjectparams("real_field_names",TRUE);
		$xform->setObjectparams("form_name",'pz_address_add_form');
		$xform->setObjectparams("main_table",'pz_address');
		$xform->setObjectparams("form_action", "javascript:pz_loadFormPage('address_add','address_add_form','".pz::url('screen','addresses',$p["function"],array("mode"=>'add_address'))."')");
		$xform->setObjectparams("form_id", "address_add_form");
		$xform->setValueField('objparams',array('fragment', 'pz_screen_xform'));
		
		$xform->setValueField("pz_address_image_screen",array("photo",rex_i18n::msg("photo"),pz_address::getDefaultImage()));
		$xform->setValueField("pz_recommend_text",array("prefix",rex_i18n::msg("address_prefix"),'options' => rex_i18n::msg('address_suffix_labels') ));
		$xform->setValueField("text",array("name",rex_i18n::msg("address_name")));
		$xform->setValueField("text",array("firstname",rex_i18n::msg("address_firstname")));
		$xform->setValueField("text",array("suffix",rex_i18n::msg("address_suffix")));

		/*
			_ TODO
			additional_names
			nickname
			birthname
		*/

		$xform->setValueField("textarea",array("company",rex_i18n::msg("address_company")));
		$xform->setValueField("checkbox",array("is_company",rex_i18n::msg("address_is_company")));
		$xform->setValueField("text",array("title",rex_i18n::msg("address_title")));
		$xform->setValueField("text",array("department",rex_i18n::msg("address_department")));
		$xform->setValueField("date",array("birthday",rex_i18n::msg("address_birthday"),"","","","","","",rex_i18n::msg("error_address_enter_birthday")));
		$xform->setValueField('pz_select_screen',array('created_user_id', rex_i18n::msg('user'), pz::getUsersAsString(),"",pz::getUser()->getId(),1,rex_i18n::msg("please_choose")));

		$xform->setValueField("pz_address_fields",array("fields"));
		$xform->setValueField("textarea",array("note",rex_i18n::msg("address_note")));
		$xform->setValueField("stamp",array("created","created","mysql_datetime","0","1","","","",""));
		$xform->setValueField("stamp",array("updated","updated","mysql_datetime","0","0","","","",""));
		$xform->setValueField("hidden",array("updated_user_id",pz::getUser()->getId()));
		$xform->setValidateField("empty",array("name",rex_i18n::msg("error_address_enter_name")));

		$xform->setActionField("db",array());

		$return = $xform->getForm();
		
		if($xform->getObjectparams("actions_executed")) {
			
			$address_id = $xform->getObjectparams("main_id");
			if($address = pz_address::get($address_id)) {
				$address->create();
				// $return = $header.'<p class="xform-info">'.rex_i18n::msg("address_added").'</p>';
				$return .= pz_screen::getJSUpdateLayer('addresses_list',pz::url('screen','addresses',$p["function"],array("mode"=>'list')));
				
				$r = new pz_address_screen($address);
				$return .= $r->getEditForm($p);
				
			}else
			{
				$return = $header.'<p class="xform-warning">'.rex_i18n::msg("error_address_added_failed").'</p>';
			}
		}else
		{
			$return = $header.$return;	
		}
		$return = '<div id="address_form"><div id="address_add" class="design1col xform-add">'.$return.'</div></div>';

		return $return;	
	
	}
	
	
	function getDeleteForm($p = array())
	{
		$header = '
	        <header>
	          <div class="header">
	            <h1 class="hl1">'.rex_i18n::msg("delete_address").'</h1>
	          </div>
	        </header>';

		$fullname = $this->address->getFullName();
		
		$return = $header.'<p class="xform-info">'.rex_i18n::msg("address_deleted", htmlspecialchars($fullname)).'</p>';
		$return .= pz_screen::getJSLoadFormPage('addresses_list','addresses_search_form',pz::url('screen','addresses',$p["function"],array("mode"=>'list')));
		$return = '<div id="address_form"><div id="address_delete" class="design1col xform-delete">'.$return.'</div></div>';

		return $return;
	}
	
	
	function getEditForm($p = array())
	{

		$header = '
	        <header>
	          <div class="header">
	            <h1 class="hl1">'.rex_i18n::msg("edit_address").'</h1>
	          </div>
	        </header>';

		$xform = new rex_xform;
		// $xform->setDebug(TRUE);
		$xform->setObjectparams("real_field_names",TRUE);
		$xform->setObjectparams("main_table",'pz_address');
		$xform->setObjectparams("main_id",$this->address->getId());
		$xform->setObjectparams("main_where",'id='.$this->address->getId());
		$xform->setObjectparams('getdata',true);
		$xform->setHiddenField("address_id",$this->address->getId());
		$xform->setObjectparams("form_action", "javascript:pz_loadFormPage('address_edit','address_edit_form','".pz::url('screen','addresses',$p["function"],array("mode"=>'edit_address'))."')");
		$xform->setObjectparams("form_id", "address_edit_form");
		$xform->setObjectparams('form_showformafterupdate',1);
				
		$xform->setValueField('objparams',array('fragment', 'pz_screen_xform'));
		
		$xform->setValueField("pz_address_image_screen",array("photo",rex_i18n::msg("photo"),pz_address::getDefaultImage()));
		
		$xform->setValueField("pz_recommend_text",array("prefix",rex_i18n::msg("address_prefix"),'options' => rex_i18n::msg('address_suffix_labels')));
		$xform->setValueField("text",array("name",rex_i18n::msg("address_name")));
		$xform->setValueField("text",array("firstname",rex_i18n::msg("address_firstname")));
		$xform->setValueField("text",array("suffix",rex_i18n::msg("address_suffix")));
		$xform->setValueField("textarea",array("company",rex_i18n::msg("address_company")));
		
		$xform->setValueField("checkbox",array("is_company",rex_i18n::msg("address_is_company")));
		$xform->setValueField("text",array("title",rex_i18n::msg("address_title")));
		$xform->setValueField("text",array("department",rex_i18n::msg("address_department")));
		$xform->setValueField("date",array("birthday",rex_i18n::msg("address_birthday"),"","","","","","",rex_i18n::msg("error_address_enter_birthday")));
		$xform->setValueField('pz_select_screen',array('created_user_id', rex_i18n::msg('creator'), pz::getUsersAsString(),"",pz::getUser()->getId(),1,rex_i18n::msg("please_choose")));
		$xform->setValueField("pz_address_fields",array("fields"));
		$xform->setValueField("textarea",array("note",rex_i18n::msg("address_note")));
		$xform->setValueField("stamp",array("created","created","mysql_datetime","0","1","","","",""));
		$xform->setValueField("stamp",array("updated","updated","mysql_datetime","0","0","","","",""));
		$xform->setValueField("hidden",array("created_user_id",pz::getUser()->getId()));
		$xform->setValueField("hidden",array("updated_user_id",pz::getUser()->getId()));

		$xform->setValidateField("empty",array("name",rex_i18n::msg("error_address_enter_name")));

		$xform->setActionField("db",array('pz_address','id='.$this->address->getId()));

		$return = $xform->getForm();
		
		if($xform->getObjectparams("actions_executed")) {
		
			$this->address = pz_address::get($this->address->getId());
			$this->address->update();

			$return = $header.'<p class="xform-info">'.rex_i18n::msg("address_updated").'</p>'.$return;
			$return .= pz_screen::getJSLoadFormPage('addresses_list','addresses_search_form',pz::url('screen','addresses',$p["function"],array("mode"=>'list')));
			// $return .= pz_screen::getJSUpdateLayer('addresses_list',pz::url('screen','addresses',$p["function"],array("mode"=>'list')));
		}else
		{
			$return = $header.$return;

			
		}
		
		$delete_link = pz::url("screen","addresses",$p["function"],array("address_id"=>$this->address->getId(),"mode"=>"delete_address"));

		$return .= '<div class="xform">
				<p><a class="bt17" onclick="check = confirm(\''.rex_i18n::msg("address_confirm_delete",htmlspecialchars($this->address->getFullName())).'\'); if (check == true) pz_loadPage(\'address_form\',\''.$delete_link.'\')" href="javascript:void(0);">- '.rex_i18n::msg("delete_address").'</a></p>
				</div>';

		$return = '<div id="address_form"><div id="address_edit" class="design1col xform-edit">'.$return.'</div></div>';

		return $return;	
	
	}
	
	


}