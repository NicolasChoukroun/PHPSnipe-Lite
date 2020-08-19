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


$con_gb=0; //  global to avoid duplicating the class too much

class Database
    {
    var $Host     = DB_HOST;        // Hostname of our MySQL server.
    var $Database = DB_NAME;         // Logical database name on that server.
    var $User     = DB_USER;             // User and Password for login.
    var $Password = DB_PASSWORD;
    var $table="settings";

    var $con  ;                  // Result of mysql_connect().
    var $result ;                  // Result of most recent mysql_query().
    var $Record   = array();            // current mysql_fetch_array()-result.
    var $rs   = array();            // current mysql_fetch_array()-result.    
    var $Row;                           // current row number.
    var $LoginError = "";

    var $Errno    = 0;                  // error state of query...
    var $Error    = "";


	//-------------------------------------------
	//    Connects to the database
	//-------------------------------------------
   
    function connect()
    {
		global $con_gb;
		//if ($con_gb!=0) $this->con=$con_gb;
        if( !isset($this->con))	 {
            $this->con=mysqli_connect( $this->Host, $this->User, $this->Password,$this->Database );
            // check connection
            if (mysqli_connect_errno()) {
                printf("Database Connect failed: %s\n", mysqli_connect_error());
                exit();
            }
        }
		mysqli_set_charset($this->con,'utf8'); 
        
   } // end function connect
   
	//-------------------------------------------
	//    Queries the database
	//-------------------------------------------
    function query( $sql,$cond="")
    {
    	Global $settings,$user;

    	//if ($user->userid>0) {
	    $t=time();
	    //}
        $this->connect();
		//$Query_String=$this->escapeMimic($Query_String);
        /*if ($cond<>"") {
	        $this->result = mysqli_query($this->con, $cond);
            if ( $this->result===false )
            {
                $this->Row = 0;
                $this->Errno = mysqli_errno($this->con);
                if( !$this->result ) $this->halt( "Invalid SQL: ".$cond );
            }else{
                if (mysqli_num_rows( $this->result )) {
                    $this->Row = 0;
                    return false;
                }
            }
	        return $this->result;
        }*/
	    if ($sql=="") return false;
	    $this->result = @mysqli_query($this->con, $sql);
        if ( $this->result ===false )
        {
            $this->Row = 0;
            $this->Errno = mysqli_errno($this->con);
            if( !$this->result ) $this->halt( "Invalid SQL: ".$sql );
        }

        $tt = time() - $t;
        if ($tt>1) {
		    $log= "\nuser=".$user->userid." - " . $tt . " seconds sql=" . $sql." - referer: ".$_SERVER['HTTP_REFERER'];
		    //logtofile($log);

	    }
        return $this->result;

    } // end function query



    //-------------------------------------------
    //   prepare the SQL depending on the type of the data
    //-------------------------------------------
	function prepSQL($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
	{
	  $theValue = str_replace('\'',' ',$theValue);
	  $theValue = str_replace('\r\n','<br>',$theValue);
	   if ($theType=="date")
	   {   $theValue = str_replace('/','-',$theValue);
			if (!$this->valid_date($theValue))
			{
				trim($theValue);
				list($d, $m, $y) = explode('-', $theValue);
				$mk = mktime(0,0,0,$m, $d, $y);
				$theValue =strftime('%Y-%m-%d',$mk);
			}
	   }
	  switch ($theType) {
		case "text":
		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		  break;
		case "long":
		case "int":
		  $theValue = ($theValue != "") ? intval($theValue) : 0;
		  break;
		case "double":
		  $theValue = ($theValue != "") ? "'" . floatval($theValue) . "'" : "NULL";
		  break;
		case "date":
		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		  break;
		case "defined":
		  $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
		  break;
	  }
	  return $theValue;
	}


	function valid_date($date, $format = 'YYYY-MM-DD'){
		if(strlen($date) >= 8 && strlen($date) <= 10){
			$separator_only = str_replace(array('M','D','Y'),'', $format);
			$separator = $separator_only[0];
			if($separator){
				$regexp = str_replace($separator, "\\" . $separator, $format);
				$regexp = str_replace('MM', '(0[1-9]|1[0-2])', $regexp);
				$regexp = str_replace('M', '(0?[1-9]|1[0-2])', $regexp);
				$regexp = str_replace('DD', '(0[1-9]|[1-2][0-9]|3[0-1])', $regexp);
				$regexp = str_replace('D', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
				$regexp = str_replace('YYYY', '\d{4}', $regexp);
				$regexp = str_replace('YY', '\d{2}', $regexp);
				if($regexp != $date && preg_match('/'.$regexp.'$/', $date)){
					foreach (array_combine(explode($separator,$format), explode($separator,$date)) as $key=>$value) {
						if ($key == 'YY') $year = '20'.$value;
						if ($key == 'YYYY') $year = $value;
						if ($key[0] == 'M') $month = $value;
						if ($key[0] == 'D') $day = $value;
					}
					if (checkdate($month,$day,$year)) return true;
				}
			}
		}
		return false;
	}


//-------------------------------------------
//    If error, halts the program
//-------------------------------------------
    function halt( $msg )
    {
        printf( "<strong>Database error:</strong> %sn", $msg );
        printf( " <strong>MySQL Error</strong>: %s (%s)n", $this->Errno, $this->Error );
        die( "Session halted." );
    } // end function halt

//-------------------------------------------
//    Retrieves the next record in a recordset
//-------------------------------------------
    function nextRecord(){
        @$this->Record = mysqli_fetch_array( $this->result,MYSQLI_ASSOC );
        $this->rs=$this->Record;
        $this->Row += 1;
        $this->Errno = mysqli_errno($this->con);
        $this->Error = mysqli_error($this->con);
        $stat = is_array( $this->Record );
        if( !$stat )
        {
            @mysqli_free_result( $this->result );
            $this->result = 0;
        }
        return $stat;
    } // end function nextRecord
    
    function next(){
        @$this->Record = mysqli_fetch_array( $this->result,MYSQLI_ASSOC );
        $this->rs=$this->Record;
        $this->Row += 1;
        $this->Errno = mysqli_errno($this->con);
        $this->Error = mysqli_error($this->con);
        $stat = is_array( $this->Record );
        if( !$stat )
        {
            @mysqli_free_result( $this->result );
            $this->result = 0;
        }
        return $stat;
    } // end function nextRecord    

//-------------------------------------------
//    Retrieves a single record
//-------------------------------------------
    function singleRecord()
    {
        $this->Record = mysqli_fetch_array( $this->result,MYSQLI_ASSOC );
        $this->rs=$this->Record;
        $stat = is_array( $this->Record );
        return $stat;
    } // end function singleRecord
        
    function single()
    {
        $this->Record = mysqli_fetch_array( $this->result,MYSQLI_ASSOC );
        $this->rs=$this->Record;
        $stat = is_array( $this->Record );
        return $stat;
    } // end function singleRecord        

//-------------------------------------------
//    Returns the number of rows  in a recordset
//-------------------------------------------
    function numRows()
    {
        return mysqli_num_rows( $this->result );
    } // end function numRows
        
    function nbrRows()
    {
        return mysqli_num_rows( $this->result );
    } // end function numRows

    function nbr()
    {

        return mysqli_num_rows( $this->result );
    } // end function numRows

//-------------------------------------------
//    Returns the Last Insert Id
//-------------------------------------------
    function lastId()
    {
        return mysqli_insert_id($this->con);
    } // end function numRows



//-------------------------------------------
//    Returns Escaped string
//-------------------------------------------
    function mysqlEscapeMimic($inp)
    {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);
        if(!empty($inp) && is_string($inp)) {
            return str_replace(['\\', "\0", "\n", "\r", "'", '"', "\x1a"], ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'], $inp);
        }
        return $inp;
    }

    function escapeMimic($inp)
    {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);
        if(!empty($inp) && is_string($inp)) {
            return str_replace(['\\', "\0", "\n", "\r", "'", '"', "\x1a"], ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'], $inp);
        }
        return $inp;
    }
    function clean($inp)
    {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);
        //$inp=str_replace("&","and",$inp);
        //$inp=str_replace(":"," ",$inp);
        
        if(!empty($inp) && is_string($inp)) {
            $inp= str_replace(['\\', "\0", "\n", "\r", "'", '"', "\x1a"], ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'], $inp);
        }

        
        //$inp = mysqli_real_escape_string($this->con, $inp );
        return $inp;
    }

	function escape($text) {
		return mysqli_real_escape_string($this->con, $text);
	}
//-------------------------------------------
//    Returns the number of fields in a recordset
//-------------------------------------------
    function numFields()
    {
            return mysqli_num_fields($this->result);
    } // end function numRows

    function close()
    {
        mysqli_close($this->con);
    }

} // end class Database
?>