<div class="dg-m-setting">
	<div>
		<div class="dg-m-setting-f">
			<strong class="box-title">Generate Sizes</strong>
			<div class="dg-r dg-r-lf">
				@php
					$r = DB::table("media_setting")->where("media_key", "sizes")->first();
					$sizes = array();
					$len = 0;
					if(isset($r->id)){
						$sizes = json_decode($r->media_value, true);
						$len = count($sizes) - 1;
					}
				@endphp
				@for($n=0; $n <= $len; $n++ )
					@php
						if(isset($sizes[$n])){
							$size = $sizes[$n];
						}else{
							$size = "";
						}
					@endphp
				<div>
					<input type="number" name="size" placeholder="px" value="{{$size}}" >
				</div>
				@endfor
			</div>
			<a href="#" class="dg-m-s-m">Add More</a>
			<button class="dg-m-s-s">Save</button>
		</div>
	</div>
	<div>
		
		@php
		$webp = (isset($settings["auto_webp"])) ? $settings["auto_webp"] : 0;
		$title = (isset($settings["default_image_title"])) ? $settings["default_image_title"] : 1;
		$alt = (isset($settings["default_alt_text"])) ? $settings["default_alt_text"] : 1;
		$desp = (isset($settings["default_desp"])) ? $settings["default_desp"] : 1;
		$cap = (isset($settings["default_caption"])) ? $settings["default_caption"] : 1;
		$sz = (isset($settings["default_image_size"])) ? $settings["default_image_size"] : 0;
		$typ = (isset($settings["default_image_type"])) ? $settings["default_image_type"] : 0;
		@endphp
		<div class="dg-m-setting-f dm-setting-lf">
			<strong class="box-title">Default Settings</strong>
			<div class="dg-r">
				<div>
					<label>Auto Convert to WebP</label>
					<div>
					<input type="checkbox" name="auto_webp" value="1" @if($webp==1) checked @endif >
					</div>
				</div>
				<div>
					<label>Default Image Title</label>
					<div>
					<input type="radio" name="image_title" value="1" @if($title==1) checked @endif > Actual &nbsp; &nbsp; 
					<input type="radio" name="image_title" value="0" @if($title==0) checked @endif > Empty &nbsp; &nbsp; 
					</div>
				</div>
				<div>
					<label>Default Alt Text</label>
					<div>
					<input type="radio" name="alt_text" value="1" @if($alt==1) checked @endif > Actual &nbsp; &nbsp; 
					<input type="radio" name="alt_text" value="0" @if($alt==0) checked @endif > Empty &nbsp; &nbsp; 
					</div>
				</div>
				<div>
					<label>Default Description</label>
					<div>
					<input type="radio" name="image_description" value="1" @if($desp==1) checked @endif > Actual &nbsp; &nbsp; 
					<input type="radio" name="image_description" value="0" @if($desp==0) checked @endif > Empty &nbsp; &nbsp; 
					</div>
				</div>
				<div>
					<label>Default Caption</label>
					<div>
					<input type="radio" name="image_caption" value="1" @if($cap==1) checked @endif > Actual &nbsp; &nbsp; 
					<input type="radio" name="image_caption" value="0" @if($cap==0) checked @endif > Empty &nbsp; &nbsp; 
					</div>
				</div>
				<div>
					<label>Default Image Size</label>
					<div>
					<input type="radio" name="image_size" value="0" @if($sz==0) checked @endif > Actual
					@for($n=0; $n <= $len; $n++)
						@if(isset($sizes[$n]))
							<input type="radio" name="image_size" value="{{$sizes[$n]}}"  @if($sz==$sizes[$n]) checked @endif > {{$sizes[$n]}} &nbsp; &nbsp; 
						@endif
					@endfor
					</div>
				</div>
				<div>
					<label>Default Image Type</label>
					<div>
					<input type="radio" name="image_type" value="1" @if($typ==1) checked @endif > Actual &nbsp; &nbsp; 
					<input type="radio" name="image_type" value="0" @if($typ==0) checked @endif > WebP &nbsp; &nbsp; 
					</div>
				</div>
			</div>
			<button class="dg-m-s-gs">Save</button>
		</div>
	</div>
</div>