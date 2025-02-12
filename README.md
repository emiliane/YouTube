# YouTube

<!DOCTYPE html>
<meta charset="utf-8">

<style type="text/css">

  body {
  	font: normal normal 25px Arial, Tahoma, Helvetica, FreeSans, sans-serif;
  	color: #0A00A0;
  	padding: 0 40px 40px 40px;
  	background-image:url(https://themes.googleusercontent.com/image?id=16p0DhIQDIoTZOUyiLjbBSajoCvlXtai8rSbZiLnXfEwdwTFgFVIeDYQBf3S_b5BpMAfo);
  	
  }

  * {
    box-sizing: border-box;
  }

  .row {
    display: flex;
  }

  /* Create two equal columns that sits next to each other */
  .column {
    flex: 50%;
    padding: 10px;
  }

</style>

<head>
  <title>Jucător de discuri</title>
</head>

<html>
  <body>
  	<h1>Jucător de discuri</h1>
    <h4>
      <div>Adaugă videoclipuri după identificator:</div>
      <textarea id="lista" rows="4" cols="15"></textarea></br>
      <button type="button" onclick="iaUrmatoareleVideoclipuri()" style="width:144px;background-color:rgba(44,0,0,0.4);color: #0A00A0;font: normal normal 25px Arial, Tahoma, Helvetica, FreeSans, sans-serif;">Adaugă</button>
      <div id="informațieListă"></div>
      <div>Estompează (secunde):
      <input type="number" id="estompeaza" value="25" min="0" max="100" style="width:55px;height:44px;font: normal normal 25px Arial, Tahoma, Helvetica, FreeSans, sans-serif;"/></br>
      <button type="button" onclick="invarte()" style="width:144px;background-color:rgba(44,0,0,0.4);color: #0A00A0;font: normal normal 25px Arial, Tahoma, Helvetica, FreeSans, sans-serif;">Învârte</button>
      </div>
      <div id="informație"></div>
    </h4>
    
    <script>
      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');

      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var playerUnu;
      var playerDoi;
      var fadeSeconds;
      var precizie = 44;
      var listaVideoclipuri = ["1DEFmFzxAIU", "lKM-8CZRplI", "s7jXASBWwwI", "8NtijaKu8i0", "hJA9vnxwoHU", "apT2MbWd72o"];
      function onYouTubeIframeAPIReady() {
        playerUnu = new YT.Player('playerUnu', {
          height: '444',
          width: '100%',
          videoId: damiUrmătorulVideoclip(),
          playerVars: {
            'playsinline': 1
          },
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
        playerDoi = new YT.Player('playerDoi', {
          height: '444',
          width: '100%',
          videoId: damiUrmătorulVideoclip(),
          playerVars: {
            'playsinline': 1
          },
          events: {
            'onReady': onPlayerReady2,
            'onStateChange': onPlayerStateChange2
          }
        });
      }

      // 4. The API will call this function when the video player is ready.
      function onPlayerReady(event) {
        playerUnu.unMute();
        afiseaza(playerUnu, "Unu");
        afișeazăLista();
      }
      function onPlayerReady2(event) {
        playerDoi.unMute();
        afiseaza(playerDoi, "Doi");
      }

      // 5. The API calls this function when the player's state changes.
      //    The function indicates that when playing a video (state=1),
      //    the player should play for six seconds and then stop.
      var doneUnu = false;
      var doneDoi = false;
      function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.ENDED) {
          playerUnu.cueVideoById(damiUrmătorulVideoclip());
          playerUnu.unMute();
        }
      }
      function onPlayerStateChange2(event) {
        if (event.data == YT.PlayerState.ENDED) {
          playerDoi.cueVideoById(damiUrmătorulVideoclip());
          playerDoi.unMute();
        }
      }
      function stopVideo() {
        playerUnu.stopVideo();
      }
      function invarte() {
        playerUnu.playVideo();
        bucla();
      }
      function bucla() {
        try {
          iaValori();
          afiseaza(playerUnu, "Unu");
          afiseaza(playerDoi, "Doi");
          ajusteazaVolum(playerUnu);
          ajusteazaVolum(playerDoi);
          redare();
        } catch(err) {
          document.getElementById('informație').innerHTML = err.message;
        }
      	setTimeout(bucla, precizie);
      }

      function redare() {
        var principal = damiRedorPrincipal();
        var secundar = damiRedorSecundar();
        var timpRamas = principal.getDuration() - principal.getCurrentTime();

        if (timpRamas <= fadeSeconds) {
          secundar.playVideo();
        }
      }

      function iaValori() {
        fadeSeconds = document.getElementById("estompeaza").value;
      }

      function iaUrmatoareleVideoclipuri() {
        var lista = document.getElementById("lista").value.trim().split(/\r?\n/);
        listaVideoclipuri = listaVideoclipuri.concat(lista);
        listaVideoclipuri = listaVideoclipuri.filter(elm => elm);
        document.getElementById("lista").value = "";
        afișeazăLista();
      }

      function damiUrmătorulVideoclip() {
        let videoclip = listaVideoclipuri.shift();
        listaVideoclipuri.push(videoclip);
        afișeazăLista();
        return videoclip;
      }

      function damiRedorPrincipal() {
        var redor;
        if (playerUnu.getPlayerState() == YT.PlayerState.PLAYING) {
          redor = playerUnu;
        } else {
          redor = playerDoi;
        }
        return redor;
      }

      function damiRedorSecundar() {
        var redor;
        if (playerUnu.getPlayerState() == 1) {
          redor = playerDoi;
        } else {
          redor = playerUnu;
        }
        return redor;
      }

      function ajusteazaVolum(redor) {
        var volum;
        var timpActual = redor.getCurrentTime();
        var timpRamas = redor.getDuration() - timpActual;

      	if (timpRamas <= fadeSeconds) {
          var percent = timpRamas / fadeSeconds;
          volum = percent * 100;
        } else if (timpActual <= fadeSeconds) {
          var percent = timpActual / fadeSeconds;
          volum = percent * 100;
        } else {
          volum = 100;
        }
        redor.setVolume(volum);
      }

      function afiseaza(redor, numar) {
      	var titlu = document.querySelector("#player" + numar).title;
        var volum = redor.getVolume();
        var timp = redor.getCurrentTime();
        var timpRamas = redor.getDuration() - timp;

      	document.getElementById('titlu'+ numar).innerHTML = titlu;
        document.getElementById('informație' + numar).innerHTML = "Volum: " + volum + "</br>Timp: " + timp.toFixed(2);

        if (redor.getPlayerState() == YT.PlayerState.PLAYING) {
          document.getElementById('informație').innerHTML = "Volum: " + volum + " secunde rămase: " + timpRamas.toFixed(2) + " timp scurs: " + convertStoMs(timp.toFixed(0));
        }
      }

      function afișeazăLista() {
        var info = "";
        for (let i = 0; i < listaVideoclipuri.length; i++) {
          var numar = i + 1;
            info = info + numar + ": [" + listaVideoclipuri[i] + "], ";
        }
        info = info.slice(0, -2) + ".";
        document.getElementById('informațieListă').innerHTML = info;
      }

      function convertStoMs(seconds) {
        let minutes = Math.floor(seconds / 60);
        let extraSeconds = seconds % 60;
        minutes = minutes < 10 ? "0" + minutes : minutes;
        extraSeconds = extraSeconds< 10 ? "0" + extraSeconds : extraSeconds;
        var a = minutes + ":" + extraSeconds;
        return a;
      }/**/

    </script>

  <div class="row">
    <div class="column" style="background-color:#07a;">
      <h4 id="titluUnu"></h4>

      <!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
        <div id="playerUnu"></div>

      <div id="informațieUnu"></div>
    </div>
    <div class="column" style="background-color:#a70;">
      <h4 id="titluDoi"></h4>

      <!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
        <div id="playerDoi"></div>

        <div id="informațieDoi"></div>
      </div>
    </div>
  </body>
</html>
