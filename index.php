<?php

/*

GPIO USATI:

23: ----------------> RELE 1
24: ----------------> RELE 2
25: ----------------> RELE 3
26: ----------------> RELE 4

*/


?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://unpkg.com/onsenui/css/onsenui.css">
  <link rel="stylesheet" href="https://unpkg.com/onsenui/css/onsen-css-components.min.css">
  <script src="https://unpkg.com/onsenui/js/onsenui.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="moment.js"></script>

  <script>
    //da leggere da config del server.
    var schedulazione_apertura=false;
    var schedulazione_chiusura=false;
    var orario_apertura_schedulata="07:00";
    var orario_chiusura_schedulata="20:30";
    var log_azioni =[];
    var sportello_aperto=true;
    //----------------------------------
    
    var rele_gpio = [23,24,25,26];
    var rele_aperto = [];
    for (var i = 0; i < 4; i++) {
      rele_aperto.push(false);
    }

    var scriviLOG = function(testo) {
      log_azioni.push(moment().format("YYYY-MM-DD HH:mm:ss") + " - " + testo);
    }

    var aggiornaLog = function() {
      var testo = log_azioni.toString();
      testo = testo.replace(/,/g,"<br>"); // la sintassi /,/g serve per effettuare la sostituzione di tutte le occorrenze e non solo della prima... :/
      $("#lbl_log").html(testo);
    }

    var aggiornaData = function() {
      //console.log("- aggiornaData() - " + Date.now());
      $.ajax({ url : "data_esatta.php",
            dataType: "text",
            success : function (data) {
                $("#dd_data").text(data);
            }
      });
    };

    var aggiornaOra = function() {
      //console.log("- aggiornaOra() - " + Date.now());
      $.ajax({ url : "ora_esatta.php",
            dataType: "text",
            success : function (data) {
                $("#dd_ora").text(data);
            }
      });
    };

    var aggiornaTemperatura = function() {
      //console.log("- aggiornaTemperatura() - " + Date.now());
      $.ajax({ url : "temp_read.php",
            dataType: "text",
            success : function (data) {
              var valore=parseInt(data);
              valore=valore/1000;
                $("#dd_temperatura_CPU").text(valore);
            }
      });
    };

    var aggiornaFoto = function() {
      //console.log("- aggiornaFoto() - " + Date.now());
      $("#dd_foto").attr("src","camera.php?ts="+Date.now());
    };

    var aggiornaStatoRele = function() {
      //console.log("- aggiornaStatoRele() - " + Date.now());
      for (var i = 0; i < 4; i++) {
        //console.log("ajax: gpio_read.php?id="+ rele_gpio[i]);
        $.ajax({ url : "gpio_read.php?id="+ rele_gpio[i],
            dataType: "text",
            success : function (data) {
              var valore=parseInt(data);
              
              console.log("lettura elettronica --> " + url + " == " + valore)

              if(valore==1)
              {
                rele_aperto[i]=true;
              }
              else
              {
                rele_aperto[i]=false;
              }
            }
      });
      }
    }

    var azionaRele = function(numero) {
      scriviLOG("Azionato Relè n. " + numero);
      if (rele_aperto[numero-1]==true)
      {
        rele_aperto[numero-1]=false;
        $("#dd_stato_rele" + numero).attr("checked","false");
      }
      else
      {
        rele_aperto[numero]=true;
        $("#dd_stato_rele" + numero).attr("checked","true");
      }
      showToast("Azionato Relé n." + numero);
    };
    
    var azionaSportello = function() {
      scriviLOG("Azionato Sportello");
      showToast("Azionato Sportello! Attendere...");
    };

    var apertura_schedulata = function(comando) {
      //console.log("- apertura_schedulata() - " + Date.now());
      //controlla se è stato attivato o disattivato lo switch
      if (document.querySelector('#dd_sw_apertura_automatica')!=null)
      {
        if (document.querySelector('#dd_sw_apertura_automatica').checked)
        {
          if (comando=='switch')
            scriviLOG("Apertura automatica sportello - Abilitata!");
          
          schedulazione_apertura=false;
        }
        else
        {
          if (comando=='switch')
            scriviLOG("Apertura automatica sportello - Abilitata!");
          
          schedulazione_apertura=true;
        }
        document.querySelector('#btn_apertura_meno').disabled=schedulazione_apertura;
        document.querySelector('#btn_apertura_piu').disabled=schedulazione_apertura;
      }
      //se attivo, abilita i bottoni di selezione orario
      if (comando=="-")
      {
        var tempo = moment('1970-01-01 ' + orario_apertura_schedulata);
        tempo.subtract(15,'m');
        orario_apertura_schedulata =tempo.hour().toString().padStart(2,"0") + ":" + tempo.minute().toString().padStart(2,"0");
        scriviLOG("Apertura automatica sportello - Nuovo orario: " + orario_apertura_schedulata);
      }
      if (comando=="+")
      {
        var tempo = moment('1970-01-01 ' + orario_apertura_schedulata);
        tempo.add(15,'m');
        orario_apertura_schedulata =tempo.hour().toString().padStart(2,"0") + ":" + tempo.minute().toString().padStart(2,"0");
        scriviLOG("Apertura automatica sportello - Nuovo orario: " + orario_apertura_schedulata);
      }
      //aggiorna la label con il valore della variabile
      $("#lbl_orario_apertura").text(orario_apertura_schedulata);
    }
    
    var chiusura_schedulata = function(comando) {
      //console.log("- chiusura_schedulata() - " + Date.now());
      //controlla se è stato attivato o disattivato lo switch
      if (document.querySelector('#dd_sw_chiusura_automatica')!=null)
      {
        if (document.querySelector('#dd_sw_chiusura_automatica').checked)
        {
          if (comando=='switch')
            scriviLOG("Chiusura automatica sportello - Abilitata!");

          schedulazione_chiusura=false;
        }
        else
        {
          if (comando=='switch')
            scriviLOG("Chiusura automatica sportello - Abilitata!");

          schedulazione_chiusura=true;
        }
        document.querySelector('#btn_chiusura_meno').disabled=schedulazione_chiusura;
        document.querySelector('#btn_chiusura_piu').disabled=schedulazione_chiusura;
      }

      //se attivo, abilita i bottoni di selezione orario
      if (comando=="-")
      {
        var tempo = moment('1970-01-01 ' + orario_chiusura_schedulata);
        tempo.subtract(15,'m');
        orario_chiusura_schedulata =tempo.hour().toString().padStart(2,"0") + ":" + tempo.minute().toString().padStart(2,"0");
        scriviLOG("Apertura automatica sportello - Nuovo orario: " + orario_apertura_schedulata);
      }
      if (comando=="+")
      {
        var tempo = moment('1970-01-01 ' + orario_chiusura_schedulata);
        tempo.add(15,'m');
        orario_chiusura_schedulata =tempo.hour().toString().padStart(2,"0") + ":" + tempo.minute().toString().padStart(2,"0");
        scriviLOG("Apertura automatica sportello - Nuovo orario: " + orario_apertura_schedulata);
      }
      //se abilitati, aggiorna la label con il valore della variabile
      $("#lbl_orario_chiusura").text(orario_chiusura_schedulata);
    }

    var showToast = function(testo) {
      ons.notification.toast(testo, {
        timeout: 2000
      });
    };
    
    $(function() {
      scriviLOG("Avvio Applicazione");
      $("#dd_titolo").text("Pollaio Domotico");
        
      aggiornaFoto();
      aggiornaOra();
      aggiornaData();
      aggiornaTemperatura();
      apertura_schedulata();
      chiusura_schedulata();
      aggiornaLog();

      window.setInterval(function() { aggiornaFoto() },5000);
      window.setInterval(function() { aggiornaOra() },1000);
      window.setInterval(function() { aggiornaData() },3600000);
      window.setInterval(function() { aggiornaTemperatura() },1000);
      window.setInterval(function() { apertura_schedulata() },1000);
      window.setInterval(function() { chiusura_schedulata() },1000);
      window.setInterval(function() { aggiornaLog() },500);

    });

 </script>
