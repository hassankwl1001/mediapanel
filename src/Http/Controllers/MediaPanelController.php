<?php
//dgaps\mediapanel\src\Http\Controllers\MediaPanelController.php
namespace hassankwl1001\mediapanel\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
//use dgaps\mediapanel\Models\Media;

class MediaPanelController extends Controller{
	private $_limit;
	function __construct(){
		$this->_limit = 16;
	}
	public function index(){
		request("action");
		if(request()->has("action")){
			return $this->loadTemplate();
		}else{
			$settings = $this->getSetting();
			return view("mediapanel::index", compact("settings"));	
		}
		
	}
	
	public function loadTemplate(){
		$data = $this->media("load");
		$data["up_path"] = base_path().'/images/';
		return view("mediapanel::template", $data);
	}
	
	public function media($g=""){
		$data = $this->get_images(0);
		$data["up_path"] = base_path().'/images/';
		if($g=="load"){
			return $data;
		}else{
			return view("mediapanel::images", $data);
		}
	}
	
	public function parseImages($v){
		$up_path = base_path().'/images/';
		$images_data = array();
		$images = json_decode($v->images, true);
		$image = $images["full"];
		$allowed = explode("|","jpg|jpeg|gif|png|webp");
		$exp=explode(".", $image);
		$file_type="image";
		$info = explode(".",$image);
		$file_ext = strtolower(end($info));
		$allsizes = json_decode($v->sizes, true);
		$sizes = array();
		foreach($allsizes as $vsize){
			$sizes[] = $vsize;
		}
		sort($sizes);
		if (in_array($file_ext, $allowed)){
			$f = explode(".",$image);
			$file_name = str_replace(".".end($f),"", $image); 
			$name = str_replace(".".end($f),"", $image);
			$name = str_replace("-", " ",$name);
			$name = str_replace("_", " ",$name);
			$name = str_replace("thumb","",$name);
			$name = ucwords($name);
			if (file_exists($up_path.$image)){
				$types = explode(",", $v->type);
				$sizes = json_decode($v->sizes, true);
				$image_info = array();
				$image_info["id"] = $v->id;
				$image_info["title"] = $v->title;
				$image_info["alt"] = $v->alt;
				$image_info["caption"] = $v->caption;
				$image_info["description"] = $v->description;
				$image_info["created_at"] = $v->created_at;
				$image_info["updated_at"] = $v->updated_at;
				$image_info["folders"] = explode(",", $v->folder);
				$image_info["types"] = $types;
				$sizes[] = "Actual";
				foreach($types as $kt=>$vt){
					$fileType = $vt;
					foreach($sizes as $size){
						if($size=="Actual"){
							$fileP = $file_name.".$fileType";
						}else{
							$fileP = $file_name."-$size".".$fileType";
						}
						$fileUrl = url("/images/".$fileP);
						list($width, $height) = getimagesize($up_path.$fileP);
						$image_info["sizes"][$size][$fileType] = array(
							"url" => $fileUrl,
							"size" => filesize($up_path.$fileP),
							"width" => $width,
							"height" => $height
						);
					}
				}
				$images_data = $image_info;	
			}
		}
		return $images_data;
	}
	
	public function createFolder(){
		$d["folders"] = DB::table("media_category")->get();	
		return view("mediapanel::gallery", $d);
	}
	
	public function storefolder(){
		$folder_name = Request()->input("t");
		$type = Request()->input("s");
		$data = array(
			"folder_type" => "folder",
			"folder_name" => $folder_name,
		);
		if($type=="new"){
			DB::table("media_category")->updateOrInsert(['folder_name'=>$folder_name],$data);
		}else{
			DB::table("media_category")->where("folder_name", $type)->update($data);
		}
		return $this->get_folder_list();
	}
	
	public function uploadMedia(){
		$d["folder"] = DB::table("media_category")->get();	
		return view("mediapanel::upload", $d);
	}
	
	public function insertVideo(){
		return view("mediapanel::video");
	}
	
	function get_images($offset = 0, $folder = ""){
		$d = array();
		if ($folder==""){
			$r = DB::table("media")->orderBy("id", "desc")->offset($offset)->limit($this->_limit)->get();
		}else{
			$r = DB::table("media")->where([
				["folder", "like", "%$folder%"]
			])->orderBy("id", "desc")->offset($offset)->limit($this->_limit)->get();
		}
		$d["images"] = $r;
		$d["folder"] = DB::table("media_category")->get();	
		$d["folder_c"] = $folder;
		return $d;
	}
	
	function more_images(){
		$folder = request("f");
		$offset = (request("page") - 1) * $this->_limit;
		$images = $this->get_images($offset, $folder);
		$images = $images["images"];
		$up_path = base_path().'/images/';
		if(count($images)==0){
			return "No More Images";
		}else{
			return view("mediapanel::more_list", compact("images", "up_path"))->render();	
		}
	}
	
