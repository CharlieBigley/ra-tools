<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_ra_tools
 */

namespace Ramblers\Component\Ra_tools\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;

class ClusterModel extends AdminModel
{
    
    //public function getTable($type = 'Cluster', $prefix = 'Administrator', $config = array())
    public function getTable($type = 'Cluster', $prefix = 'ClusterTable', $config = array())
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_ra_tools.cluster', 'cluster', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        $data = $this->getItem();
        return $data;
    }
}
