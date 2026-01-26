<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_ra_tools
 */

namespace Ramblers\Component\Ra_tools\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class ClustersModel extends ListModel
{
    protected function populateState($ordering = null, $direction = null)
    {
        parent::populateState($ordering ?: 'name', $direction ?: 'ASC');
    }

    public function getListQuery()
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('c.id, c.code, c.name, c.website, c.contact_id, con.name AS contact_name')
            ->from($db->quoteName('#__ra_clusters', 'c'))
            ->leftJoin($db->quoteName('#__contact_details', 'con') . ' ON con.id = c.contact_id');
        if (JDEBUG) {
            \Joomla\CMS\Factory::getApplication()->enqueueMessage('sql = ' . $this->_db->replacePrefix($query), 'notice');
        }
        return $query;
    }
}