	function get_folder_list(){
		$r = DB::table("media_category")->get();
		return view("mediapanel::folder-list", ["folders"=>$r]);
	}
	
	function _upload(){
		$options["file"] = "ufile";
		$options["thumb"] = true;
		$options["mid"] = true;
		$options["path"] = base_path().'/images/';
		$options["up_path"] = base_path().'/images/';
		$r = DB::table("media_setting")->where("media_key", "sizes")->first();
		$sizes = array();
		if(isset($r->id)){
			$sizes = json_decode($r->media_value, true);
		}
		$media = new ImageManage($options);
		$up = $media->upload($sizes);
		if (isset($up["images"]) and count($up["images"]) > 0){
			$images = json_encode($up["images"]);
			$folder = Request()->input("folder");
			$folder = ($folder=="") ? "": implode(",",$folder);
			$r = array(
				"images" => $images,
				"title" => $up["name"],
				"alt" => $up["name"],
				"caption" => $up["name"],
				"description" => $up["name"],
				"sizes" => json_encode($up["sizes"]),
				"folder" => $folder,
				"type" => $up["ext"],
				"created_at" => date("Y-m-d h:i:s")
			);
			DB::table("media")->insert($r);
		}
		return $this->_loadImages();
	}
	
	function _loadImages($offset = 0){
		$d = $this->get_images($offset);
		$d["up_path"] = base_path().'/images/';
		$html = view("mediapanel::images", $d)->render();
		return $html;
	}
	
	function _update_opt(){
		$sizes = array();
		if(request()->has("id")){
			$id = request("id");
			$alt = request("alt");
			$title = request("title");
			$caption = request("caption");
			$description = request("description");
			$size = request("size");
			$folder = request("folder");
			$data = array(
				"alt" => $alt,
				"title" => $title,
				"caption" => $caption,
				"description" => $description,
				"folder" => implode(",",$folder),
				"updated_at" => date("Y-m-d h:i:s")
			);
			$r = DB::table("media")->where("id", $id)->first();
			$sizes = json_decode($r->sizes,true);
			if(request()->has("size")){
				if(isset($r->id) and is_numeric($size)){
					$rweb = DB::table("media_setting")->where("media_key", "auto_webp")->first();
					if(isset($rweb->id)){
						$is_webp_allow = ($rweb->media_value==1) ? 1 : 0;
					}else{
						$is_webp_allow = 0;
					}
					$images = json_decode($r->images,true);
					$image=  $images["full"];
					$source = base_path().'/images/'.$image;
					$media = new ImageManage();
					$media->generate_thumb($source, base_path().'/images/', $size,"","");
					if($is_webp_allow==1){
						if(extension_loaded('gd')){
							$info = gd_info();
							if($info["WebP Support"]){
								$media->generate_thumb_with_webp($source, base_path().'/images/',$size, "thumb");
							}
						}
					}
					
					$sizes[] = $size;
					$data["sizes"] = $sizes;
				}
			}
			DB::table("media")->where("id", $id)->update($data);
		}
		$r = DB::table("media")->where("id", $id)->first();
		$n = $this->parseImages($r);
		return json_encode(array("resp"=>"success", "msg"=>"Record has been updated.", "data"=>$n));
	}
	
	function getSetting(){
		$r = DB::table("media_setting")->where("media_key", "auto_webp")
			->orWhere("media_key", "default_title")
			->orWhere("media_key", "default_alt_text")
			->orWhere("media_key", "default_desp")
			->orWhere("media_key", "default_caption")
			->orWhere("media_key", "default_image_size")
			->orWhere("media_key", "default_image_type")->get();
		$set = array();
		foreach($r as $k=>$v){
			$set[$v->media_key] = $v->media_value;
		}
		return $set;
	}
	
	function settings (){
		$settings = $this->getSetting();
		return view("mediapanel::settings", compact("settings"));
	}
	function cropSec (){
		return view("mediapanel::crop_sec");
	}
	
	function storesizes(){
		if(request()->has("data")){
			$r = DB::table("media_setting")->where("media_key", "sizes")->first();
			$data = array(
				"media_key" => "sizes",
				"media_value" => json_encode(request("data")),
				"auto" => "",
			);
			if(isset($r->id)){
				DB::table("media_setting")->where("media_key", "sizes")->update($data);
			}else{
				DB::table("media_setting")->insert($data);
			}
		}
	}
	
	function storesetting(){
		if(request()->has("data") and is_array(request("data"))){
			foreach(request("data") as $k=>$v){
				$r = DB::table("media_setting")->where("media_key", $k)->first();
				$data = array(
					"media_key" => $k,
					"media_value" => $v,
					"auto" => "",
				);
				if(isset($r->id)){
					DB::table("media_setting")->where("media_key", $k)->update($data);
				}else{
					DB::table("media_setting")->insert($data);
				}
			}
		}
	}
	
