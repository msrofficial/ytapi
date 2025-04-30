<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_REQUEST['video-url'])) {

    include('other_php_files/functions.php');
    include('other_php_files/arrays.php');
    include('classes/VideoGetterClass.php');

    $video_link = $_REQUEST['video-url'];

    if (!isValidURL($video_link) || empty($video_link)) {
        $result = [
            'status' => 'error',
            'message' => 'Not a valid URL'
        ];
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    $url_host = parse_url($video_link)['host'];

    if (in_array($url_host, $facebookHosts)) {
        VideoGetterClass::getFacebookVideoInfoByParsing($video_link);
    } elseif (in_array($url_host, $instagramHosts)) {
        VideoGetterClass::getInstagramVideoInfoByParsing($video_link);
    } elseif (in_array($url_host, $dailymotionHosts)) {
        VideoGetterClass::getDailymotionVideoInfoByParsing($video_link);
    } else {
        // Use yt-dlp instead of youtube-dl
        $cmd = 'yt-dlp --no-playlist --dump-single-json --no-warnings ' . escapeshellarg($video_link) . ' 2>&1';
        $yt_dlp_output = shell_exec($cmd);

        if (isJSON($yt_dlp_output)) {
            $decoded = json_decode($yt_dlp_output, true);

            if (in_array($url_host, $facebookHosts)) {
                VideoGetterClass::getFacebookVideoInfoByParsing($video_link);
            } else {
                VideoGetterClass::getVideoInfo($decoded);
            }
        } else {
            http_response_code(410);
            echo json_encode([
                "error" => "Invalid JSON provided from yt-dlp.",
                "raw_output" => $yt_dlp_output
            ]);
        }
    }
} else {
    http_response_code(406);
    echo json_encode([
        "error" => "Required valid video url."
    ]);
}
?>
