<?php
/**
 * @version    3.4.7
 * @author     Charlie Bigley <webmaster@bigley.me.uk>
 * @copyright  2026 Charlie Bigley
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Ramblers\Component\Ra_tools\Administrator\Controller;

\defined('_JEXEC') or die;
//die('Loaded');

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

class ClusterController extends FormController {
    protected $view_list = 'clusters';
    public function __construct($config = array())
    {
        file_put_contents(JPATH_ROOT . '/administrator/logs/cluster_display.log', date('c') . " ClusterController::__construct() called\n", FILE_APPEND);
        parent::__construct($config);

    }

    public function edit($key = null, $urlVar = null)
    {
        file_put_contents(JPATH_ROOT . '/administrator/logs/cluster_display.log', date('c') . " ClusterController::edit() called\n", FILE_APPEND);
        return parent::edit($key, $urlVar);
    }

    

    public function display($cachable = false, $urlparams = array())
    {
        // Diagnostic file log
        $logFile = JPATH_ROOT . '/administrator/logs/cluster_display.log';
        $msg = date('c') . " ClusterController::display() called\n";
        file_put_contents($logFile, $msg, FILE_APPEND);
        return parent::display($cachable, $urlparams);
    }

    public function cancel($key = null)
    {
        // Diagnostic file log
        $logFile = JPATH_ROOT . '/administrator/logs/cluster_cancel.log';
        $msg = date('c') . " ClusterController::cancel() called\n";
        file_put_contents($logFile, $msg, FILE_APPEND);

        // Redirect to clusters list view
        $this->setRedirect('index.php?option=com_ra_tools&view=clusters');
        return true;
    }

    public function save($key = null, $urlVar = null)
    {

        $data = $this->input->post->get('jform', array(), 'array');

        // Validation and formatting
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }
        if (isset($data['name'])) {
            $data['name'] = ucfirst(strtolower($data['name']));
        }
        if (isset($data['area_list'])) {
            $areas = array_map('strtoupper', array_filter(array_map('trim', explode(',', $data['area_list']))));
            $data['area_list'] = implode(',', $areas);
        }
        if (isset($data['website']) && !empty($data['website'])) {
            if (!filter_var($data['website'], FILTER_VALIDATE_URL)) {
                $this->setMessage('Invalid website URL', 'error');
                $this->setRedirect('index.php?option=com_ra_tools&view=cluster&layout=edit&id=' . (int) $data['id']);
                return false;
            }
        }

        // Save using parent
        $this->input->post->set('jform', $data);
        $result = parent::save($key, $urlVar);
        \Joomla\CMS\Factory::getApplication()->enqueueMessage('ClusterController::save() result: ' . var_export($result, true), 'notice');
        \Joomla\CMS\Factory::getApplication()->enqueueMessage('ClusterController::save() data: ' . json_encode($data), 'notice');
        if ($result) {
            $this->setRedirect('index.php?option=com_ra_tools&view=clusters');
        } else {
            $this->setMessage('Save failed', 'error');
        }
        return $result;
    }

    public function test(){
        echo 'Cluster controller<br>';
                  $logFile = JPATH_ROOT . '/administrator/logs/cluster_display.log';
            $msg = date('c') . " ClusterController::test() called\n";
            file_put_contents($logFile, $msg, FILE_APPEND);
        Factory::getApplication()->enqueueMessage('ClusterController::test() ', 'notice');
        $this->setRedirect('index.php?option=com_ra_tools&view=dashboard');
    }
}
