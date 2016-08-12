<?php

/* Copyright (c) 2016, Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see docs/LICENSE */

require_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

/**
 * Creates a submenu for the Cockpit of the GEV.
 */
class ilGEVCockpitUIHookGUI extends ilUIHookPluginGUI {
	/**
	 * @inheritdoc
	 */
	function getHTML($a_comp, $a_part, $a_par = array()) {
		if ( 	$a_part != "template_get"
			|| 	$a_par["tpl_id"] != "Services/MainMenu/tpl.main_menu.html"
			|| 	!$this->isCockpit()
		   ) {
			return parent::getHTML($a_comp, $a_part, $a_par);
		}

		$this->active = "Bildungsbiografie";
		$this->items = array
			( "Buchungen"
			, "Bildungsbiografie"
			, "Profil"
			, "TEP"
			, "Trainingseinsätze"
			, "Trainingsverwaltung"
			);

		$current_skin = ilStyleDefinition::getCurrentSkin();

		$this->addCss($current_skin);
		$html = $this->getSubMenuHTML($current_skin);

		return array
			( "mode" => ilUIHookPluginGUI::APPEND
			, "html" => $html
			);
	}

	protected function isCockpit() {
		return $_GET["baseClass"] == "gevDesktopGUI"
			&& $_GET["cmdClass"] != "gevcoursesearchgui"
			&& $_GET["cmdClass"] != "iladminsearchgui"
			;
	}

	protected function getSubMenuHTML($current_skin) {
		assert('is_string($current_skin)');
		$tpl = $this->getTemplate($current_skin, true, true); 
		$count = 1;
		foreach ($this->items as $item) {
			if ($this->active == $item) {
				$tpl->touchBlock("active");
			}
			$tpl->setCurrentBlock("item");
			$tpl->setVariable("NUM", $count);
			$tpl->setVariable("LABEL", $item);
			$tpl->parseCurrentBlock();
			$count++;
		}
		return $tpl->get();
	}

	protected function addCss($current_skin) {
		assert('is_string($current_skin)');
		global $tpl;
		$tpl->addCss($this->plugin_object->getStyleSheetLocation("submenu.css"));
	}

	protected function getTemplate($current_skin, $remove_unknown_vars, $remove_empty_blocks) {
		assert('is_string($current_skin)');
		$skin_folder = $this->getSkinFolder($current_skin);
		$tpl_file = "tpl.submenu.html";
		$tpl_path = "$skin_folder/Plugins/GEVCockpit/$tpl_file";
		if (is_file($tpl_path)) {
			return new ilTemplate($tpl_path, $remove_unknown_vars, $remove_empty_blocks);
		}
		else {
			return $this->plugin_object->getTemplate("tpl.submenu.html", $remove_unknown_vars, $remove_empty_blocks);
		}
	}

	protected function getStyleSheetLocation($current_skin) {
		assert('is_string($current_skin)');
	}

	protected function getSkinFolder($current_skin) {
		assert('is_string($current_skin)');
		return "./Customizing/global/skin/$current_skin";
	}
}
