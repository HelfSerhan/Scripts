<?php
      ob_start();
      /*
      By Helf Serhan  27/10/2016 - 23:58 
      */
      error_reporting(0);
      echo "  <p dir='ltr' align='center'><u><b><font size='4'>XFN-IDS<br>";
      echo "<font color='#FF0000'>Ransomware Connection Detected</font></font></b></p>";
      $date_past       = date('o-m-d H:i:s', time() - 5 * 60);
      $dateTime_F      = str_replace(' ', "", $date_past);
      $Alerts          = '"\"Ransomware\"' . " start:\"$dateTime_F\"\"  ";
      $Json            = ' | jq "."';
      //$output          = shell_exec("sh /opt/elsa/contrib/securityonion/contrib/cli.sh   {$Alerts} {$Json} > /tmp/Alert.log ");
      $string          = file_get_contents("/tmp/Alert.log");
      $json_a          = json_decode($string, true);
      $recordsReturned = ($json_a['recordsReturned']);
      if ($recordsReturned > '0') {
                $x = '0';
                echo "<table>";
                echo "        <th><font color=\"#000000\"><p align='left'>Src_IP</p></font></th>
                  <th><font color=\"#000000\"><p align='left'>Dst_IP</p></font></th>
                  <th><font color=\"#000000\"><p align='left'>Msg</p></font></th>
                   \n";
                while ($x <= $recordsReturned) {
                          $timestamp = $json_a["results"]["$x"]["timestamp"];
                          $SRCIP[]   = $json_a["results"]["$x"]["_fields"]["7"]["value"];
                          $DstIP[]   = $json_a["results"]["$x"]["_fields"]["5"]["value"];
                          echo '    
<tr>
<td>' . $json_a["results"]["$x"]["_fields"]["7"]["value"] . '</td>
<td>' . $json_a["results"]["$x"]["_fields"]["5"]["value"] . '</td>
<td>' . '--' . $json_a["results"]["$x"]["_fields"]["10"]["value"] . '</td>

</tr> ';
                          $x++;
                }
                echo '</table>';
                exec('cd /tmp && rm Alert.log');
                $SrcIP_array   = (array_unique($SRCIP));
                $SrcIP_array_F = array_filter($SrcIP_array);
                foreach ($SrcIP_array_F as $unique_SrcIP_array) {
                          $pieces = explode(".", $unique_SrcIP_array);
                          if ($pieces[0] == '192' or '172' or '10') {
                                    $SrcIP_output = shell_exec("nbtscan -e {$unique_SrcIP_array}");
                                    echo "<pre>$SrcIP_output</pre>";
                          }
                }
                $DstIP_array   = (array_unique($DstIP));
                $DstIP_array_F = array_filter($DstIP_array);
                foreach ($DstIP_array_F as $unique_DstIP_array) {
                          $pieces2 = explode(".", $unique_DstIP_array);
                          if ($pieces2[0] == '192' or '172' or '10') {
                                    $DstIP_output = shell_exec("nbtscan -e {$unique_DstIP_array}");
                                    echo "<pre>$DstIP_output</pre>";
                          }
                }
                $variable = ob_get_clean();
                $subject  = "Ransomware Connection Detected";
                $to       = "example@XFN-IDS";
                $headers  = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: <XFN-IDS@XFN-IDS>' . "\r\n";
                mail($to, $subject, $variable, $headers);
      }
      exec('cd /tmp && rm Alert.log');
?>
