<?php

/**
 * @version     3.5.0
 * @package     com_ra_tools
 * @copyright   Copyleft (C) 2021
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Charlie <webmaster@bigley.me.uk> - https://www.stokeandnewcastleramblers.org.uk

 * 12/03/25 CB Add Events / Bookings
 * 16/03/25 CB add Events / Users
 * 25/03/25 CB add events / dataload
 * 06/04/25 CB Events / Eventtypes
 * 13/04/25 CB list Users
 * 21/04/25 CB use JsonHelper to show feed
 * 01/05/25 CB check canDo->create for showing WalksRefresh
 * 14/06/25 CB add option for Events . apisites
 * 25/08/25 show title from View
 * 28/08/25 CB use apisites from com_ra_tools, not com_ra_events
 * 09/09/25 CB remove diagnostics for events menu
 * 14/11/25 CB Mailman recipients
 * 02/02/26 CB add Clusters
 * 04/02/26 CB Add RA Develop section
 * 11/02/26 CB Restructure with grid layout and permission-based blocks
 */
// No direct access
\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Ramblers\Component\Ra_events\Site\Helpers\EventsHelper;
use Ramblers\Component\Ra_tools\Site\Helpers\JsonHelper;
use Ramblers\Component\Ra_tools\Site\Helpers\ToolsHelper;

$jsonHelper = new JsonHelper;
$toolsHelper = new ToolsHelper;

$wa = $this->document->getWebAssetManager();
$wa->registerAndUseStyle('ramblers', 'com_ra_tools/ramblers.css');
$wa->registerAndUseStyle('dashboard', 'com_ra_tools/dashboard.css');

$component = ComponentHelper::getComponent('com_ra_tools');
$canDo = ContentHelper::getActions('com_ra_tools');

// Build blocks array
$blocks = [];

// ========== SYSTEM TOOLS BLOCK (SUPERUSER ONLY) ==========
if ($toolsHelper->isSuperuser()) {
    $sysToolsItems = [
        ['label' => 'Show your access permissions', 'url' => 'index.php?option=com_ra_tools&task=system.showAccess'],
    ];
    
    if ((ComponentHelper::isEnabled('com_ra_events', true)) || (ComponentHelper::isEnabled('com_ra_mailman', true))) {
        $sysToolsItems[] = ['label' => 'List Users', 'url' => 'index.php?option=com_ra_tools&view=users'];
    }
    
    $sysToolsItems[] = ['label' => 'API sites', 'url' => 'index.php?option=com_ra_tools&view=apisites'];
    $sysToolsItems[] = ['label' => 'Access Configuration Wizard', 'url' => 'index.php?option=com_ra_tools&task=system.AccessWizard'];
    $sysToolsItems[] = ['label' => 'System Reports', 'url' => 'index.php?option=com_ra_tools&view=reports'];
    
    $target = $jsonHelper->setUrl('organisation', '');
    $sysToolsItems[] = ['label' => 'Show Organisation feed', 'url' => $target, 'target' => '_blank'];
    
    $sysToolsItems[] = ['label' => 'Refresh details of Areas', 'url' => 'index.php?option=com_ra_tools&task=area_list.refreshAreas'];
    $sysToolsItems[] = ['label' => 'Refresh details of Groups', 'url' => 'index.php?option=com_ra_tools&task=group_list.refreshGroups'];
    
    if ($canDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_tools');
        $sysToolsItems[] = ['label' => 'Configure com_ra_tools (v' . $versions->component . ')', 'url' => 'index.php?option=com_config&view=component&component=com_ra_tools'];
        $sysToolsItems[] = ['label' => 'DB version: ' . $versions->db_version, 'url' => '#', 'disabled' => true];
    }
    
    $blocks[] = [
        'title' => 'System Tools',
        'items' => $sysToolsItems
    ];
}




