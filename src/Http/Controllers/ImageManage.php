<?php
namespace hassankwl1001\mediapanel\Http\Controllers;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use WebPConvert\WebPConvert;
use WebPConvert\Convert\Converters\Gd;
use DB;
class ImageManage{
	
	private $file;
	public $Common;
	public $Request;
	public $thumb = false;
	public $mid = false;
	public $type = "input"; // input or object
	public $path = "";
	public $name = "";
	
	function __construct($options=array()){
		$this->config($options);
	}
	
	function config($options){
		$this->file = (isset($options["file"])) ? $options["file"] : "";
		$this->Request = request();
		$this->thumb  = (isset($options["thumb"]))  ? (bool) $options["thumb"] : false;
		$this->mid  = (isset($options["mid"]))  ? (bool) $options["mid"] : false;
		$this->type = (isset($options["type"]))  ? $options["type"] : "input";
		$this->path = (isset($options["path"])) ? $options["path"] : base_path().'/images/';
		$this->name = (isset($options["name"])) ? $options["name"] : "" ;
	}
	
	function get_newHeightWidth($image, $sizes=array()){
		$width = $sizes["width"];
		$height = $sizes["height"];
		$org_width = Image::make($image)->width();
		$org_height = Image::make($image)->height();
		$imageratio = $org_width/$org_height;
		if($org_width > $org_height){
			$newwidth = $width;
			$newheight = floor(($org_height/$org_width)*$width);
		}else{
			$newwidth  = floor(($org_width/$org_height)*$height);
			$newheight = $height;
		};
		//echo "$org_width:$org_height $newwidth:$newheight";
		//die();
		return array($newwidth, $newheight);
	}
	function sanitize_title($title, $raw_title = '', $context = 'display') {
		$title = strip_tags($title);
		// Preserve escaped octets.
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		// Remove percent signs that are not part of an octet.
		$title = str_replace('%', '', $title);
		// Restore octets.
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
		$title = strtolower($title);
		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = str_replace('.', '-', $title);
		if ('save' == $context) {
			// Convert nbsp, ndash and mdash to hyphens
			$title = str_replace(array('%c2%a0', '%e2%80%93', '%e2%80%94'), '-', $title);
			// Strip these characters entirely
			$title = str_replace(array(
				// iexcl and iquest
				'%c2%a1', '%c2%bf',
				// angle quotes
				'%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
				// curly quotes
				'%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
				'%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
				// copy, reg, deg, hellip and trade
				'%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
				// acute accents
				'%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
				// grave accent, macron, caron
				'%cc%80', '%cc%84', '%cc%8c',
			), '', $title);
			// Convert times to x
			$title = str_replace('%c3%97', 'x', $title);
		}
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
		$title = preg_replace('/\s+/', '-', $title);
		$title = preg_replace('|-+|', '-', $title);
		$title = trim($title, '-');
		return $title;
	}
	function single_upload($image){
		if (is_object($image)){
			$fileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
			$fileName = $this->sanitize_title($fileName);
			$r = rand(100,999).rand(100,999).rand(100,999).rand(100,999);
			$this->name = ($this->name=="") ? $fileName."-".$r : $this->name;
			$ext = $image->getClientOriginalExtension();
			$fileName=strtolower($this->name.".".$ext);
			$destinationPath = $this->path;
			$image->move($destinationPath,$fileName);
			return $fileName;
		}
		return "";
	}
	function admin_upload($image){
		if (is_object($image)){
			$fileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
			$fileName = $this->sanitize_title($fileName);
			$this->name = ($this->name=="") ? $fileName: $this->name;
			$ext = $image->getClientOriginalExtension();
			$fileName=strtolower($this->name.".".$ext);
			$destinationPath = $this->path;
			$image->move($destinationPath,$fileName);
			return $fileName;
		}
		return "";
	}
	
