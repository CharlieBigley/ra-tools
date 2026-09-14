<?php

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Ramblers/Ra_tools/Site/Helpers/ToolsHelper;

class PlgSystemRaTools extends CMSPlugin {

    public function onAfterInitialise(): void{
$toolsHelper = new ToolsHelper;
$sql = 'DELETE FROM #__ra_profiles WHERE user_id=' . $id;
$toolsHelper->executeCommand($sql);
    }

}
