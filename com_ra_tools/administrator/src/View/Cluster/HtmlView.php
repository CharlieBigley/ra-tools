<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_ra_tools
 */

namespace Ramblers\Component\Ra_tools\Administrator\View\Cluster;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;
    protected $state;

    public function display($tpl = null)
    {
        echo __FILE__ . '<br:>';
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        $this->state = $this->get('State');

        // Add Joomla admin toolbar buttons
        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $user = \Joomla\CMS\Factory::getUser();
        $canDo = \Joomla\CMS\Helper\ContentHelper::getActions('com_ra_tools', 'component');

        \Joomla\CMS\Toolbar\ToolbarHelper::title(\Joomla\CMS\Language\Text::_('Cluster'), 'generic.png');

        if ($canDo->get('core.edit') || ($this->item->id == 0 && $canDo->get('core.create'))) {
            \Joomla\CMS\Toolbar\ToolbarHelper::apply('cluster.apply');
            \Joomla\CMS\Toolbar\ToolbarHelper::save('cluster.save');
        }
        \Joomla\CMS\Toolbar\ToolbarHelper::cancel('cluster.cancel', 'JCANCEL');
    }
}