	/*array(
		"mid"=>array("width"=>300,"height"=>300),
		"thumb"=>array("width"=>100,"height"=>100),
	)
	
	*/
	function upload($sizes = array()){
		$rweb = DB::table("media_setting")->where("media_key", "auto_webp")->first();
		if(isset($rweb->id)){
			$is_webp_allow = ($rweb->media_value==1) ? 1 : 0;
		}else{
			$is_webp_allow = 0;
		}
		
		$arr = array();
		if ($this->type=="input" and $this->Request->hasFile($this->file)){
			$image = $this->Request->file($this->file);
		}elseif($this->type=="object" and is_object($this->file)){
			$image = $this->file;
		}
		$arr = array();
		$fileOrgName = "";
		$selSizes = array();
		if (is_object($image)){
			$fileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
			$fileName = $this->sanitize_title($fileName);
			$fileOrgName = $fileName;
			$r = rand(100,999).rand(100,999).rand(100,999).rand(100,999);
			$this->name = ($this->name=="") ? $fileName."-".$r : $this->name;
			$ext = $image->getClientOriginalExtension();
			$thumb = $this->name."-thumb.".$ext;
			$mid = $this->name."-mid.".$ext;
			$fileName=strtolower($this->name.".".$ext);
			$destinationPath = $this->path;
			$image->move($destinationPath,$fileName);
			//Storage::putFileAs("",$this->Request->file($this->file), $fileName);
			$img=$this->path.$fileName;
			//$this->correctImageOrientation($img);
			$destination = $this->path.$this->name.".webp";
			if($is_webp_allow==1){
				if(extension_loaded('gd')){
					$info = gd_info();	
					if($info["WebP Support"]){
						$this->convert_webp($img, $destination, $ext);	
					}
				}
			}
			$all_images = array();
			$all_images["full"] = strtolower($fileName);
			if($this->is_valid_image($img)==false){
				unlink($img);	
			}else{
				try{
					$cmp = $destinationPath."/$fileName";
					$all_images["full"] = $fileName;
					if (count($sizes) > 0){
						foreach($sizes as $size){
							if($is_webp_allow==1){
								if(extension_loaded('gd')){
									$info = gd_info();
									if($info["WebP Support"]){
										$this->generate_thumb_with_webp($img, $this->path,$size, "thumb");
										$selSizes[]=  $size;
									}
								}
							}else{
								if(strtolower($ext)!="webp"){
									$this->generate_thumb($img, $this->path,$size, "thumb");
									$selSizes[]=  $size;
								}
							}
						}
					}else{
						
					}
				}catch (Exception $e) {
					unlink($img);
				}
			}
		}
		
		
		if($ext=="webp"){
			$extentions = "webp";
		}else{
			if(extension_loaded('gd') and $is_webp_allow==1){
				$info = gd_info();
				if($info["WebP Support"]){
					$extentions = "webp,$ext";
				}
			}else{
				$extentions = "$ext";	
			}
		}
		$arr = array("images" => $all_images, "name" =>$fileOrgName, "ext"=>$extentions, "sizes" => $selSizes);
		return $arr;
	}
	
	function convert_webp($source, $destination, $ext){
		if(extension_loaded('gd')){
			$info = gd_info();
			if($info["WebP Support"]){
				if($ext!="webp"){
					WebPConvert::convert($source, $destination, []);
				}
			}	
		}	
	}
	
	function ecape_name_by_source($source = ""){
		$exp = explode("/", $source);
		$filename = end($exp);
		$ext = explode(".", $filename);
		$ext = end($ext);
		$org_name = str_replace(".$ext", "", $filename);
		$arr = array(
			"name" => $org_name,
			"filename" => $filename,
			"ext" => $ext,
		);
		return $arr;
	}
	
	function generate_thumb_with_webp($source="", $destination="", $width="", $append = "", $name=""){
		if (is_file($source) and file_exists($source)){
			if ($name==""){
				$src = $this->ecape_name_by_source($source);	
			}else{
				$src = $this->ecape_name_by_source($name);
			}
			
			$append = "-".$width;
			$new_name = $src["name"].$append.".".$src["ext"];
			$destination = rtrim($destination, "/");
			$destinationP = rtrim($destination, "/")."/".$src["name"].$append.".webp";
			$destination = $destination."/".$new_name;
			Image::make($source)->resize($width, null, function($constraint){
				$constraint->aspectRatio();
			})->save($destination);
			if($src["ext"]!="webp"){
				$this->convert_webp($destination, $destinationP, $src["ext"]);
			}
			return $new_name;
		}else{
			return false;
		}
	}
	
