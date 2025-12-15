<?php

/**
 * @version     3.4.2
 * @package     com_ra_tools
 * @copyright   Copyright (C) 2021. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Charlie <webmaster@bigley.me.uk> - https://www.stokeandnewcastleramblers.org.uk
 * 24/10/24 CB change default group_type to list
 * 16/12/24 CB show_criteria
 */

namespace Ramblers\Component\Ra_tools\Site\View\Programme_day;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView {

    protected $day;
    protected $display_type;
    protected $group;
    protected $intro;
    protected $limit;
    protected $show_criteria;
    protected $menu_id;
    protected $user;

    public function display($tpl = null) {
        $this->user = Factory::getApplication()->getIdentity();
        $app = Factory::getApplication();
        $this->day = Factory::getApplication()->input->getCmd('day', '');
        $this->menu_id = Factory::getApplication()->input->getCmd('Itemid', '');
        $menu = $app->getMenu()->getActive();
        if (is_null($menu)) {
            echo 'Menu params are null<br>';
        } else {
            $menu_params = $menu->getParams();
        }
        $group_type = $menu_params->get('group_type', 'list');
        $this->intro = $menu_params->get('intro');
        if ($this->day == '') {
            $this->day = $menu_params->get('day');
        }
        $this->display_type = $menu_params->get('display_type', 'simple');
        $this->show_cancelled = $menu_params->get('show_cancelled', '0');
        $this->restrict_walks = $menu_params->get('restrict_walks');
        $this->lookahead_weeks = (int) $menu_params->get('lookahead_weeks');
        $this->limit = (int) $menu_params->get('limit');
        $this->show_criteria = $menu_params->get('show_criteria', '2'); // default to Always
        //       var_dump($menu_params);
        $params = ComponentHelper::getParams('com_ra_tools');
//        var_dump($params);
//        echo '<br>end of params from component helper<br>';
        if ($group_type == "single") {
            $this->group = $params->get('default_group');
        } else {
            $this->group = $params->get('group_list');
        }

        echo "<h2>" . $this->day . " walks</h2>";
        $wa = $this->document->getWebAssetManager();
        $wa->useScript('keepalive')
                ->useScript('form.validate');
        return parent::display($tpl);
    }

}