</head>
<body>
<ons-page>
  <ons-toolbar>
    <div class="left"><image src="pollo.png" width="30" height="42" style="margin-left:10px"></div>
    <div class="center" id="dd_titolo">Attendere...</div>
    <div class="right"><image src="pollo.png" width="30" height="42" style="margin-right:10px"></div>
  </ons-toolbar>

  <ons-tabbar swipeable position="auto">
    <ons-tab page="stato.html" label="Stato" icon="ion-home" active-icon="ion-home" active>
    </ons-tab>
    <ons-tab page="comandi_manuali.html" label="Manuali" icon="md-walk" active-icon="md-walk">
    </ons-tab>
    <ons-tab page="comandi_schedulati.html" label="Schedulati" icon="md-alarm" active-icon="md-alarm">
    </ons-tab>
    <ons-tab page="log.html" label="Eventi" icon="md-book" active-icon="md-book">
    </ons-tab>
  </ons-tabbar>
</ons-page>

<template id="stato.html">
  <ons-page id="Stato">
    <ons-list>
      <ons-list-header style="text-align: center;">Stato del sistema&nbsp;-&nbsp;<label id="dd_data" style="color: blue;">00/00/0000</label>&nbsp;-&nbsp;Ora:&nbsp;<label id="dd_ora" style="color: blue;">00:00:00</label></ons-list-header>
      <ons-list-item>Temperatura CPU:&nbsp;<label id="dd_temperatura_CPU" style="color: green;">0</label>&nbsp;°C</ons-list-item>
      <ons-list-item>Stato portello:&nbsp;<label id="dd_stato_sportello" style="color: blue;">Lettura in corso</label></ons-list-item>
      <ons-list-item>Relé:
        &nbsp;<ons-icon id="dd_status_rele1" icon="md-brightness-7" style="color: red;">
        &nbsp;<ons-icon id="dd_status_rele2" icon="md-brightness-7" style="color: red;">
        &nbsp;<ons-icon id="dd_status_rele3" icon="md-brightness-7" style="color: red;">
        &nbsp;<ons-icon id="dd_status_rele4" icon="md-brightness-7" style="color: red;">
      </ons-list-item>
      <ons-list-item><image id="dd_foto" width="370" height="370" src="pollo.png" style="margin-left: -12px;"></ons-list-item>
    
    </ons-list>
  </ons-page>