// ========== MAIL MANAGER BLOCK ==========
if (ComponentHelper::isEnabled('com_ra_mailman', true)) {
    $mailmanCanDo = ContentHelper::getActions('com_ra_mailman');
    
    $mailmanItems = [
        ['label' => 'Mailing lists', 'url' => 'index.php?option=com_ra_mailman&view=mail_lsts'],
        ['label' => 'Mailshots', 'url' => 'index.php?option=com_ra_mailman&view=mailshots'],
    ];
    
    if ($mailmanCanDo->get('core.create')) {
        $mailmanItems[] = ['label' => 'Subscriptions', 'url' => 'index.php?option=com_ra_mailman&view=subscriptions'];
        $mailmanItems[] = ['label' => 'Recipients', 'url' => 'index.php?option=com_ra_mailman&view=recipients'];
        $mailmanItems[] = ['label' => 'MailMan Users', 'url' => 'index.php?option=com_ra_mailman&view=profiles'];
        $mailmanItems[] = ['label' => 'Import list of members', 'url' => 'index.php?option=com_ra_mailman&view=dataload'];
        $mailmanItems[] = ['label' => 'Import Reports', 'url' => 'index.php?option=com_ra_mailman&view=import_reports'];
        $mailmanItems[] = ['label' => 'Mailman Reports', 'url' => 'index.php?option=com_ra_mailman&view=reports'];
        $mailmanItems[] = ['label' => 'Process Renewals', 'url' => 'index.php?option=com_ra_mailman&task=system.checkRenewals'];
    }
    
    if ($mailmanCanDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_mailman');
        $mailmanItems[] = ['label' => 'Configure com_ra_mailman (v' . $versions->component . ')', 'url' => 'index.php?option=com_config&view=component&component=com_ra_mailman'];
        $mailmanItems[] = ['label' => 'DB version: ' . $versions->db_version, 'url' => '#', 'disabled' => true];
    }
    
    $blocks[] = [
        'title' => 'Mail Manager',
        'items' => $mailmanItems
    ];
}

// ========== ORGANISATION BLOCK ==========
$orgItems = [
    ['label' => 'List of Clusters', 'url' => 'index.php?option=com_ra_tools&view=clusters'],
    ['label' => 'List of Areas', 'url' => 'index.php?option=com_ra_tools&view=area_list'],
    ['label' => 'List of Groups', 'url' => 'index.php?option=com_ra_tools&view=group_list'],
];

$blocks[] = [
    'title' => 'Organisation',
    'items' => $orgItems
];

// ========== PATH MAINTENANCE BLOCK ==========
if (ComponentHelper::isEnabled('com_ra_paths', true)) {
    $pathsCanDo = ContentHelper::getActions('com_ra_paths');
    
    $pathsItems = [
        ['label' => 'Fault Reports', 'url' => 'index.php?option=com_ra_paths&view=faults'],
        ['label' => 'Followups', 'url' => 'index.php?option=com_ra_paths&view=followups'],
    ];
    
    if ($pathsCanDo->get('core.create')) {
        $pathsItems[] = ['label' => 'Categories', 'url' => 'index.php?option=com_ra_paths&view=categories'];
        $pathsItems[] = ['label' => 'Statuses', 'url' => 'index.php?option=com_ra_paths&view=statuses'];
        $pathsItems[] = ['label' => 'Parishes', 'url' => 'index.php?option=com_ra_paths&view=parishes'];
        $pathsItems[] = ['label' => 'Boroughs', 'url' => 'index.php?option=com_ra_paths&view=boroughs'];
        $pathsItems[] = ['label' => 'Regions', 'url' => 'index.php?option=com_ra_paths&view=regions'];
        $pathsItems[] = ['label' => 'Sectors', 'url' => 'index.php?option=com_ra_paths&view=sectors'];
    }
    
    $pathsItems[] = ['label' => 'Enhancements', 'url' => 'index.php?option=com_ra_paths&view=enhancements'];
    
    if ($pathsCanDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_paths');
        $pathsItems[] = ['label' => 'Configure com_ra_paths (v' . $versions->component . ')', 'url' => 'index.php?option=com_config&view=component&component=com_ra_paths'];
        $pathsItems[] = ['label' => 'DB version: ' . $versions->db_version, 'url' => '#', 'disabled' => true];
    }
    
    $blocks[] = [
        'title' => 'Path Maintenance',
        'items' => $pathsItems
    ];
}

