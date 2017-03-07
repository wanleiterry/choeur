<?php

if (! function_exists('chkIpV4')) {
     function chkIpV4 ($ip) {
         if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
             return true;
         } else {
             return false;
         }
     }
}