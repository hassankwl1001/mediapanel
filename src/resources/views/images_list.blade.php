@if(count($images)==0)
		<div class="dg-m-alert dg-m-alert-info">No More Images</div>
	@else
		<ul class='file-list'>
			@foreach($images as $k=>$v)
				@php
					//dd($v);
					$imagess = json_decode($v->images, true);
					$image = $imagess["full"];
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
								foreach($imagess as $s_size=>$s_image){
									if($s_size=="full"){
										$fileP = $s_image;
										$mSize = "Actual";
									}else{
										$fileP = $s_image;
										$mSize = $s_size;
									}
									$mSize = ucwords(str_replace("-"," ", $mSize));
									if(file_exists($up_path.$fileP)){
										list($width, $height) = getimagesize($up_path.$fileP);
										$fileUrl = url("/images/".$fileP);
										$image_info["sizes"][$mSize][$fileType] = array(
											"url" => $fileUrl,
											"size" => filesize($up_path.$fileP),
											"width" => $width,
											"height" => $height
										);
									}
								}
							}
							;
							$image_info = json_encode($image_info);
							$image_exp = explode(".", $image);
							if(isset($imagess[$sizes[0]])){
								$image = $imagess[$sizes[0]];
							}else{
								$image = $imagess["full"];
							}
				@endphp
					<li>
						<div style="display: none;">{!!$image_info!!}</div>
						<img src='{{url("/images/$image")}}' class='m-ins-image' >
					</li>
				@php }} @endphp
			@endforeach
		</ul>
		<div style="clear: both"></div>
		<div style="text-align: center;@if(count($images) < 16) display:none; @endif" class="moreload">
			<a href="#" class="___loadMore">Load More</a>
		</div>
	@endif