echo '</ul>';
if (ComponentHelper::isEnabled('com_ra_mailman', true)) {
    $canDo = ContentHelper::getActions('com_ra_mailman');

    echo '<h3>Mail Manager</h3>';
    echo '<ul>';
    echo '<li><a href="index.php?option=com_ra_mailman&amp;view=mail_lsts" target="_self">Mailing lists</a></li>';
    echo '<li><a href="index.php?option=com_ra_mailman&amp;view=mailshots" target="_self">Mailshots</a></li>';
    if ($canDo->get('core.create')) {
        echo '<li><a href="index.php?option=com_ra_mailman&amp;view=subscriptions" target="_self">Subscriptions</a></li>';
        echo '<li><a href="index.php?option=com_ra_mailman&amp;view=recipients" target="_self">Recipients</a></li>';
        echo '<li><a href="index.php?option=com_ra_mailman&amp;view=profiles" target="_self">MailMan Users</a></li>';
        echo '<li><a href="index.php?option=com_ra_mailman&amp;view=dataload" target="_self">Import list of members</a></li>';
        echo '<li><a href="index.php?option=com_ra_mailman&amp;view=import_reports" target="_self">Import Reports</a></li>';
        echo '<li><a href="index.php?option=com_ra_mailman&amp;view=reports" target="_self">Mailman Reports</a></li>';
        echo '<li><a href="index.php?option=com_ra_mailman&task=system.checkRenewals" target="_self">Process Renewals</a></li>' . PHP_EOL;
        //         echo '<li><a href="index.php?option=com_ra_mailman&task=profiles.purgeTestdata" target="_self">Purge test data</a></li>' . PHP_EOL;
    }

    if ($canDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_mailman');
        echo '<li><a href="index.php?option=com_config&view=component&component=com_ra_mailman" target="_self">';
        echo "Configure com_ra_mailman (version " . $versions->component . ")</a></li>" . PHP_EOL;
        echo '<li>(DB version is ' . $versions->db_version . ')</li>';
    }
    echo '</ul>' . PHP_EOL;
}


echo '<h3>Organisation</h3>' . PHP_EOL;
echo '<ul>';
echo '<li><a href="index.php?option=com_ra_tools&amp;view=clusters" target="_self">List of Clusters</a></li>' . PHP_EOL;
echo '<li><a href="index.php?option=com_ra_tools&amp;view=area_list" target="_self">List of Areas</a></li>' . PHP_EOL;
echo '<li><a href="index.php?option=com_ra_tools&amp;view=group_list" target="_self">List of Groups</a></li>';
echo '</ul>' . PHP_EOL;
//echo '</div>' . PHP_EOL;


if (ComponentHelper::isEnabled('com_ra_paths', true)) {
    $canDo = ContentHelper::getActions('com_ra_paths');

    echo '<h3>Path Maintenance</h3>';
    echo '<ul>';
    echo '<li><a href="index.php?option=com_ra_paths&amp;view=faults" target="_self">Fault Reports</a></li>';
    echo '<li><a href="index.php?option=com_ra_paths&amp;view=followups" target="_self">Followups</a></li>';
    if ($canDo->get('core.create')) {
        echo '<li><a href="index.php?option=com_ra_paths&amp;view=categories" target="_self">Categories</a></li>';
        echo '<li><a href="index.php?option=com_ra_paths&amp;view=statuses" target="_self">Statuses</a></li>';
        echo '<li><a href="index.php?option=com_ra_paths&amp;view=parishes" target="_self">Parishes</a></li>';
        echo '<li><a href="index.php?option=com_ra_paths&amp;view=boroughs" target="_self">Boroughs</a></li>';
        echo '<li><a href="index.php?option=com_ra_paths&amp;view=regions" target="_self">Regions</a></li>';
        echo '<li><a href="index.php?option=com_ra_paths&amp;view=sectors" target="_self">Sectors</a></li>';
    }
    echo '<li><a href="index.php?option=com_ra_paths&amp;view=enhancements" target="_self">Enhancements</a></li>';
    if ($canDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_paths');
        echo '<li><a href="index.php?option=com_config&view=component&component=com_ra_paths" target="_self">';
        echo "Configure com_ra_paths (version " . $versions->component . ")</a></li>" . PHP_EOL;
        echo '<li>(DB version is ' . $versions->db_version . ')</li>';
    }
    echo '</ul>' . PHP_EOL;
}



