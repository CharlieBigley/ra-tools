<?php

/**
 * Various common functions used to access Json feeds
 *
 * @version     3.5.5
 * @package     com_ra_tools
 * @author      Charlie Bigley <charlie@bigley.me.uk>
 *
 * 16/06/23 CB Created
 * 21/04/25 CB get API from configuration settings, added support for Organisation feed
 * 18/08/25 CB get API key from ra_api_sites
 * 11/03/26 CB added displayFields and fetchApiData 
 */

namespace Ramblers\Component\Ra_tools\Site\Helpers;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Uri\Uri;
use Ramblers\Component\Ra_tools\Site\Helpers\ToolsHelper;
use Ramblers\Component\Ra_tools\Site\Helpers\ToolsTable;

class JsonHelper {

    private $api_key;
    private $url = 'https://walks-manager.ramblers.org.uk/api/volunteers/';
    public $feedType = 'walksevents';          // This can be over-written

    /**
     * Display all fields from the first record in the response
     * @param array $response Decoded API response
     * @return array List of field names and values
     */
    public static function displayFields($response)
    {
        if (!isset($response['data'][0])) {
            return ['error' => 'No records found'];
        }
        return $response['data'][0]['attributes'] ?? $response['data'][0];
    }

     /**
     * Execute a curl command to fetch API data
     * @param int $api_site_id ID of the record in api_sites
     * @param string $endpoint The required endpoint URL
     * @param int $verbose Verbose flag (0/1)
     * @return array Decoded response or error info
     */
    public static function fetchApiData($api_site_id, $endpoint, $verbose = 0)
    {
        $db = Factory::getDbo();
        // Get token for api_site_id
        $query = $db->getQuery(true)
            ->select($db->quoteName(['token', 'url']))
            ->from($db->quoteName('#__ra_api_sites'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $api_site_id, ParameterType::INTEGER);
        $db->setQuery($query);
        $site = $db->loadObject();
        if (!$site || empty($site->token)) {
            return ['error' => 'API site or token not found'];
        }
        $token = $site->token;
        $url = $site->url . $endpoint;
        $headers = [
            'Accept: application/vnd.api+json',
            'Content-Type: application/json',
            'X-Joomla-Token: ' . $token,
            'Authorization: Bearer ' . $token
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
        // Split headers/body
        $header_size = $curl_info['header_size'] ?? 0;
        $raw_headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $decoded = json_decode($body, true);
        if ($verbose) {
            // Log to DB with correct columns
            $log_date = date('Y-m-d H:i:s');
            $sub_system = 'RA Develop';
            $record_type = 11;
            $ref = 'builds';
            $message = "Endpoint: $endpoint\n" .
                "API Site ID: $api_site_id\n" .
                "Headers: " . json_encode($headers) . "\n" .
                "Raw Headers: $raw_headers\n" .
                "Body: $body\n" .
                "Curl Info: " . json_encode($curl_info) . "\n" .
                "Curl Error: $curl_error\n" .
                "Decoded: " . json_encode($decoded);
            $query = "INSERT INTO #__ra_logfile (`log_date`, `sub_system`, `record_type`, `ref`, `message`) VALUES (" .
                $db->quote($log_date) . ", " .
                $db->quote($sub_system) . ", " .
                (int)$record_type . ", " .
                $db->quote($ref) . ", " .
                $db->quote($message) . ")";
            $db->setQuery($query);
            $db->execute();
        }
        return $decoded ?: ['error' => $curl_error ?: 'No response'];
    }


//    private $key = '&api-key=742d93e8f409bf2b5aec6f64cf6f405e';

    public function getCountEvents($code) {
        // https://walks-manager.ramblers.org.uk/api/volunteers/walksevents?types=walkevents&types=group-event&api-key=742d93e8f409bf2b5aec6f64cf6f405e&groups=CF
        return $this->getJson('group-event', 'groups=' . $code, 'Y');
    }

    public function getCountOrganisations($code) {
        return $this->getJson('organisation', 'groups=' . $code, 'Y');
    }

    public function getCountWalks($code) {
//        $count = $this->getJson('group-walk', 'groups=' . $code, 'Y');
//        if (($count ==0)|| $count == ''){
//            return '-';
//        }
        return $this->getJson('group-walk', 'groups=' . $code, 'Y');
    }

    public function getJson($type, $criteria, $count = 'N') {

        /*
         * Documention is on https://app.swaggerhub.com/apis-docs/abateman/Ramblers-third-parties/1.0.0#/default/get_api_volunteers_walksevents
         * $type can be: walkevents, group-event or organisation
         */

//        if ($type == 'organisation') {
//            $url = $this->url . 'groups';
//        } else {
//            $url = $this->url . 'walksevents?types=';
//            $url .= $type;
//        }
//
//        $api_key = $this->toolsHelper->lookupApiKey();
//        if ($api_key == '') {
//            $message = $message = 'API key not found - please create a record in API sites';
//            Factory::getApplication()->enqueueMessage($message, 'error');
//            return false;
//        }
//
//        $url .= '&api-key=' . $api_key . '&' . $criteria;
        $feedurl = $this->setUrl($type, $criteria);
//        if (JDEBUG) {
//        echo 'getJson:' . $feedurl . '<br>';
//        }
//        $url .= '&limit=3';
//        $url .= '&dow=7';
//      set up maximum time of 10 minutes
        $max = 10 * 60;
        set_time_limit($max);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $feedurl);
        curl_setopt($ch, CURLOPT_HEADER, false); // do not include header in output
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // do not follow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // do not output result
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $max);  // allow xx seconds for timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, $max);  // allow xx seconds for timeout
//	curl_setopt($ch, CURLOPT_REFERER, JURI::base()); // say who wants the feed

        curl_setopt($ch, CURLOPT_REFERER, "com_ra_tools"); // say who wants the feed

        $data = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $message = 'Error: ' . $httpCode;
            $message .= ', ' . $error;
            $toolsHelper = new Toolshelper;
            if ($toolsHelper->isSuperuser()) {
                $message .= ' ' . $url;
            }

            Factory::getApplication()->enqueueMessage($message, 'error');
            return;
        }

