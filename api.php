<?php

$listID = filter_input(INPUT_GET, "playlist", FILTER_SANITIZE_STRING);

$array = getYoutubePlayListInfo($listID);
$array = remove_duplicates_safe($array);
echo json_encode($array);

function getYoutubePlayListInfo($id, $tryForManyTimes = 5)
{
    $url = 'https://www.youtube.com/playlist?list=' . $id;
    $list = getListFirstPart($url);
    //return;
    $startTime = microtime(true);

    $newLastVideoId = getLastID($list);
    $oldLastVideoId = '';
    $multe = 0;
    $testMare = 0;

    while ($oldLastVideoId != $newLastVideoId || $multe < $tryForManyTimes) {
        if ($oldLastVideoId != $newLastVideoId) {
            $multe = 0;
            if ($multe + 5 > $tryForManyTimes) {
                $tryForManyTimes = $multe + 5;
            }
        } else {
            $multe++;
            if ($testMare < $multe) {
                $testMare = $multe;
                //echo 'De câte ori maxim: ' . ($multe + 1) . PHP_EOL;
            }
        }
        $url = 'https://www.youtube.com/watch?v=' . $newLastVideoId . '&list=' . $id;
        //echo '<br>' . $url . ' de ' . ($multe + 1) . '<br>' . PHP_EOL;
        endTimeDifference($startTime);
        $newList = getListNextPart($url);
        $list = array_merge($list, $newList);
        $oldLastVideoId = $newLastVideoId;
        $newLastVideoId = getLastID($list);
    }

    return $list;
}

function getListFirstPart($url)
{
    $response = file_get_contents($url);
    //// echo $response . PHP_EOL;
    $list = titleAndIdFirst($response);
    return $list;
}

function getListNextPart($url)
{
    $response = file_get_contents($url);
    $list = titleAndIdAfter($response);
    return $list;
}

function getLastID($array)
{
    $lastid = $array[count($array) - 1][0];
    return $lastid;
}

function titleAndIdFirst($html)
{
    $dom = new DOMDocument();
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    $scripts = $dom->getElementsByTagName('script');
    $content = $scripts->item(22)->textContent;

    //// echo $content;

    $jsonobj = substr($content, strpos($content, '=') + 1);

    $jsonobj = rtrim($jsonobj, ";");
    //// echo $jsonobj;

    $json = json_decode($jsonobj);

    $array = $json->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->playlistVideoListRenderer->contents;

    //var_dump($a);

    $results = array();

    foreach ($array as $key) {
        $id = $key->playlistVideoRenderer->videoId;
        $title = $key->playlistVideoRenderer->title->runs[0]->text;
        $e = $id . ' ' . $title . '<br>';
        //var_dump($e);
        if (isset($id) && isset($title)) {
            $result = array($id, $title);
            $results[] = $result;
        }
    }

    $results = array_filter($results);
    //echo json_encode($results);

    return $results;
}
function titluriSiId2($data)
{
    $info = [];
    $indexes = find($data, 'playlistVideoRenderer');

    $playlistVideoRenderer = [];
    for ($i = 0; $i < count($indexes) - 1; $i++) {
        $a = substr($data, $indexes[$i], $indexes[$i + 1] - $indexes[$i]);
        $playlistVideoRenderer[] = $a;
    }
    $a = substr($data, $indexes[count($indexes) - 1]);
    $playlistVideoRenderer[] = $a;

    foreach ($playlistVideoRenderer as $render) {
        $title = substr($render, strpos($render, 'title'));
        $title = substr($title, strpos($title, 'text'));
        $title = substr($title, strpos($title, ':') + 2, strpos($title, '"}]') - strpos($title, ':') - 2);
        $videoId = substr($render, strpos($render, 'videoId'));
        $videoId = substr($videoId, strpos($videoId, ':') + 2, strpos($videoId, '",') - strpos($videoId, ':') - 2);
        $video = [$videoId, $title];
        $info[] = $video;
    }
    return $info;
}

