<?php
/**
 * @version    3.5.5
 * @package    com_ra_tools
 * @author     Charlie Bigley <webmaster@bigley.me.uk>
 * @copyright  2023 Charlie Bigley
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * 10/03/25 GPT created 
 */

namespace Ramblers\Component\Ra_tools\Administrator\Field;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Language\Text;

// Prevent direct access
\defined('_JEXEC') or die;

class ContactField extends FormField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    protected $type = 'Contact';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        // Get a database object
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'name']))
            ->from($db->quoteName('#__contact_details'))
            ->order($db->quoteName('name'));
        $db->setQuery($query);
        $contacts = $db->loadObjectList();

        $options = [];
        $options[] = '<option value="">' . Text::_('JSELECT') . '</option>';
        foreach ($contacts as $contact) {
            $selected = ($this->value == $contact->id) ? ' selected' : '';
            $options[] = '<option value="' . (int) $contact->id . '"' . $selected . '>'
                . htmlspecialchars($contact->name, ENT_COMPAT, 'UTF-8') . '</option>';
        }

        $html = '<select name="' . $this->name . '" id="' . $this->id . '" class="custom-select">';
        $html .= implode("\n", $options);
        $html .= '</select>';

        return $html;
    }
}
