<?php
/**
 * @version    5.3.2
 * @package    com_ra_tools
 * @author     Charlie Bigley <webmaster@bigley.me.uk>
 * @copyright  2024 Charlie Bigley
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * 24/01/26 Created by copilot
 */
namespace Ramblers\Component\Ra_tools\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Build Controller
 * Handles form submission and delegates to model
 */
class BuildController extends BaseController
{
    /**
     * Save the build
     */
    public function save()
    {
        $data = $this->input->post->get('jform', array(), 'array');
        $component = isset($data['component']) ? $data['component'] : '';
        $version = isset($data['version']) ? $data['version'] : '';

        if (empty($component) || empty($version)) {
            $this->setRedirect('index.php', 'Component and version are required', 'error');
            return;
        }

        /** @var \Ramblers\Component\Ra_tools\Site\Model\BuildModel $model */
        $model = $this->getModel('Build');
        
        $result = $model->build($component, $version);

        if ($result['success']) {
            $this->setRedirect('index.php', 'Build completed successfully: ' . $result['file']);
        } else {
            $this->setRedirect('index.php', 'Build failed: ' . $result['error'], 'error');
        }
    }
}
?>