</template>

<template id="comandi_manuali.html">
  <ons-page id="Manuali">
    <p style="text-align: center;">
      Comandi Manuali
    </p>
    <ons-list>
    <ons-list-item>
        <div class="center">
          Comando Sportello
        </div>
        <div class="right">    
          <ons-button disabled="true" onclick="azionaSportello();">Apri</ons-button>
          &nbsp;
          <ons-button onclick="azionaSportello();">Chiudi</ons-button>
        </div>
      </ons-list-item>
      <ons-list-item>
        <div class="center">
          Comando Relè 1
        </div>
        <div class="right">
          <ons-switch id="dd_sw_rele1" onclick="azionaRele(1);"></ons-switch>
        </div>
      </ons-list-item>
      <ons-list-item>
        <div class="center">
          Comando Relè 2
        </div>
        <div class="right">
          <ons-switch id="dd_sw_rele2" onclick="azionaRele(2);"></ons-switch>
        </div>
      </ons-list-item>
      <ons-list-item>
        <div class="center">
          Comando Relè 3
        </div>
        <div class="right">
          <ons-switch id="dd_sw_rele3" onclick="azionaRele(3);"></ons-switch>
        </div>
      </ons-list-item>
      <ons-list-item>
        <div class="center">
          Comando Relè 4
        </div>
        <div class="right">
          <ons-switch id="dd_sw_rele4" onclick="azionaRele(4);"></ons-switch>
        </div>
      </ons-list-item>
      </ons-list>
  </ons-page>
</template>

<template id="comandi_schedulati.html">
  <ons-page id="Schedulati">
    <p style="text-align: center;">
      Comandi Schedulati
    </p>
    <ons-list>
      <ons-list-item>
        <div class="left">
          <ons-switch id="dd_sw_apertura_automatica" onclick="apertura_schedulata('switch');"></ons-switch>
        </div>
        <div class="center">
          Apertura Automatica:
        </div>
        <div class="right">
        <ons-button id="btn_apertura_meno" onclick="apertura_schedulata('-');">-</ons-button>&nbsp;&nbsp;<label id="lbl_orario_apertura">XX:XX</label>&nbsp;&nbsp;<ons-button id="btn_apertura_piu" onclick="apertura_schedulata('+');">+</ons-button>
        </div>
      </ons-list-item>
      <ons-list-item>
        <div class="left">
          <ons-switch id="dd_sw_chiusura_automatica" onclick="chiusura_schedulata('switch');"></ons-switch>
        </div>
        <div class="center">
          Chiusura Automatica:
        </div>
        <div class="right">
        <ons-button id="btn_chiusura_meno" onclick="chiusura_schedulata('-');">-</ons-button>&nbsp;&nbsp;<label id="lbl_orario_chiusura">XX:XX</label>&nbsp;&nbsp;<ons-button id="btn_chiusura_piu" onclick="chiusura_schedulata('+');">+</ons-button>
        </div>
      </ons-list-item>
  </ons-page>
</template>


<template id="log.html">
  <ons-page id="Eventi">
    <p style="text-align: center; margin-left: 5px; margin-right: 5px;">
      Riepilogo eventi
    </p>
    <label id="lbl_log" style="font-size: x-small;">LOG</label>
  </ons-page>
</template>

</body>
</html>