// ========== EVENTS BLOCK ==========
if (ComponentHelper::isEnabled('com_ra_events', true)) {
    $versions = $toolsHelper->getVersions('com_ra_events');
    
    // For newer versions, use EventsHelper
    if (version_compare($versions->component, '2.1.1', 'ge')) {
        $eventsHelper = new EventsHelper;
        // EventsHelper handles its own block addition
        $eventsHelper->menusDashboard();
    } else {
        // Fallback for older versions
        $eventsCanDo = ContentHelper::getActions('com_ra_events');
        
        $eventsItems = [
            ['label' => 'List of Events', 'url' => 'index.php?option=com_ra_events&view=events'],
        ];
        
        if ($eventsCanDo->get('core.create')) {
            $eventsItems[] = ['label' => 'List of Bookings', 'url' => 'index.php?option=com_ra_events&view=bookings'];
            $eventsItems[] = ['label' => 'Event Reports', 'url' => 'index.php?option=com_ra_events&view=reports'];
            $eventsItems[] = ['label' => 'Import list of bookings', 'url' => 'index.php?option=com_ra_events&view=dataload'];
            $eventsItems[] = ['label' => 'API Sites', 'url' => 'index.php?option=com_ra_tools&view=apisites'];
        }
        
        if ($toolsHelper->isSuperuser()) {
            $eventsItems[] = ['label' => 'Event Types', 'url' => 'index.php?option=com_ra_events&view=eventtypes'];
        }
        
        if ($eventsCanDo->get('core.admin')) {
            $versions = $toolsHelper->getVersions('com_ra_events');
            $eventsItems[] = ['label' => 'Configure com_ra_events (v' . $versions->component . ')', 'url' => 'index.php?option=com_config&view=component&component=com_ra_events'];
            $eventsItems[] = ['label' => 'DB version: ' . $versions->db_version, 'url' => '#', 'disabled' => true];
        }
        
        $blocks[] = [
            'title' => 'Events',
            'items' => $eventsItems
        ];
    }
}

// ========== WALKS BLOCK ==========
if (ComponentHelper::isEnabled('com_ra_walks', true)) {
    $walksCanDo = ContentHelper::getActions('com_ra_walks');
    
    $walksItems = [
        ['label' => 'List of Walks', 'url' => 'index.php?option=com_ra_walks&view=walks'],
        ['label' => 'Walk Reports', 'url' => 'index.php?option=com_ra_walks&view=reports'],
    ];
    
    // Check if walks refresh is available
    if (ComponentHelper::isEnabled('com_ra_wf', true)) {
        if (ContentHelper::getActions('com_ra_wf')->get('core.create')) {
            $walksItems[] = ['label' => 'Refresh details of Walks', 'url' => 'index.php?option=com_ra_wf&task=walks.refresh'];
        }
    } else {
        if ($walksCanDo->get('core.create')) {
            $walksItems[] = ['label' => 'Refresh details of Walks', 'url' => 'index.php?option=com_ra_walks&task=walks.refresh'];
        }
    }
    
    if ($walksCanDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_walks');
        $walksItems[] = ['label' => 'Configure com_ra_walks (v' . $versions->component . ')', 'url' => 'index.php?option=com_config&view=component&component=com_ra_walks'];
        $walksItems[] = ['label' => 'DB version: ' . $versions->db_version, 'url' => '#', 'disabled' => true];
    }
    
    $blocks[] = [
        'title' => 'Walks',
        'items' => $walksItems
    ];
}

// ========== RA DEVELOPMENT BLOCK ==========
if (ComponentHelper::isEnabled('com_ra_develop', true)) {
    $developCanDo = ContentHelper::getActions('com_ra_develop');
    
    $developItems = [
        ['label' => 'Summary of extensions', 'url' => 'index.php?option=com_ra_develop&task=extensions.listExtensions'],
        ['label' => 'Builds', 'url' => 'index.php?option=com_ra_develop&view=builds'],
        ['label' => 'Sub Systems', 'url' => 'index.php?option=com_ra_develop&view=subsystems'],
        ['label' => 'Extension Types', 'url' => 'index.php?option=com_ra_develop&view=extension_types'],
        ['label' => 'Extensions', 'url' => 'index.php?option=com_ra_develop&view=extensions'],
    ];
    
    if ($developCanDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_develop');
        $developItems[] = ['label' => 'Configure com_ra_develop (v' . $versions->component . ')', 'url' => 'index.php?option=com_config&view=component&component=com_ra_develop'];
        $developItems[] = ['label' => 'DB version: ' . $versions->db_version, 'url' => '#', 'disabled' => true];
    }
    
    $blocks[] = [
        'title' => 'RA Development',
        'items' => $developItems
    ];
}

// ========== WALKS FOLLOW BLOCK ==========
if (ComponentHelper::isEnabled('com_ra_wf', true)) {
    $wfCanDo = ContentHelper::getActions('com_ra_wf');
    
    $wfItems = [
        ['label' => 'List of Walks to Follow', 'url' => 'index.php?option=com_ra_wf&view=walks'],
        ['label' => 'Walks Follow profiles', 'url' => 'index.php?option=com_ra_wf&view=profiles'],
        ['label' => 'Walks Follow Reports', 'url' => 'index.php?option=com_ra_wf&view=reports'],
    ];
    
    if ($wfCanDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_wf');
        $wfItems[] = ['label' => 'Configure com_ra_wf (v' . $versions->component . ')', 'url' => 'index.php?option=com_config&view=component&component=com_ra_wf'];
        $wfItems[] = ['label' => 'DB version: ' . $versions->db_version, 'url' => '#', 'disabled' => true];
    }
    
    $blocks[] = [
        'title' => 'Walks Follow',
        'items' => $wfItems
    ];
}

