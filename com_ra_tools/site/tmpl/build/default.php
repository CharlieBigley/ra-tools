<?php
/**
 * @version    5.3.2
 * @package    com_ra_tools
 * @author     Charlie Bigley <webmaster@bigley.me.uk>
 * @copyright  2024 Charlie Bigley
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * 24/01/26 Created by copilot
 * 19/09/26 CB corrected build logic
 */
/**
 * Build Form Template
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>

<div class="build-form front-end-form">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        </div>
    <?php endif; ?>

    <form id="buildForm" name="buildForm" method="post" action="<?php echo Route::_('index.php?option=com_ra_tools&task=build.save'); ?>" class="form-validate form-horizontal">
        
        <?php echo $this->form->renderField('component'); ?>
        <?php echo $this->form->renderField('version'); ?>

        <div class="control-group">
            <div class="controls">
                <button type="submit" class="validate btn btn-primary">
                    <span class="fas fa-check" aria-hidden="true"></span>
                    <?php echo Text::_('JSUBMIT'); ?>
                </button>
                <a class="btn btn-danger" href="<?php echo Route::_('index.php'); ?>">
                    <span class="fas fa-times" aria-hidden="true"></span>
                    <?php echo Text::_('JCANCEL'); ?>
                </a>
            </div>
        </div>

        <input type="hidden" name="option" value="com_ra_tools" />
        <input type="hidden" name="task" value="build.save" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
