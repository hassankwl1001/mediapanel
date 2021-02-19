
			@foreach($images as $k=>$v)
				@php
					//dd($v);
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
							$image_info = json_encode($image_info);						
				@endphp
					<li>
						<div style="display: none;">{!!$image_info!!}</div>
						<img src='{{url("/images/$image")}}' class='m-ins-image' >
					</li>
				@php }} @endphp
			@endforeach
		