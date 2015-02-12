/*   
     REDIRECTION.JS        Par alexbad
     29 mars 2007          alexbad@projetduweb.com
     
     Affiche les secondes restantes avant la redirection
     dans les messages.
*/
function set_tps_lef() {
     val = document.getElementById('tps').innerHTML;
     val -= 1;
     if (val > 0) {
          if (val > 1)
               document.getElementById('s').innerHTML = 's';
          else
               document.getElementById('s').innerHTML = '';
          document.getElementById('tps').innerHTML = val;
          setTimeout('set_tps_lef()', 1000);
     }
     if (val == 0) {
          document.getElementById('redirection').innerHTML = 'Attendez...';
     }     
}