// ========== SELF GUIDED WALKS BLOCK ==========
if (ComponentHelper::isEnabled('com_ra_sg', true)) {
    $sgCanDo = ContentHelper::getActions('com_ra_sg');
    
    $sgItems = [
        ['label' => 'Self Guided walks', 'url' => 'index.php?option=com_ra_tools&view=sg_list'],
        ['label' => 'Categories', 'url' => 'index.php?option=com_categories&extension=com_ra_tools'],
    ];
    
    if ($sgCanDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_sg');
        $sgItems[] = ['label' => 'Configure com_ra_sg (v' . $versions->component . ')', 'url' => 'index.php?option=com_config&view=component&component=com_ra_sg'];
        $sgItems[] = ['label' => 'DB version: ' . $versions->db_version, 'url' => '#', 'disabled' => true];
    }
    
    $blocks[] = [
        'title' => 'Self Guided Walks',
        'items' => $sgItems
    ];
}

if (ComponentHelper::isEnabled('com_ra_events', true)) {
//echo '<div style="float: left">';
    $versions = $toolsHelper->getVersions('com_ra_events');

    if (version_compare($versions->component, '2.1.1', 'ge')) {
        $eventsHelper = new EventsHelper;
        $eventsHelper->menusDashboard();
    } else {
        $canDo = ContentHelper::getActions('com_ra_events');
        echo '<h3>Events</h3>' . PHP_EOL;
        echo '<ul>' . PHP_EOL;
        echo '<li><a href="index.php?option=com_ra_events&amp;view=events" target="_self">List of Events</a></li>' . PHP_EOL;
        if ($canDo->get('core.create')) {
            echo '<li><a href="index.php?option=com_ra_events&amp;view=bookings" target="_self">List of Bookings</a></li>' . PHP_EOL;
            echo '<li><a href="index.php?option=com_ra_events&amp;view=reports" target="_self">Event Reports</a></li>' . PHP_EOL;
            echo '<li><a href="index.php?option=com_ra_events&amp;view=dataload" target="_self">Import list of bookings</a></li>' . PHP_EOL;
            echo '<li><a href="index.php?option=com_ra_toolss&amp;view=apisites" target="_self">API Sites</a></li>' . PHP_EOL;
        }
        if ($toolsHelper->isSuperuser()) {
            echo '<li><a href="index.php?option=com_ra_events&amp;view=eventtypes" target="_self">Event Types</a></li>' . PHP_EOL;
        }
        if ($canDo->get('core.admin')) {
            $versions = $toolsHelper->getVersions('com_ra_events');
            echo '<li><a href="index.php?option=com_config&view=component&component=com_ra_events" target="_self">';
            echo "Configure com_ra_events (version " . $versions->component . ")</a></li>" . PHP_EOL;
            echo '<li>(DB version is ' . $versions->db_version . ')</li>';
        }
        echo '</ul>' . PHP_EOL;
    }
//    echo '</div>' . PHP_EOL;
}