	function _search_images(){
		if(request()->has("f")){
			$v = request("f");
			if (request()->has("g") and request("g")!=""){
				$images = DB::table("media")->where("title", "like" ,"%$v%")->whereRaw('FIND_IN_SET(?,folder)', [request("g")])->offset(0)->limit($this->_limit)->orderBy("id", "desc")->get();
			}else{
				$images = DB::table("media")->where("title", "like" ,"%$v%")->offset(0)->limit($this->_limit)->orderBy("id", "desc")->get();
			}
			
			$up_path = base_path().'/images/';
			$is_search = true;
			$html = view("mediapanel::images_list", compact("images", "up_path", "is_search"))->render();
			return $html;
		}else{
			return "";
		}
	}
	
	function _changeFolder(){
		$up_path = base_path().'/images/';
		if(request()->has("f") and request("f")!=""){
			$images = DB::table("media")->whereRaw('FIND_IN_SET(?,folder)', [request("f")])->offset(0)->limit($this->_limit)->orderBy("id", "desc")->get();
		}else{
			$images = DB::table("media")->offset(0)->limit($this->_limit)->orderBy("id", "desc")->get();
		}
		$html = view("mediapanel::images_list", compact("images", "up_path"))->render();
		return $html;
	}
	
	function _delFolder(){
		if(request()->has("t")){
			$folder_name = request()->input("t");
			$row = DB::table("media_category")->where("folder_name",$folder_name)->first();
			if (is_object($row)){
				$r["folder"] = 0;
				$f = DB::table("media")->whereRaw('FIND_IN_SET(?,folder)', $row->id)->first();
				if(isset($f->id)){
					$folders  = explode(",",$f->folder);
					$fld = array();
					foreach($folders as $folder){
						$fld[] = $folder;
					}
					$f = DB::table("media")->whereRaw('FIND_IN_SET(?,folder)', $row->id)->update([
						"folder" => implode(",", $fld)
					]);
				}
				DB::table("media_category")->where("folder_name", $folder_name)->delete();
			}
		}
	}
	
	function _delMedia(){
		$images = array();
		if(request()->has("t")){
			$id = request("t");
			$r= DB::table("media")->where("id", $id)->first();
			if(isset($r->id)){
				$images = json_decode($r->images, true);
			}
		}
		
		foreach($images as $k=>$v){
			$image = base_path().'/images/'.$v;
			if(file_exists($image) and $v!=""){
				unlink($image);
			}
		}
		DB::table("media")->where("id", $id)->delete();
		return "yes";
	}
	
	function _insertVideo(){
		$link= Request()->input("embed");
  		$source= Request()->input("source");
  		if($source=="facebook"){
	  		$parse = parse_url($link);
			$trim = trim($parse["path"],"/");
			$exp = explode("/",$trim);
			$page = $exp[0];
			$video_id = $exp[count($exp)-1];
			$embeded ="<iframe src='http://www.facebook.com/plugins/video.php?href=https://www.facebook.com/$page/videos/$video_id' width='560' height='393' class='embed-responsive-item' frameborder='0'></iframe>";	
		}elseif($source=="tune.pk"){
			$parse = parse_url($link);
			$trim = trim($parse["path"], "/");
			$sp=explode("/",$trim);
			$v=$sp[1];
			$embeded ="<iframe width='600' height='336' src='http://tune.pk/player/embed_player.php?vid=$v&width=600&height=336&autoplay=no' class='embed-responsive-item' frameborder='0' allowfullscreen scrolling='no'></iframe>";	
		}elseif($source=="dailymotion"){
			$rep=str_replace("http://","",$link);
			$sp=explode("/",$rep);
			$vl=explode("_",$sp[count($sp)-1]);
			$v=$vl[0];
			$embeded ="<iframe frameborder='0' width='600' height='336' src='//www.dailymotion.com/embed/video/$v' class='embed-responsive-item' allowfullscreen></iframe>";	
		}elseif($source=="youtube"){
			$parts = parse_url($link);
			parse_str($parts['query'], $query);
			$v=$query['v'];
			$embeded ="<iframe width='600' height='336' src='//www.youtube.com/embed/$v?rel=0' frameborder='0' class='embed-responsive-item' allowfullscreen></iframe>";	
		}elseif ($source=="vimeo") {
			$link=str_replace("http://","",$link);
			$vf=explode("/",$link);
			$id=$vf[count($vf)-1];
			 $embeded="
			<iframe src='//player.vimeo.com/video/$id' width='600' height='336' frameborder='0' class='embed-responsive-item' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";	
		}
        echo  "<div class='embed-responsive embed-responsive-16by9'>".$embeded."</div>";
	}
	
}