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

Class Product
{
	public $id;
	public $allfields;
	public $userid;
	public $zip;
	public $compatible;
	public $exclu;
	public $hide;
	public $version;
	public $forged;
	public $forged2;
	public $description;
	public $name;
	public $publisher;
	public $date;
	public $buy;
	public $rewardwinid;
	public $rewarduid;
	public $rewardrid;
	public $price;
	public $size;
	public $dmca;
	public $type;
	public $image;
	public $section;
	public $category;
	public $subcategory;
	public $subcategory2;
	public $subcategory3;
	public $qsection;
	public $qcategory;
	public $qsubcategory;
	public $qsubcategory2;
	public $qsubcategory3;
	public $pubdate;
	public $minunityversion;
	public $published_date;
	public $publisher_url;
    public $image_main;

	private $db;


	function __construct($id)
	{
	    global $user;
		if ($id <= 0) return false;
		$this->db = New Database();
		$sql = "SELECT * FROM products WHERE id=" . intval($id);
		$this->db->query($sql);
		if ($this->db->nbr() <= 0) return false;

		$this->db->single();
		$u = $this->db->rs;
		if ($u <> 0) {
			foreach ($u as $key => $v) {
				$this->$key = $v;
				$this->allfields[]=$key;
				//if ($user->id==1) echo "<Br>".$v." - ".$key;
			}
		}
		$this->name=_t($this->name);
		$this->id=intval($id);
		$this->description=_t($this->description);
		$this->releasenotes=_t($this->releasenotes);
		$this->compatible();
		return $this;
	}

	function __destruct()
	{
		$this->db->close();
	}

	function update()
	{
		$table = "products";
		$col = "";
		$obj = get_object_vars($this);
		//dump($obj);
		foreach ($this->allfields as $key => $value) {
			if (!is_object($value)) {
				if ($value <> "" && $col <> "id") $col .= " {$key} = '{$value}',";
				//echo $key . "-" . $value . "<br>";
			}
		}
		$col[strlen($col) - 1] = " ";

		$sql = "UPDATE {$table} SET {$col} WHERE id = '" . $this->id . "'";
		$this->db->query($sql);
		//echo $sql;exit;

	}

	function searchfilter()
	{
		global $settings;

		if ($this->id==0) return 0;
		// force compatibility

		switch($this->qsection){
			case COMPATIBLE_UNITY_ID:
				$settings->search_compatible_unity="on";
				break;
			case COMPATIBLE_UE4_ID:
				$settings->search_compatible_ue4="on";
				break;
			case COMPATIBLE_PDF_ID:
				$settings->search_compatible_pdf="on";;
				break;
			case COMPATIBLE_MOVIE_ID:
				$settings->search_compatible_movie="on";
				break;
			case COMPATIBLE_TEXTURE_ID:
				$settings->search_compatible_texture="on";
				break;
			case COMPATIBLE_3DMODEL_ID:
				$settings->search_compatible_3dmodel="on";
				break;
			case COMPATIBLE_APPLICATION_ID:
				$settings->search_compatible_app="on";
				break;
			case COMPATIBLE_AUDIO_ID:
				$settings->search_compatible_sound="on";
				break;
		}

		//if ($user->id==1) echo "compatible=".$this->compatible." - qsection=".$this->qsection;
		return $this->compatible;
	}

	function compatible()
    {


        if ($this->id==0) return 0;
        // force compatibility
	    $compatible=$this->compatible;
		switch($this->qsection){
			case COMPATIBLE_UNITY_ID:
				$this->compatible=COMPATIBLE_UNITY;
				break;
			case COMPATIBLE_UE4_ID:
				$this->compatible=COMPATIBLE_UE4;
				break;
			case COMPATIBLE_PDF_ID:
				$this->compatible=COMPATIBLE_PDF;
				break;
			case COMPATIBLE_MOVIE_ID:
				$this->compatible=COMPATIBLE_MOVIE;
				break;
			case COMPATIBLE_TEXTURE_ID:
				$this->compatible=COMPATIBLE_TEXTURE;
				break;
			case COMPATIBLE_3DMODEL_ID:
				$this->compatible=COMPATIBLE_3DMODEL;
				break;
			case COMPATIBLE_APPLICATION_ID:
				$this->compatible=COMPATIBLE_APPLICATION;
				break;
			case COMPATIBLE_AUDIO_ID:
				$this->compatible=COMPATIBLE_AUDIO;
				break;
		}
		if ($compatible<>$this->compatible) {
			$db=new Database();
			$sql = "UPDATE products SET compatible='" . $this->compatible . "' WHERE id=" . $this->id;
			$db->query($sql);
			$db->close();
		}
		//if ($user->id==1) echo "compatible=".$this->compatible." - qsection=".$this->qsection;
		return $this->compatible;
    }

	function hit()
	{
		$sql = "UPDATE products SET hits = hits + 1 where id=" . $this->id;
		$this->db->query($sql);
	}

	function updateRow($r, $s)
	{
		$sql = "UPDATE products  SET " . $r . "='" . $s . "' WHERE id=" . $this->id;;
		$this->db->query($sql);
	}

	function getNbrDownloads()
	{
		Global $settings,$user;
		$sql = "select count(*) as total  from action_logs where userid=" . $user->id . " and productid=" . $this->id .
			" and actionid=1 and action_logs.date>(UNIX_TIMESTAMP(NOW())-" . $settings->nbr_downloads_time . ")";

		$this->db->query($sql);
		$this->db->single();
		return $this->db->rs["total"];
	}

	function getNbrDownloadsFromId($pid)
	{
		Global $settings,$user;
		$sql = "select count(*) as total  from action_logs where userid=" . $user->id . " and productid=" . $pid .
			" and actionid=1 and action_logs.date>(UNIX_TIMESTAMP(NOW())-" . $settings->nbr_downloads_time . ")";

		$this->db->query($sql);
		$this->db->single();
		return $this->db->rs["total"];
	}

	function getNbrThanks()
	{

		$sql = "SELECT * FROM thanks WHERE productid='$this->id' order by id  ";
		$this->db->query($sql);
		return $this->db->numRows();
	}

	function checkProductOwner($userid)
	{
		if ($userid==$this->userid) return true; else return false;
	}

	/**
	 * getUnityVersion()
	 *
	 * @return
	 */
	function getUnityVersion()
	{
		Global $settings;
		$filename = str_replace("xxxx", $settings->download_path, $this->zip);
		$zip = new ZipArchive;
		if ($zip->open($filename) === true) {
			for ($i = 0; $i < $zip->numFiles; $i++) {
				$entry = $zip->getNameIndex($i);
				if (preg_match('#\.(unitypackage)$#i', $entry)) {
					//echo "<b>Unitypackage found=</b>".$entry."<br>";
					$fp = $zip->getStream($entry);
					if (!$fp) {
						echo "<font color=red>failed</font><br>";
					} else {
						$content = stream_get_contents($fp, 1024, -1);
						$start = strpos($content, "{");
						$content = substr($content, $start);
						// echo $content;
						preg_match_all("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is", $content, $version);
						if (strpos($content, "version") > 0)
							$or = true;
						else
							$or = false;
						if ($or) {
							$zip->close();
							return $version[0][6][1];
						} else {
							$zip->close();
							return -1;
						}

					}
					fclose($fp);
				}
			}
		}
		$zip->close();
		return -1;

	}
	/**
	 * getUnityVersion()
	 *
	 * @return
	 */
	public static function getUnityVersionFromPath($filename)
	{
		Global $settings;
		$zip = new ZipArchive;
		if ($zip->open($filename) === true) {
			for ($i = 0; $i < $zip->numFiles; $i++) {
				$entry = $zip->getNameIndex($i);
				if (preg_match('#\.(unitypackage)$#i', $entry)) {
					//echo "<b>Unitypackage found=</b>".$entry."<br>";
					$fp = $zip->getStream($entry);
					if (!$fp) {
						echo "<font color=red>failed</font><br>";
					} else {
						$content = stream_get_contents($fp, 1024, -1);
						$start = strpos($content, "{");
						$content = substr($content, $start);
						// echo $content;
						preg_match_all("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is", $content, $version);
						if (strpos($content, "version") > 0)
							$or = true;
						else
							$or = false;
						if ($or) {
							$zip->close();
							return $version[0][6][1];
						} else {
							$zip->close();
							return -1;
						}

					}
					fclose($fp);
				}
			}
		}
		$zip->close();
		return -1;

	}

	/**
	 * getZipVersion()
	 *
	 * @return
	 */
	function getZipVersion()
	{
		Global $settings;

		$filename = str_replace("xxxx", $settings->download_path, $this->zip);
		$zip = new ZipArchive;
		$idcode = abs(crc32($filename));
		if ($zip->open($filename) === true) {
			for ($i = 0; $i < $zip->numFiles; $i++) {
				$entry = $zip->getNameIndex($i);
				if (preg_match('#\.(unitypackage)$#i', $entry)) {
					echo "<b>Unitypackage found=</b>" . $entry . "<br>";
					$fp = $zip->getStream($entry);
					if (!$fp) {
						echo "<font color=red>failed</font><br>";
					} else {
						$content = stream_get_contents($fp, 1024, -1);
						$start = strpos($content, "{");
						$content = substr($content, $start);
						//echo $content;
						if (preg_match_all("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is", $content,
							$version)) {
						}
						if (strpos($content, "version") > 0)
							$or = true;
						else
							$or = false;

						if ($or) {
							echo "<font color=green>This is an original from the Asset Store</font><br>";
							?>

							<button class="btn btn" type="button" data-toggle="collapse"
							        data-target="#collapseExample<?php echo
							        $idcode; ?>" aria-expanded="false" aria-controls="collapseExample">
								Open Unityasset package header
							</button>
							<div class="collapse" id="collapseExample<?php echo $idcode; ?>">

								<?php
								dump($version);
								?>
							</div>
							<?php
						} else {
							echo "<font color=orange><b>Cannot verify the version number</b> This has been saved by the customer (not original). Can be a web rip or an old package (<2013).</font><br>";
						}
					}
					fclose($fp);
					// break;
				}
			}
			$zip->close();
		} else {
			echo " <font color=red>ZIP archive failed: $filename</font><br>";
		}

	}
	/**
	 * getUnityInfo()
	 *
	 * @return
	 */
	function getUnityInfo()
	{
		Global $settings;
		$filename = str_replace("xxxx", $settings->download_path, $this->zip);
		$zip = new ZipArchive;
		if ($zip->open($filename) === true) {
			for ($i = 0; $i < $zip->numFiles; $i++) {
				$entry = $zip->getNameIndex($i);
				if (preg_match('#\.(unitypackage)$#i', $entry)) {
					//echo "<b>Unitypackage found=</b>".$entry."<br>";
					$fp = $zip->getStream($entry);
					if (!$fp) {
						echo "<font color=red>failed</font><br>";
					} else {
						$content = stream_get_contents($fp, 1024, -1);
						$start = strpos($content, "{");
						$content = substr($content, $start);
						// echo $content;
						preg_match_all("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is", $content,
							$version);
						if (strpos($content, "version") > 0)
							$or = true;
						else
							$or = false;
						if ($or) {
							//  dump($version);exit;
							$v = getinfofromunitypackage("publisher", $version[0]);
							// echo "newv=".$v."<br>";
							$unityinfo['required_version'] = str_replace("\"", "", $version[0][getinfofromunitypackage("unity_version", $version[0])]);
							$unityinfo['published_date'] = str_replace("\"", "", $version[0][getinfofromunitypackage("pubdate", $version[0])]);
							$unityinfo['publisher'] = str_replace("\"", "", $version[0][getinfofromunitypackage("publisher", $version[0]) + 1]);
							$unityinfo['pid'] = str_replace("\"", "", $version[0][getinfofromunitypackage("id",$version[0])]);
							$unityinfo['version'] = str_replace("\"", "", $version[0][getinfofromunitypackage("version", $version[0])]);
							$unityinfo['categories'] = str_replace("\"", "", $version[0][getinfofromunitypackage("label", $version[0])]);
							$unityinfo['description'] = str_replace("\"", "", $version[0][getinfofromunitypackage("description", $version[0])]);
							$unityinfo['id'] = str_replace("\"", "", $version[0][getinfofromunitypackage("id", $version[0])]);
							$unityinfo['url'] = "https://www.assetstore.unity3d.com/en/#!/content/" . str_replace("\"", "", $version[0][getinfofromunitypackage("id", $version[0])]);
							$unityinfo['title'] = str_replace("\"", "", $version[0][getinfofromunitypackage("title", $version[0])]);
							if (strlen($unityinfo['version']) > 10) $unityinfo['version'] = str_replace("\"", "", $version[0][8]);
							if ($unityinfo['required_version'] == "link") $unityinfo['required_version'] = "< 4.0.0";
							fclose($fp);
							$zip->close();
							return $unityinfo;
						} else {
							return 0;
						}

					}
					fclose($fp);
				}
			}
		}

		$zip->close();
		return -1;

	}

	/**
	 * getUnityInfo()
	 *
	 * @return
	 */
	public static function getUnityInfoFromPath($filename)
	{
		Global $settings;
		if ($filename=="") return false;
		$zip = new ZipArchive;
		if ($zip->open($filename) === true) {
			for ($i = 0; $i < $zip->numFiles; $i++) {
				$entry = $zip->getNameIndex($i);
				if (preg_match('#\.(unitypackage)$#i', $entry)) {
					//echo "<b>Unitypackage found=</b>".$entry."<br>";
					$fp = $zip->getStream($entry);
					if (!$fp) {
						echo "<font color=red>failed</font><br>";
					} else {
						$content = stream_get_contents($fp, 1024, -1);
						$start = strpos($content, "{");
						$content = substr($content, $start);
						// echo $content;
						preg_match_all("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is", $content, $version);
						if (strpos($content, "version") > 0)
							$or = true;
						else
							$or = false;
						if ($or) {
							//dump($version);exit;
							$v = getinfofromunitypackage("publisher", $version[0]);
							// echo "newv=".$v."<br>";
							$unityinfo['required_version'] = str_replace("\"", "", $version[0][getinfofromunitypackage("unity_version", $version[0])]);
							$unityinfo['published_date'] = str_replace("\"", "", $version[0][getinfofromunitypackage("pubdate", $version[0])]);
							$unityinfo['publisher'] = str_replace("\"", "", $version[0][getinfofromunitypackage("publisher", $version[0]) + 1]);
							$unityinfo['pid'] = str_replace("\"", "", $version[0][getinfofromunitypackage("id", $version[0])]);
							$unityinfo['version'] = str_replace("\"", "", $version[0][getinfofromunitypackage("version", $version[0])]);
							$unityinfo['categories'] = str_replace("\"", "", $version[0][getinfofromunitypackage("label", $version[0])]);
							$unityinfo['description'] = str_replace("\"", "", $version[0][getinfofromunitypackage("description", $version[0])]);
							$unityinfo['id'] = str_replace("\"", "", $version[0][getinfofromunitypackage("id", $version[0])]);
							$unityinfo['url'] = "https://www.assetstore.unity3d.com/en/#!/content/" . str_replace("\"", "", $version[0][getinfofromunitypackage("id", $version[0])]);
							$unityinfo['title'] = str_replace("\"", "", $version[0][getinfofromunitypackage("title", $version[0])]);
							if (strlen($unityinfo['version']) > 10) $unityinfo['version'] = str_replace("\"", "", $version[0][8]);
							if ($unityinfo['required_version'] == "link") $unityinfo['required_version'] = "< 4.0.0";
							return $unityinfo;
						} else {
							return false;
						}

					}
					fclose($fp);
				}
			}
		}else  return false;

		$zip->close();
		return false;

	}


}

?>