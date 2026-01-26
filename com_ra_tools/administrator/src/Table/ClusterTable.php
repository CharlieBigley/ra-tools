<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_ra_tools
 */

namespace Ramblers\Component\Ra_tools\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Table\Table;

class ClusterTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__ra_clusters', 'id', $db);
    }

    public function bind($array, $ignore = '')
    {
        // Ensure code is uppercase
        if (isset($array['code'])) {
            $array['code'] = strtoupper($array['code']);
        }
        // Ensure name is capitalized
        if (isset($array['name'])) {
            $array['name'] = ucfirst(strtolower($array['name']));
        }
        // Ensure area_list is uppercase, comma separated, no trailing comma
        if (isset($array['area_list'])) {
            $areas = array_map('strtoupper', array_filter(array_map('trim', explode(',', $array['area_list']))));
            $array['area_list'] = implode(',', $areas);
        }
        return parent::bind($array, $ignore);
    }
}
