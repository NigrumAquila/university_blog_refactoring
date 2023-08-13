<?php 

// function redirectWithParams(string $URL, $GETparams = '', $GETvalues = ''): void {
//     if(!empty($GETparams) AND !empty($GETvalues)) {
//         if(is_array($GETparams) AND is_array($GETvalues) AND count($GETparams) == count($GETvalues)) {
//             $URL = $URL . '?' . $GETparams[0] . '=' . $GETvalues[0];
//             if(count($GETparams) > 1) {
//                 for($i = 1; $i < count($GETparams); $i++) {
//                     $URL = $URL . "&" . $GETparams[$i] . '=' . $GETvalues[$i];
//                 }
//                 redirectTo($URL);
//                 return;
//             }
//             redirectTo($URL);
//             return;
//         }
//         redirectTo($URL . '?' . $GETparams . '=' . $GETvalues);
//         return;
//     }
//     redirectTo($URL);
// }

?>