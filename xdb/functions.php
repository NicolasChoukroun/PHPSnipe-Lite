<?php
/*
 *  Copyright (c) 2013-2020. Nicolas Choukroun.
 *  Copyright (c) 2013-2020. The PHPSnipe Developers.
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the Attribution 4.0 International License as published by the
 *  Creative Commons Corporation; either version 2 of the License, or (at your option)
 *  any later version.  See COPYING for more details.
 *
 ******************************************************************************/ 


function a_numbers($value, $fieldname, $primary, $row, $xcrud) {
   

    $value = a_number_formatx($value, 8, '.', "'", 3);
   
    return $value;
}

function a_blockhash($value, $fieldname, $primary, $row, $xcrud) {
   

    $v= '<a data-fancybox  
        href="javascript:;" style="text-decorations:none; color:inherit;" 
        data-type="iframe" data-src="_mac_getblock.php?hash='.$value.'">
        '.$value.'</a>';
  
    return $v;
}


function a_number_formatx($number_in_iso_format, $no_of_decimals = 3, $decimals_separator = '.', $thousands_separator = '', $digits_grouping = 3) {
    // Check input variables
    if (!is_numeric($number_in_iso_format)) {
        error_log("Warning! Wrong parameter type supplied in my_number_format() function. Parameter \$number_in_iso_format is not a number.");
        return false;
    }
    if (!is_numeric($no_of_decimals)) {
        error_log("Warning! Wrong parameter type supplied in my_number_format() function. Parameter \$no_of_decimals is not a number.");
        return false;
    }
    if (!is_numeric($digits_grouping)) {
        error_log("Warning! Wrong parameter type supplied in my_number_format() function. Parameter \$digits_grouping is not a number.");
        return false;
    }


    // Prepare variables
    $no_of_decimals = $no_of_decimals * 1;


    // Explode the string received after DOT sign (this is the ISO separator of decimals)
    $aux = explode(".", $number_in_iso_format);
    // Extract decimal and integer parts
    $integer_part = $aux[0];
    $decimal_part = isset($aux[1]) ? $aux[1] : '';

    // Adjust decimal part (increase it, or minimize it)
    if ($no_of_decimals > 0) {
        // Check actual size of decimal_part
        // If its length is smaller than number of decimals, add trailing zeros, otherwise round it
        if (strlen($decimal_part) < $no_of_decimals) {
            $decimal_part = str_pad($decimal_part, $no_of_decimals, "0");
        } else {
            $decimal_part = substr($decimal_part, 0, $no_of_decimals);
        }
    } else {
        // Completely eliminate the decimals, if there $no_of_decimals is a negative number
        $decimals_separator = '';
        $decimal_part = '';
    }

    // Format the integer part (digits grouping)
    if ($digits_grouping > 0) {
        $aux = strrev($integer_part);
        $integer_part = '';
        for ($i = strlen($aux) - 1; $i >= 0; $i--) {
            if ($i % $digits_grouping == 0 && $i != 0) {
                $integer_part .= "{$aux[$i]}{$thousands_separator}";
            } else {
                $integer_part .= $aux[$i];
            }
        }
    }

    $processed_number = "{$integer_part}{$decimals_separator}{$decimal_part}";
    return $processed_number;
}
?>