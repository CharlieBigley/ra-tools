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
namespace Ramblers\Component\Ra_tools\Site\View\Build;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Build View
 * Displays the build form and results
 */
class HtmlView extends BaseHtmlView
{
     protected $items;
    protected $params;
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the
     *                         template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $active = $app->getMenu()->getActive();
        $this->params = $active->getParams();
        $this->items = $this->get('Items');
        $this->form = $this->get('Form');
        return parent::display($tpl);
    }
}
?>