	function generate_thumb($source="", $destination="", $width="", $append = "", $name=""){
		if (is_file($source) and file_exists($source)){
			if ($name==""){
				$src = $this->ecape_name_by_source($source);	
			}else{
				$src = $this->ecape_name_by_source($name);
			}
			
			$append = "-".$width;
			$new_name = $src["name"].$append.".".$src["ext"];
			$destination = rtrim($destination, "/");
			$destinationP = rtrim($destination, "/")."/".$src["name"].$append.".webp";
			$destination = $destination."/".$new_name;
			Image::make($source)->resize($width, null, function($constraint){
				$constraint->aspectRatio();
			})->save($destination);
			return $new_name;
		}else{
			return false;
		}
	}
	
	function correctImageOrientation($filename) {
	  if (function_exists('exif_read_data')) {
		$exif = exif_read_data($filename);
		if($exif && isset($exif['Orientation'])) {
		  $orientation = $exif['Orientation'];
		  if($orientation != 1){
			$img = imagecreatefromjpeg($filename);
			$deg = 0;
			switch ($orientation) {
			  case 3:
				$deg = 180;
				break;
			  case 6:
				$deg = 270;
				break;
			  case 8:
				$deg = 90;
				break;
			}
			if ($deg) {
			  $img = imagerotate($img, $deg, 0);        
			}
			// then rewrite the rotated image back to the disk as $filename 
			imagejpeg($img, $filename, 95);
		  } // if there is some rotation necessary
		} // if have the exif orientation info
	  } // if function exists      
	}
	
	function generate_mid($source="", $destination="", $sizes="", $append = "", $name){
		if (is_file($source) and file_exists($source)){
			
			if (is_array($sizes)){
				$width = (isset($sizes["width"])) ? $sizes["width"] : 300;
				$height = (isset($sizes["height"])) ? $sizes["height"] : 300;
			}else{
				if ($sizes=="mid" or $sizes=="medium"){
					$width = 300;
					$height = 300;
				}
			}
			
			if ($name==""){
				$src = $this->ecape_name_by_source($source);	
			}else{
				$src = $this->ecape_name_by_source($name);
			}
			
			if ($append==""){
				$append = "-".$width."x".$height;
				$new_name = $src["name"].$append.".".$src["ext"];
			}else{
				$append = "-".$append;
				$new_name = $src["name"].$append.".".$src["ext"];
			}
			$destination = rtrim($destination, "/");
			$destination = $destination."/".$new_name;
			$sizes_n = array("width"=>$width,"height"=>$height);
			$r= $this->get_newHeightWidth($source,$sizes_n);
			$new_width = $r[0];
			$new_height = $r[1];
			Image::make($source)->resize($new_width, $new_height)->save($destination);
			return $new_name;
		}else{
			return false;
		}
	}
	
	
	function onTimeUpload($field, $user_id=""){
		$user_folder  = "3$user_id";
		if (request()->hasFile($field)) {
			$path = base_path()."/images/".$user_folder."/temp/";
			$image = request()->file($field);
			$fileSize = request()->file($field)->getSize() / 1024;
			$fileLimitMax = config("dg.limit.image_upload_max_size");
			$fileLimitMin = config("dg.limit.image_upload_min_size");
			if ($fileSize > $fileLimitMax){
				$resp = array("resp"=>"error", "msg"=>"Image size is high.");
			}elseif ($fileSize < $fileLimitMin){
				$resp = array("resp"=>"error", "msg"=>"Image size is low.");
			}else{
				$exp = explode("_",request("name"));
				$org_counter = $exp[1];
				$filename = request()->file($field)->getClientOriginalName();
				$filename = unique_filename(base_path().'/images/'.$user_folder."/", $filename);
				
				$slg = strtolower($filename);
				$options["request"] = request();
				$options["file"] = "dropImage";
				$options["path"] = $path;
				$options["type"] = "input";
				$options["thumb"] = false;
				$options["mid"] = false;
				$options["name"] = "$org_counter"."_$slg";	
				if (file_exists($path) and $handle = opendir($path)) {
					while (false !== ($entry = readdir($handle))) {
						if ($entry != "." && $entry != "..") {
							$exp = explode("_", $entry);
							$ct = $exp[0];
							$ext = end($exp);
							$c_f = str_replace(".$ext","",$entry);
							if ($org_counter==$ct){
								if (file_exists($path.$entry)){
									unlink($path.$entry); // Full Image
								}
								if (file_exists($path.$c_f."-mid.$ext")){
									unlink($path.$c_f."-mid.$ext"); // Mid Image
								}
								if (file_exists($path.$c_f."-thumb.$ext")){
									unlink($path.$c_f."-thumb.$ext"); // Thumb Image
								}
							}
						}
					}
					closedir($handle);
				}
				$this->config($options);
				$sizes = array(
					"mid"=>array("width"=>250,"height"=>130),
					"thumb"=>array("width"=>100,"height"=>100),
				);
				$up = $this->upload($sizes);
				$resp = array("resp"=>"success", "msg"=>$up["full"]);
			}
		}else{
			$resp = array("resp"=>"error", "msg"=>"Something is wrong.");
		}
		echo json_encode($resp);
	}
	
