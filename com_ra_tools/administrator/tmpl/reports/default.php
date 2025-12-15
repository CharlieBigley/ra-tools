<?php
/**
 * @version     3.3.14
 * @package     com_ra_tools
 * @copyright   Copyright (C) 2020. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Charlie <webmaster@bigley.me.uk> - https://www.stokeandnewcastleramblers.org.uk
 * 01/12/22 CB created from com ramblers
 * 07/12/22 CB analyse Joomla users by their allocated security group
 * 12/12/22 CB showPaths
 * 19/12/22 CB add WF reports from site reports
 * 06/02/23 CB mailman report
 * 23/06/23 CB remove mailman reports again
 * 06/09/23 CB showLogfile
 * 18/08/23 CB areasLatitude
 * 22/01/24 CB contactsByCategory
 * 21/04/25 CB Show Events from WalksManager, User by Registration date
 * 24/04/25 CB showLogfileByDate
 * 01/05/25 CB show logfiles view
 * 18/05/25 CB duplicateName
 * 08/07/25 CB breadcrumbs
 * 15/07/25 CB show emails
 * 21/07/25 CB refer to Home Dashboard in breadcrumbs
 */
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Ramblers\Component\Ra_tools\Site\Helpers\ToolsHelper;
use Ramblers\Component\Ra_tools\Site\Helpers\ToolsTable;

$toolsHelper = new ToolsHelper;
$objTable = new ToolsTable();
ToolBarHelper::title('System reports');

// Import CSS
$this->wa = $this->document->getWebAssetManager();
$this->wa->registerAndUseStyle('ramblers', 'com_ra_tools/ramblers.css');

echo '<h2>System reports</h2>';
$breadcrumbs = $toolsHelper->buildLink('administrator/index.php', 'Home Dashboard');
$breadcrumbs .= '>' . $toolsHelper->buildLink('administrator/index.php?option=com_ra_tools&view=dashboard', 'RA Dashboard');
echo $breadcrumbs;
//echo __file__ . '<br>';
//var_dump($this->params);
?>

<form action="<?php echo Route::_('index.php?option=com_ra_tools&view=reports'); ?>" method="post" name="reportsForm" id="reportsForm">
    <div id="j-main-container" class="span10">
        <div class="clearfix"> </div>
        <?php
        //$mode = $this->escape($this->state->get('list.ordering'));
        //$listDirn = $this->escape($this->state->get('list.direction'));
        $objTable->width = 30;
        $objTable->add_header('Report,Action', 'grey');

        $objTable->add_item("List emails");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&view=emails", "Go", False, 'red'));
        $objTable->generate_line();

        $objTable->add_item("Groups by bespoke description");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.showBespoke", "Go", False, 'red'));
        $objTable->add_item("");
        $objTable->generate_line();

//        $objTable->add_item("Show Paths");
//        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.showPaths", "Go", False, 'red'));
//        $objTable->add_item("");
//        $objTable->generate_line();

        $objTable->add_item("Contact By Category");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.contactsByCategory", "Go", False, 'red'));
        $objTable->generate_line();

        $objTable->add_item("Extract contacts");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.extractContacts", "Go", False, 'red'));
        $objTable->generate_line();

        $objTable->add_item("Users with duplicate name");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.duplicateName", "Go", False, 'red'));
        $objTable->generate_line();

        $objTable->add_item("Count users by Registration date");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.showRegistrations", "Go", False, 'red'));
        $objTable->generate_line();

        $objTable->add_item("Joomla User by Group");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.showJoomlaUsersByGroup", "Go", False, 'red'));
        $objTable->generate_line();

        $objTable->add_item("Extensions and versions");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.showExtensions", "Go", False, 'red'));
        $objTable->generate_line();

        $objTable->add_item("Schema");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.showSchema", "Go", False, 'red'));
        $objTable->generate_line();

        $objTable->add_item("Areas by latitude");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.areasLatitude", "Go", False, 'red'));
        $objTable->generate_line();

        if (ComponentHelper::isEnabled('com_ra_mailman', true) OR (ComponentHelper::isEnabled('com_ra_walks', true))) {
            $objTable->add_item("Show Logfile by month");
            $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.showLogfileByMonth", "Go", False, 'red'));
            $objTable->generate_line();

            $objTable->add_item("Search all Logfile records");
            $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&view=logfiles", "Go", False, 'red'));
            $objTable->generate_line();

            $objTable->add_item("Show recent Logfile records");
            $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.showLogfile", "Go", False, 'red'));
            $objTable->generate_line();
        }

        if (ComponentHelper::isEnabled('com_ra_sg', true)) {
            $objTable->add_item("Summarise Self Guided walks");
            $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.summariseGuided", "Go", False, 'red'));
            $objTable->generate_line();
        }

        $objTable->add_item("Colours");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.showColours", "Go", False, 'red'));
        $objTable->generate_line();

        $objTable->add_item("Show Events from JSON feed, by Area");
        $objTable->add_item($toolsHelper->buildButton('administrator/index.php?option=com_ra_tools&task=reports.showEvents', 'Go', False, 'red'));
        $objTable->generate_line();

        $objTable->add_item("Show Ramblers menus");
        $objTable->add_item($toolsHelper->buildButton("administrator/index.php?option=com_ra_tools&task=reports.showMenus", "Go", False, 'red'));
        $objTable->add_item("");
        $objTable->generate_line();

        $objTable->generate_table();
        $target = 'administrator/index.php?option=com_ra_tools&view=dashboard';
        echo $toolsHelper->backButton($target);
        ?>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</div>
</form>
<?php
echo "<!-- End of code from ' . __file . ' -->" . PHP_EOL;
