<?php namespace App\Models;

use CodeIgniter\Model;

/**
 * @author Samuel Omotayo <sam.omotayo@tearfund.org>
 * Class to hold all functions that will interact with the csv file
 */
class chromeBookModel extends Model
{

    /**
     * read csv file into a string and separate by new line 
     *  place strings into multidimentional arrays where columns can be accessed using header of csv
     * @return void
     */
    public function get_csv()
    {
        if (($csv = file_get_contents("../writable/uploads/cros.csv")) !== FALSE)
        {
            $contents  = explode("\n", $csv );
            $headers = str_getcsv(array_shift($contents));
            $data = array();
            foreach ($contents as $content)
            {
                $row = array();
                foreach(str_getcsv($content) as $key => $value)
                    $row[$headers[$key]] = $value;
                
                $row = array_filter($row);
                $data[] = $row;
            }
            $array = array(
                "data"=> $data
            );            
        }

        else{
            //log error 
            $var = true;
        }
        $this->data_db($array);
    }

    /**
     * Place contents of csv into db
     * @return void
     */
    public function data_db($array)
    {
        $db = \Config\Database::connect();
        $sql = "INSERT into chrome_book(assetID, deviceid, username, location, activity, recent_user, ip_address, os_version) values(?,?,?,?,?,?,?,?) 
                on duplicate key update
                assetID=values(assetID),
                deviceid=values(deviceid),
                username=values(username),
                location=values(location),
                activity=values(activity),
                recent_user=values(recent_user),
                ip_address=values(ip_address),
                os_version=values(os_version)
                ";

        $data = $array["data"];
        foreach($data as $key => $val)
        {
            //var_dump($val);
            try
            {
                if (count($val) <=0)
                    break;

                //var_dump($val);
                //echo "<br><br>";

                $deviceid = $val["deviceId"];
                $assetId = $val["annotatedAssetId"];
                $username = ($val["annotatedUser"] = empty($val["annotatedUser"]) ?'':$val["annotatedUser"]);
                $location = ($val["annotatedLocation"] = empty($val["annotatedLocation"]) ? '':$val["annotatedLocation"]);
                $recent_activity = ($val["mostRecentActivity"] = empty($val["mostRecentActivity"])? '':$val["mostRecentActivity"]);
                $recent_user = ($val["mostRecentUser"] = empty($val["mostRecentUser"])? '':$val["mostRecentUser"]);
                $wanIp = ($val["wanIpAddress"] = empty($val["wanIpAddress"])? '':$val["wanIpAddress"]);
                $osVersion = ($val["osVersion"] = empty($val["osVersion"])? '':$val["osVersion"]);
            }
            catch (Exception $e)
            {
                echo "Exception: $e";
                //instead of echoing exception, log it
            }
            
            if(!($db->query($sql, [$assetId,$deviceid,$username,$location,$recent_activity,$recent_user,$wanIp,$osVersion])))
            {
                //log error instead of var_dump error
                var_dump($db->error());
            }
        }

    }
}

?>