function titleAndIdAfter($data)
{
    $dom = new DOMDocument();
    $dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));

    $content = $dom->textContent;
    // echo '<br><br>aaaaaaaaaaaaaaaaaaaaaa body<br><br>';
    // echo '<br><br>aaaaaaaaaaaaaaaaaaaaaa body<br><br>';
    //var_dump($scripts);
    // echo '<br><br>aaaaaaaaaaaaaaaaaaaaaa body<br><br>';
    // echo '<br><br>aaaaaaaaaaaaaaaaaaaaaa body<br><br>';

    $search = 'ytInitialData';
    $content = substr($content, strpos($content, $search) + strlen($search) + 2);

    //  // echo $jsonobj;

    $content = substr($content, 0, strpos($content, 'window.ytcsi') - 5);

    //// echo $content;

    $json = json_decode($content);

    $array = $json->contents->twoColumnWatchNextResults->playlist->playlist->contents;

    //var_dump($array);

    $results = array();

    foreach ($array as $key) {
        $id = $key->playlistPanelVideoRenderer->videoId;
        $title = $key->playlistPanelVideoRenderer->title->simpleText;
        $e = $id . ' ' . $title . '<br>';
        //var_dump($e);
        if (isset($id) && isset($title)) {
            $result = array($id, $title);
            $results[] = $result;
        }
    }

    $results = array_filter($results);
    //// echo json_encode($results);

    return $results;
}

function t2222222223333($data)
{
    $info = [];
    $indexes = find($data, 'playlistPanelVideoRenderer');

    $playlistVideoRenderer = [];
    for ($i = 0; $i < count($indexes) - 1; $i++) {
        $playlistVideoRenderer[] = substr($data, $indexes[$i], $indexes[$i + 1] - $indexes[$i]);
    }
    $playlistVideoRenderer[] = substr($data, $indexes[count($indexes) - 1]);

    foreach ($playlistVideoRenderer as $render) {
        $title = substr($render, strpos($render, 'simpleText'));
        $title = substr($title, strpos($title, ':') + 2, strpos($title, '"},') - strpos($title, ':') - 2);
        $videoId = substr($render, strpos($render, 'videoId'));
        $videoId = substr($videoId, strpos($videoId, ':') + 2, strpos($videoId, '",') - strpos($videoId, ':') - 2);
        $video = [$videoId, $title];
        $info[] = $video;
    }
    return $info;
}




function split_at_index($value, $index)
{
    return substr($value, 0, $index) . "," . substr($value, $index);
}

function split_from_index_to_index($value, $index1, $index2)
{
    return substr($value, $index1, $index2 - $index1);
}

function find($sourceStr, $searchStr)
{
    preg_match_all('/' . preg_quote($searchStr, '/') . '/i', $sourceStr, $matches, PREG_OFFSET_CAPTURE);
    return array_column($matches[0], 1);
}

function concatNew($firstArray, $secondArray)
{
    $result = array_unique(array_merge($firstArray, $secondArray));
    $result = remove_duplicates_safe($result);
    return $result;
}

function remove_duplicates_safe($merged)
{
    $final = array();

    foreach ($merged as $current) {
        if (!in_array($current, $final)) {
            $final[] = $current;
        }
    }
    return $final;
}

function showArray($array)
{
    $zeroPad = function ($num, $places) {
        return str_pad($num, $places, '0', STR_PAD_LEFT);
    };
    $numberDigits = strlen(count($array));

    foreach ($array as $index => $element) {
        // // echo $element[0] . ' ' . $element[1] . ' | ' . $zeroPad($index + 1, $numberDigits) . PHP_EOL;
    }
}

function endTimeDifference($startTime)
{
    $endTime = microtime(true);
    $timeDiff = $endTime - $startTime; // in seconds
    $seconds = round($timeDiff);
    // // echo $seconds . " seconds" . PHP_EOL;
}
?>