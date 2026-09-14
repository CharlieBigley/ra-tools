<?php

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Ramblers\Ra_tools\Site\Helpers\ToolsHelper;

class PlgSystemRaTools extends CMSPlugin {

    /**
     * Handle cascade deletion of related records when a user is deleted
     * 
     * @param   string  $context  The context for the content passed to the plugin
     * @param   object  $user     The user object being deleted
     * 
     * @return  boolean  True to allow deletion, false to prevent it
     */
    public function onUserBeforeDelete($context, $user): bool
    {
        $toolsHelper = new ToolsHelper();
        $userId = $user->id ?? 0;

        if ($userId <= 0) {
            return true;
        }

        try {
            // Delete related records from ra_emails table
            $sqlEmails = 'DELETE FROM #__ra_emails WHERE user_id = ' . (int)$userId;
            $toolsHelper->executeCommand($sqlEmails);

            // Delete related records from ra_profiles table
            $sqlProfiles = 'DELETE FROM #__ra_profiles WHERE user_id = ' . (int)$userId;
            $toolsHelper->executeCommand($sqlProfiles);
        } catch (Exception $e) {
            // Log the error but allow user deletion to continue
            JLog::add('Error deleting user related records: ' . $e->getMessage(), JLog::WARNING, 'com_ra_tools');
        }

        return true;
    }

}
