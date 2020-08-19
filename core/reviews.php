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


Class Reviews
{


	private $db;

	function __construct($id)
	{
		if ($id <= 0) return false;
		$this->db = New Database();
		$sql = "SELECT * FROM news WHERE id=" . intval($id);
		$this->db->query($sql);
		if ($this->db->numRows() <= 0) return false;
		$this->db->single();
		$u = $this->db->rs;
		if ($u <> 0) {
			foreach ($u as $key => $v) {
				$this->$key = $v;
				$this->allfields[]=$key;
			}
		}

		return $this;
	}

	function __destruct()
	{
		$this->db->close();
	}

	function update()
	{
		$table = "news";
		$col = "";
		$obj = get_object_vars($this);
		//dump($obj);
		foreach ($this->allfields as $key => $value) {
			if (!is_object($value)) {
				if ($value <> "" && $col <> "id") $col .= " {$key} = '{$value}',";
			}
		}
		$col[strlen($col) - 1] = " ";

		$sql = "UPDATE {$table} SET {$col} WHERE id = '" . $this->id . "'";
		$this->db->query($sql);

	}
}

?>