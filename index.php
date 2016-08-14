<?php
/*

Jared's simple web based ping tool
http://

*/
if (isset($_GET['pong'])) {
    echo microtime(true);
} else {
?><!doctype html>
<html>
<head>
<style>
* {

}

th {
  text-align: left;
}
td {
  text-align: right;
}

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>

function show_rounded(number) {
  num_in = number;
  return num_in.toFixed(1);
}

$(function() {
    var last = 0;
    var recent_low = 0;
    var recent_high = 0;
    var session_low = 0;
    var session_high = 0;
    var ping_ms = 0;
    var count = 0;
    var entries = 75;
    var padding = 12;
    var recent_pings = Array();
    var rsum = 0;
    var full_red_ping_ms = 200;
    var failed_pings = 0;

    function ping() {
        $.get('?pong=1', function(data) {
             //window.alert('hi');
            if(last != 0) {
                count++;
                ping_ms = ((data - last) * 1000);
                recent_pings[(count - 1) % entries] = ping_ms;
                if(count>=3){
                    if(count>=entries) $('ul li:last').remove();
                    rsum = recent_pings.reduce(function(a,b) { return a + b; });
                    ravg = rsum / Math.min(count, entries);
                    recent_low = Math.min.apply(null, recent_pings);
                    recent_high = Math.max.apply(null, recent_pings);
                    if(session_low == 0) {
                      session_low = recent_low;
                      session_high = recent_high;
                    } else {
                      session_high = Math.max(session_high, recent_high);
                      session_low = Math.min(session_low, recent_low);

                    }
                    $("#recent_avg").html(show_rounded(ravg));
                    $("#recent_low").html(show_rounded(recent_low));
                    $("#recent_high").html(show_rounded(recent_high));
                    $("#session_low").html(show_rounded(session_low));
                    $("#session_high").html(show_rounded(session_high));
                }
                $("#ping_count").html(count);
                $("#failed_pings").html(failed_pings);

                var color_ping = Math.min(ping_ms, full_red_ping_ms);
                var color_red = Math.round(color_ping / full_red_ping_ms * 255);
                var color_green = 255-Math.round(color_ping / full_red_ping_ms * 255);

                // var color_green = 15 - int(Math.min(ping_ms, 500) / 500 * 15);
                //var line_color = color_red.toString(16) + color_green.toString(16) + "0";
                var line_color = "";

                //begin rgb solver
                // red
                if(color_red < 16)  line_color += "0";
                line_color += color_red.toString(16);
                // green
                line_color += "00";
                // blue
                if(color_green < 16)  line_color += "0";
                line_color += color_green.toString(16);
                //done rgb

                var str_out = show_rounded(ping_ms);
                str_out += Array(padding - (str_out.length)).join("&nbsp;");
                str_out += Array( Math.round(ping_ms) ).join("*");
                $('#pings').prepend('<li style="color:#' + line_color + '">' + str_out + '</li>');
            }
            last = data;
            ping();
        }).fail(function () {
            failed_pings++;
            count++;
            $("#ping_count").html(count);
            $("#failed_pings").html(failed_pings);
            setTimeout(ping, 1000);
        });
    }

    ping();
    // todo: try $.ping
    //setInterval(ping(), 1000);


});
</script>
<title>jared's pjs ping</title>
</head>
<body>
<h1>jared's <abbr title="php and js">pjs</abbr> ping</h1>
<table>
  <tr>
    <th>pings:<th>
    <td id="ping_count" style="width:60px;">0</td>
    <td colspan="2" style="width:30px;">&nbsp;</td>
    <th>failed pings:<th>
    <td id="failed_pings" style="width:60px;">0</td>
  </tr>
  <tr>
    <th>recent average:<th>
    <td id="recent_avg">~</td>
    <td>ms</td>
  </tr>
  <tr>
    <th>recent low:<th>
    <td id="recent_low">~</td>
    <td>ms</td>
    <td style="width:30px;">&nbsp;</td>
    <th>session low:<th>
    <td id="session_low" style="width:60px;">~</td>
    <td>ms</td>
  </tr>
  <tr>
    <th>recent high:<th>
    <td id="recent_high">~</td>
    <td>ms</td>
    <td>&nbsp;</td>
    <th>session high:<th>
    <td id="session_high">~</td>
    <td>ms</td>
  </tr>
</table>
<ul id="pings" style="font-family: monospace">
<li>get ready, get set</li>
</ul>
</body>
</html><?php
}
