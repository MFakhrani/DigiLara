<?php

namespace Fakhrani\Digilara\Functions;

use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class DigilaraUpdate {

    public static function companySetting()
    {
        $setting = config('fakhrani_digilara.setting');
        return (new $setting)::first();
    }

    public static function showReview()
    {
        $setting = config('fakhrani_digilara.setting');
        $envatoUpdateCompanySetting = (new $setting)::first();

        // ShowReview only when supported members and show_review_modal is enabled
        return (!is_null($envatoUpdateCompanySetting->supported_until) &&
            !\Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->isPast() &&
            ((\Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->diffInDays(\Carbon\Carbon::now()) <= 175) || (\Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->diffInDays(\Carbon\Carbon::now()) > 200 && \Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->diffInDays(\Carbon\Carbon::now()) <= 360)) &&
            $envatoUpdateCompanySetting->show_review_modal===1);

    }

    public static function reviewUrl()
    {
        $setting = config('fakhrani_digilara.setting');
        $envatoUpdateCompanySetting = (new $setting)::first();

        $url = str_replace('verify-purchase','review',config('fakhrani_digilara.verify_url'));
        return $url.'/'.$envatoUpdateCompanySetting->purchase_code;

    }
    
    public static function plugins(){
        $client = new Client();
        $res = $client->request('GET', config('fakhrani_digilara.plugins_url'), ['verify' => false]);
        $lastVersion = $res->getBody();
        return json_decode($lastVersion, true);
    }
    
     public static function updateVersionInfo()
    {
        $updateVersionInfo = [];
        try {
            $client = new Client();
            // Get Data from server for download files
            $res = $client->request('GET', config('fakhrani_digilara.updater_file_path'), ['verify' => false]);
            $lastVersion = $res->getBody();
            $lastVersion = json_decode($lastVersion, true);
            if ($lastVersion['version'] > File::get('version.txt')) {
                $updateVersionInfo['lastVersion'] = $lastVersion['version'];
                $updateVersionInfo['updateInfo'] = $lastVersion['description'];
            }
            $updateVersionInfo['updateInfo'] = $lastVersion['description'];

        } catch (\Exception $e) {
        }

        try{
            // Get data of Logs
            $resLog = $client->request('GET', config('fakhrani_digilara.versionLog') . '/' . File::get('version.txt'), ['verify' => false]);
            $lastVersionLog = json_decode($resLog->getBody(), true);
            foreach ($lastVersionLog as $item) {
                // Ignore duplicate of latest version
                $releaseDate = $item['release_date']?' (Released on:'. Carbon::parse($item['release_date'])->format('d M Y').')':'';
                if (version_compare($item['version'], $lastVersion['version']) == 0) {
                    $updateVersionInfo['updateInfo'] = '<strong>Version: ' . $item['version'] .$releaseDate. '</strong>' . $item['description'];
                    continue;
                };
                $updateVersionInfo['updateInfo'] .= '<strong>Version: ' . $item['version'] .$releaseDate. '</strong>' . $item['description'];
            }
        } catch (\Exception $e) {
        }

        $updateVersionInfo['appVersion'] = File::get('version.txt');
        $laravel = app();
        $updateVersionInfo['laravelVersion'] = $laravel::VERSION;
        return $updateVersionInfo;
    }


}
