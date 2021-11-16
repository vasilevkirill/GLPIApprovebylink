<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2011 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

class PluginApprovebylinkConfig extends CommonDBTM {

   static protected $notable = true;

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

      if (!$withtemplate) {
         if ($item->getType() == 'Config') {
            return __('Example plugin');
         }
      }
      return '';
   }

   static function configUpdate($input) {

       Session::checkRight("config", UPDATE);
       Plugin::load('approvebylink');
       $cn = new Config();
       $cur=$cn->getConfigurationValues('plugin:approvebylink');
       if ($input['salt1']!="" and $cur['salt1']!=$input['salt1']){

           $cn->setConfigurationValues('plugin:approvebylink', ['salt1' => $input['salt1']]);
       }
       if ($input['salt2']!="" and $cur['salt2']!=$input['salt2']){
           $cn->setConfigurationValues('plugin:approvebylink', ['salt2' => $input['salt1']]);
       }
       if  ($input['onlyOwnerApproved']!=$cur['onlyOwnerApproved']){
           $cn->setConfigurationValues('plugin:approvebylink', ['onlyOwnerApproved' => $input['onlyOwnerApproved']]);
       }

      return $input;
   }

   function showFormExample() {
      global $CFG_GLPI;

      if (!Session::haveRight("config", UPDATE)) {
         return false;
      }
      $config = Config::getConfigurationValues('plugin:approvebylink');
      echo "<form name='form' action=\"".Toolbox::getItemTypeFormURL('Config')."\" method='post'>";
      echo "<div class='center' id='tabsbody'>";
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr><th colspan='4'>" . __('Approve By Link setup config') . "</th></tr>";
      echo "<tr><td colspan='4'>" . __('Do not change the salt values, links that have already been sent will stop working.' ) . "</td></tr>";
      echo "<td >" . __('Соль #1 : ')."</td>";
      echo "<td colspan='3'><textarea rows='3' cols='90' name='salt1'>".$config['salt1']."</textarea></td></tr>";
      echo "<td >" . __('Соль #2 : ')."</td>";
      echo "<td colspan='3'><textarea rows='3' cols='90' name='salt2'>".$config['salt2']."</textarea></td></tr>";
      //echo "<td >" . __('Только инициатор может подтвердить выполнение? :') . "</td>";
      //echo "<td colspan='3'>";
      echo "<input type='hidden' name='config_class' value='".__CLASS__."'>";
      echo "<input type='hidden' name='config_context' value='plugin:approvebylink'>";
      // Dropdown::showYesNo("onlyOwnerApproved", $config['onlyOwnerApproved']);
      //echo "</td></tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<td colspan='4' class='center'>";
      echo "<input type='submit' name='update' class='submit' value=\""._sx('button', 'Save')."\">";
      echo "</td></tr>";

      echo "</table></div>";
      Html::closeForm();
   }

   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {

      if ($item->getType() == 'Config') {
         $config = new self();
         $config->showFormExample();
      }
   }

}
