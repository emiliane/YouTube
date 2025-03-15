//import getYoutubePlayListInfo from 'https://raw.githubusercontent.com/emiliane/Youtube-Playlist-Info/refs/heads/main/youtube-playlist-info.js';
//import ip6 from 'https://cdn.jsdelivr.net/gh/elgs/ip6/ip6.js';


// https://cdn.jsdelivr.net/gh/emiliane/Youtube-Playlist-Info@master/youtube-playlist-info.js

var listID = 'PLycloV5Iov5EKRJhDaMmDTA-uMaasis-S'
var listID2 = 'PLMtO55TGKXEKk2i-k6GSkD8HvYeCsnSv4'
var listID3 ='PL3uOtlDuT01zfcvWFQEqMIoH6dd1YRSf8'

//var array = await getYoutubePlayListInfo(listID2, 1)
var array = await getYoutubePlayListInfo(listID)
showArray(array)



async function getYoutubePlayListInfo(id, tryForManyTimes) {
    var url = 'https://www.youtube.com/playlist?list=' + id
    var list = await getListFirstPart(url)
    var startTime = new Date();

    var newLastVideoId = getLastID(list)
    var oldLastVideoId = ''
    var multe = 0
    var testMare = 0
    while (oldLastVideoId != newLastVideoId || multe < tryForManyTimes) {
        if (oldLastVideoId != newLastVideoId) {
            multe = 0
            if (multe + 5 > tryForManyTimes) {
                tryForManyTimes = multe + 5
            }
        } else {
            multe = multe + 1
            if (testMare < multe) {
                testMare = multe
                console.log('De câte ori maxim: ' + (multe + 1))
            }
        }
        //console.log('De câte ori: ' + (multe + 1))
        //console.log(newLastVideoId, oldLastVideoId)
        url = 'https://www.youtube.com/watch?v=' + newLastVideoId + '&list=' + id
        console.log(url + ' de ' + (multe + 1))
        endTimeDifference(startTime)
        var newList = await getListNextPart(url)
        list = concatNew(list, newList)
        oldLastVideoId = newLastVideoId
        var newLastVideoId = getLastID(list)
        //console.log(newLastVideoId, oldLastVideoId)
    }

    return list
}

async function getListFirstPart(url) {
    var response = await fetch(url)
    console.log(response)
    var data = await response.text()
    var list = titluriSiId(data)
    return list
}


async function getListNextPart(url) {
    var response = await fetch(url)
    var data = await response.text()
    var list = t222222222(data)

    return list
}


function getLastID(array) {
    //console.log(array)
    var lastid = array[array.length - 1][0]
    return lastid
}

function titluriSiId(data) {
    var info = []
    var indexes = find(data, 'playlistVideoRenderer');

    var playlistVideoRenderer = []
    for (var i = 0; i < indexes.length - 1; i++) {
        var a = data.substring(indexes[i], indexes[i + 1])
        playlistVideoRenderer.push(a)
    }
    a = data.substring(indexes[indexes.length - 1])
    playlistVideoRenderer.push(a)

    for (var i = 0; i < playlistVideoRenderer.length; i++) {
        let render = playlistVideoRenderer[i]
        var title = render.substring(render.indexOf('title'))
        title = title.substring(title.indexOf('text'))
        title = title.substring(title.indexOf(':') + 2, title.indexOf('"}]'))
        var videoId = render.substring(render.indexOf('videoId'))
        videoId = videoId.substring(videoId.indexOf(':') + 2, videoId.indexOf('",'))
        //console.log(i + 1, videoId, title)
        var video = [videoId, title]
        info.push(video)
    }
    return info
}

function t222222222(data) {
    var info = []
    var indexes = find(data, 'playlistPanelVideoRenderer');

    var playlistVideoRenderer = []
    for (var i = 0; i < indexes.length - 1; i++) {
        playlistVideoRenderer.push(data.substring(indexes[i], indexes[i + 1]))
    }
    playlistVideoRenderer.push(data.substring(indexes[indexes.length - 1]))

    for (var i = 0; i < playlistVideoRenderer.length; i++) {
        let render = playlistVideoRenderer[i]

        var title = render.substring(render.indexOf('simpleText'))
        title = title.substring(title.indexOf(':') + 2, title.indexOf('"},"'))
        var videoId = render.substring(render.indexOf('videoId'))
        videoId = videoId.substring(videoId.indexOf(':') + 2, videoId.indexOf('",'))
        var video = [videoId, title]
        info.push(video)
    }
    return info
}

function split_at_index(value, index) {
    return value.substring(0, index) + "," + value.substring(index);
}

function split_from_index_to_index(value, index1, index2) {
    return value.substring(index1, index2)
}

function find(sourceStr, searchStr) {
    const indexes = [...sourceStr.matchAll(new RegExp(searchStr, 'gi'))].map(a => a.index);
    return indexes
}

function concatNew(firstArray, secondArray) {
    var result = [...new Set([...firstArray, ...secondArray])]
    result = remove_duplicates_safe(result)
    return result
}

function remove_duplicates_safe(arr) {
    var seen = {};
    var ret_arr = [];
    for (var i = 0; i < arr.length; i++) {
        if (!(arr[i] in seen)) {
            ret_arr.push(arr[i]);
            seen[arr[i]] = true;
        }
    }
    return ret_arr;
}

function showArray(array) {
    const zeroPad = (num, places) => String(num).padStart(places, '0')
    var numberDigits = array.length.toString().length;

    for (let index = 0; index < array.length; index++) {
        const element = array[index];
        console.log(element[0] + ' ' + element[1] + ' | ' + zeroPad(index + 1, numberDigits))
    }
}

function endTimeDifference(startTime) {
    var endTime = new Date();
    var timeDiff = endTime - startTime; //in ms
    // strip the ms
    timeDiff /= 1000;

    // get seconds 
    var seconds = Math.round(timeDiff);
    console.log(seconds + " seconds");
}