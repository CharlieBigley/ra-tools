<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_ra_tools
 */

namespace Ramblers\Component\Ra_tools\Administrator\View\Clusters;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;

    public function display($tpl = null)
    {
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');
        parent::display($tpl);
    }
}
