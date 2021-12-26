 <?php
              $json = file_get_contents('http://us.data.vatsim.net/vatsim-data.json');
              $decoded = json_decode($json, true);

              foreach ($decoded['clients'] as $user) {

                 $cid = $user['cid'];
                 $callsign = $user['callsign'];
                // $type = $user['plan']['aircraft'];
                // $altitude = $user['altitude'];
                // $speed = $user['speed'];
                // $arrival = $user['plan']['arrival'];
                //
                // echo $cid . "<br />";
                // echo $callsign . "<br />";
                // echo $type . "<br />";
                // echo $altitude . "<br />";
                // echo $speed . "<br />";
                // echo $arrival . "<br /><br /><br />";
              }

              echo $callsign;

?>