<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_ra_tools
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div class="clusters-admin-list">
    <h1><?php echo Text::_('Clusters'); ?></h1>
    <div class="btn-toolbar mb-3">
        <a class="btn btn-success" href="<?php echo Route::_('index.php?option=com_ra_tools&task=cluster.add'); ?>">
            New
        </a>
    </div>
    <form action="<?php echo Route::_('index.php?option=com_ra_tools&view=clusters'); ?>" method="post" name="adminForm" id="adminForm">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo HTMLHelper::_('grid.sort', 'Name', 'name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort', 'Code', 'code', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort', 'Website', 'website', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort', 'Contact', 'contact_name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                    <th><?php echo Text::_('JACTIONS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($this->items)) : ?>
                    <tr><td colspan="5">No records found.</td></tr>
                <?php else : ?>
                    <?php foreach ($this->items as $item) : ?>
                        <tr>
                            <td><?php echo $this->escape($item->name); ?></td>
                            <td><a href="<?php echo Route::_('index.php?option=com_ra_tools&view=cluster&layout=edit&id=' . (int) $item->id); ?>"><?php echo $this->escape($item->code); ?></a></td>
                            <td>
                                <?php if (!empty(trim($item->website))) : ?>
                                    <a href="<?php echo $this->escape($item->website); ?>" target="_blank" rel="noopener noreferrer"><?php echo $this->escape($item->website); ?></a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $this->escape($item->contact_name); ?></td>
                            <td>
                                <a class="btn btn-primary btn-sm" href="<?php echo Route::_('index.php?option=com_ra_tools&view=cluster&layout=edit&id=' . (int) $item->id); ?>">Edit</a>
                                <a class="btn btn-danger btn-sm" href="<?php echo Route::_('index.php?option=com_ra_tools&task=cluster.delete&id=' . (int) $item->id); ?>" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php echo $this->pagination->getListFooter(); ?>
    </form>
</div>
