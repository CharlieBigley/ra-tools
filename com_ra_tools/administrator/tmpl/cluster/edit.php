
<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_ra_tools
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// Load Joomla core scripts for admin form behavior
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
        ->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
echo __FILE__ . '<br:>';
?>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
//    alert('Joomla.submitbutton called with task: ' + task);
    if (task == 'cluster.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
        Joomla.submitform(task, document.getElementById('adminForm'));
    }
}
</script>
<form action="<?php echo Route::_('index.php?option=com_ra_tools&task=cluster.save&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div style="margin-bottom: 1em;">
        <button type="button" class="button-save btn btn-success" onclick="Joomla.submitbutton('cluster.save')">Save</button>
        <button type="button" class="button-cancel btn btn-danger" onclick="Joomla.submitbutton('cluster.cancel')">Cancel</button>
        <button type="submit" class="btn btn-primary">Direct Submit</button>
    </div>
    <div class="form-horizontal">
        <?php echo $this->form->renderFieldset('basic'); ?>
    </div>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