        $temp = json_decode($data);
//        echo '<br><b>Summary</b><br>';
//        var_dump($temp->summary);
//        echo '<br><b>Data</b><br>';
//        var_dump($temp->data);

        if ($count == 'Y') {
            return $temp->summary->count;
        } else {
            return$temp->data;
        }
    }

    private function getUrl($type, $criteria) {
        if ($type == 'organisation') {
            $url = $this->url . 'groups';
        } else {
            $url = $this->url . 'walksevents?types=';
            $url .= $type;
        }
        $this->setKey();
        //       return $this->url . $type . $this->api_key . $criteria;
        die($this->url . $type . $this->api_key . $criteria);
    }

    private function setKey() {
        $toolsHelper = new Toolshelper;
        $api_key = $toolsHelper->lookupApiKey();
        if ($api_key == '') {
            $message = 'API key not found - please create a record in API sites';
            Factory::getApplication()->enqueueMessage($message, 'error');
            return false;
        }

        $this->api_key = '&api-key=' . $api_key;
    }

    public function setUrl($type, $criteria) {
        // $type can be: walkevents, group-event or organisation
        if ($type == 'organisation') {
            $url = $this->url . 'groups?';
        } else {
            $url = $this->url . 'walksevents?types=';
            $url .= $type . '&';
        }
        $toolsHelper = new Toolshelper;
        $api_key = $toolsHelper->lookupApiKey();
        if ($api_key == '') {
            $message = 'API key not found - please create a record in API sites';
            Factory::getApplication()->enqueueMessage($message, 'error');
            return false;
        }

        $url .= 'api-key=' . $api_key;
        if (trim($criteria) !== '') {
            $url .= '&' . $criteria;
        }
//        if (JDEBUG) {
//        echo 'setUrl: ' . $url . '<br>';
//        }
        return $url;
    }

    public function showEventButton($id) {
        // Parameter may be a comma delimited array of ids
        // Returns a button with a link to show the JSON feed for the given event
        $this->setKey();
        $target = $this->setUrl('group-event', 'ids=' . $id);
        $toolsHelper = new ToolsHelper;

        return$toolsHelper->imageButton('I', $target, true);
    }

    public function showEventsButton($code) {
        // Returns a button with a link to show the JSON feed for the Area/Group

        $target = $this->setUrl('group-event', 'groups=' . $code);
        $toolsHelper = new ToolsHelper;

        return$toolsHelper->imageButton('I', $target, true);
    }

    public function showFirst($api_site_id, $events) {
        $sql = 'SELECT * FROM #__ra_api_sites WHERE id=' . $api_site_id;
        $site = $this->toolsHelper->getItem($sql);
        if (is_null($site)) {
            echo 'API site ' . $api_site_id . ' not found';
            return;
        }   
        echo '<h2>First record on feed from ';
        echo $site->title;
        echo '</h2>';
        $website = $site->url;
        // Accept either the raw JSON payload (with a data key) or the data array directly.
        $payload = $events;
        if (isset($events['data']) && is_array($events['data'])) {
            $payload = $events['data'];
        }

        $count = is_array($payload) ? count($payload) : 0;
        echo $count . ' records returned<br>';

        if ($count === 0) {
            echo 'No records to display<br>';
        } else {
            $event = $payload[0];

            $attributes = isset($event['attributes']) && is_array($event['attributes']) ? $event['attributes'] : array();

            echo '<table class="table">';
            echo '<tr><th>Field</th><th>Value</th></tr>';
            $fieldCount = 0;
            foreach ($attributes as $key => $val) {
                $fieldCount++;
                if (is_array($val) || is_object($val)) {
                    $val = json_encode($val);
                }
                echo '<tr>';
                echo '<td>' . htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo $fieldCount . ' fields<br>';

        }

    }

    public function showWalkButton($id) {
        // Parameter may be a comma delimited array of ids
        // Returns a button with a link to show the JSON feed for the given walk

        $target = $this->setUrl('group-walk', 'groups=' . $code);
        $toolsHelper = new ToolsHelper;

        return$toolsHelper->imageButton('I', $target, true);
    }

    public function showWalksButton($code) {
        // Parameter may be a comma delimited array of ids
        // Returns a button with a link to show the JSON feed for the given walk
        // 'group-walk', 'groups=' . $code
        $target = $this->setUrl('group-walk', 'groups=' . $code);
        $toolsHelper = new ToolsHelper;

        return$toolsHelper->imageButton('I', $target, true);
    }

    public function groupFeed($group_code) {
        // Returns link to enable display of all walks for given Group
        return $this->url . 'group-walk&groups=' . $group_code . $this->key;
    }

}