if (ComponentHelper::isEnabled('com_ra_walks', true)) {
    $canDo = ContentHelper::getActions('com_ra_walks');
    echo '<h3>Walks</h3>' . PHP_EOL;
    echo '<ul>' . PHP_EOL;
    echo '<li><a href="index.php?option=com_ra_walks&amp;view=walks" target="_self">List of Walks</a></li>' . PHP_EOL;
    echo '<li><a href="index.php?option=com_ra_walks&amp;view=reports" target="_self">Walk Reports</a></li>' . PHP_EOL;

    if (ComponentHelper::isEnabled('com_ra_wf', true)) {
        if (ContentHelper::getActions('com_ra_wf')->get('core.create')) {
            echo ' <li><a href="index.php?option=com_ra_wf&task=walks.refresh" target="_self">Refresh details of Walks</a></li>' . PHP_EOL;
        }
    } else {
        if ($canDo->get('core.create')) {
            echo ' <li><a href="index.php?option=com_ra_walks&task=walks.refresh" target="_self">Refresh details of Walks</a></li>' . PHP_EOL;
        }
    }
    if ($canDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_walks');
        echo '<li><a href="index.php?option=com_config&view=component&component=com_ra_walks" target="_self">';
        echo "Configure com_ra_walks (version " . $versions->component . ")</a></li>" . PHP_EOL;
        echo '<li>(DB version is ' . $versions->db_version . ')</li>';
    }
    echo '</ul>' . PHP_EOL;
}
if (ComponentHelper::isEnabled('com_ra_develop', true)) {
    $canDo = ContentHelper::getActions('com_ra_develop');
    echo '<div style="float: left">';
    echo '<h3>RA Development</h3>';
    echo '<h4>Reports</h4>';
    echo '<ul>';
    echo '<li><a href="index.php?option=com_ra_develop&amp;task=extensions.listExtensions" target="_self">Summary of extensions </a></li>';
    echo '<li><a href="index.php?option=com_ra_develop&amp;view=builds" target="_self">Builds </a></li>';
    echo '</ul>';
    echo '<h4>Maintenance</h4>';
    echo '<ul>';
    echo '<li><a href="index.php?option=com_ra_develop&amp;view=subsystems" target="_self">Sub Systems</a></li>';
    echo '<li><a href="index.php?option=com_ra_develop&amp;view=extension_types" target="_self">Extension Types</a></li>';
    echo '<li><a href="index.php?option=com_ra_develop&amp;view=extensions" target="_self">Extensions</a></li>';
    echo '</ul>';
    if ($canDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_develop');
        echo '<li><a href="index.php?option=com_config&view=component&component=com_ra_develop" target="_self">';
        echo "Configure com_ra_develop (version " . $versions->component . ")</a></li>" . PHP_EOL;
        echo '<li>(DB version is ' . $versions->db_version . ')</li>';
    }
    echo '</ul>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
} 
if (ComponentHelper::isEnabled('com_ra_wf', true)) {
    echo '<h3>Walks Follow</h3>' . PHP_EOL;
    echo '<ul>' . PHP_EOL;
    echo '<li><a href="index.php?option=com_ra_wf&amp;view=walks" target="_self">List of Walks to Follow</a></li>' . PHP_EOL;

    echo '<li><a href="index.php?option=com_ra_wf&amp;view=profiles" target="_self">Walks Follow profiles</a></li>' . PHP_EOL;
    echo '<li><a href="index.php?option=com_ra_wf&amp;view=reports" target="_self">Walks Follow Reports</a></li>' . PHP_EOL;
    if ($canDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_wf');
        echo '<li><a href="index.php?option=com_config&view=component&component=com_ra_wf" target="_self">';
        echo "Configure com_ra_wf (version " . $versions->component . ")</a></li>" . PHP_EOL;
        echo '<li>(DB version is ' . $versions->db_version . ')</li>';
    }
    echo '</ul>' . PHP_EOL;
}

if (ComponentHelper::isEnabled('com_ra_sg', true)) {   // Self Guided
    echo '<h3>Walks</h3>' . PHP_EOL;
    echo '<ul>' . PHP_EOL;
    echo '<li><a href="index.php?option=com_ra_tools&amp;view=sg_list" target="_self">Self Guided walks</a></li>' . PHP_EOL;
    echo '<li><a href="index.php?option=com_categories&amp;extension=com_ra_tools" target - "_self">Categories</a></li>' . PHP_EOL;
    if ($canDo->get('core.admin')) {
        $versions = $toolsHelper->getVersions('com_ra_sg');
        echo '<li><a href="index.php?option=com_config&view=component&component=com_ra_sg" target="_self">';
        echo "Configure com_ra_sg (version " . $versions->component . ")</a></li>" . PHP_EOL;
        echo '<li>(DB version is ' . $versions->db_version . ')</li>';
    }
    echo '</ul>' . PHP_EOL;
}
?>

<!-- Grid-based Dashboard Layout -->
<div class="dashboard-grid">
    <?php foreach ($blocks as $block): ?>
        <div class="dashboard-block">
            <div class="block-header">
                <h3><?php echo $block['title']; ?></h3>
            </div>
            <div class="block-content">
                <ul>
                    <?php foreach ($block['items'] as $item): ?>
                        <li>
                            <?php if (isset($item['disabled']) && $item['disabled']): ?>
                                <span class="item-text"><?php echo $item['label']; ?></span>
                            <?php else: ?>
                                <a href="<?php echo $item['url']; ?>" target="<?php echo $item['target'] ?? '_self'; ?>">
                                    <?php echo $item['label']; ?>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>
</div>