	function upError($field, $folder){
		if (request()->hasFile($field)) {
			$path = base_path()."/images/".$folder."/temp/";
			$image = request()->file($field);
			$fileSize = request()->file($field)->getSize() / 1024;
			$fileLimitMax = config("dg.limit.image_upload_max_size");
			$fileLimitMin = config("dg.limit.image_upload_min_size");	
			if ($fileSize > $fileLimitMax){
				$resp = array("resp"=>"error", "msg"=>"Image size is high.");
			}elseif ($fileSize < $fileLimitMin){
				$resp = array("resp"=>"error", "msg"=>"Image size is low.");
			}else{
				$resp = array("resp"=>"success", "msg"=>"Success");
			}
		}else{
			$field = str_replace("-", " ", $field);
			$field = str_replace("_", " ", $field);
			$field = ucwords($field);
			$resp = array("resp"=>"error", "msg"=>"$field is empty");
		}
		return $resp;
	}
	
	function single($field, $user_id=""){
		$user_folder  = "3$user_id";
		if (request()->hasFile($field)) {
			$path = base_path()."/images/".$user_folder."/";
			$image = request()->file($field);
			$fileSize = request()->file($field)->getSize() / 1024;
			$fileLimitMax = config("dg.limit.image_upload_max_size");
			$fileLimitMin = config("dg.limit.image_upload_min_size");
			if ($fileSize > $fileLimitMax){
				$resp = array("resp"=>"error", "msg"=>"Image size is high.");
			}elseif ($fileSize < $fileLimitMin){
				$resp = array("resp"=>"error", "msg"=>"Image size is low.");
			}else{
				$filename = request()->file($field)->getClientOriginalName();
				$filename = unique_filename(base_path().'/images/'.$user_folder."/", $filename);
				
				$slg = $filename;
				$options["request"] = request();
				$options["file"] = $field;
				$options["path"] = $path;
				$options["type"] = "input";
				$options["thumb"] = false;
				$options["mid"] = false;
				$options["name"] = $slg;	
				$this->config($options);
				$up = $this->upload([]);
				$resp = array("resp"=>"success", "msg"=>$up["full"]);
			}
		}else{
			$resp = array("resp"=>"error", "msg"=>"Something is wrong.");
		}
		return $resp;
	}
	
	function is_valid_image($image = ""){
		$s = false;
		if (is_file($image)){
			$allowedMimeTypes = ['image/jpeg','image/gif','image/png','image/bmp', 'image/webp'];
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$contentType = finfo_file($finfo, $image);
			if(in_array($contentType, $allowedMimeTypes) ){
				$s = true;	
			}
		}
		return $s;
	}
}