<div class="uc-image">
	<input type="hidden" name="{{$link}}" value="{{$image_link}}">
	<div id='{{str_replace("#", "",$return)}}' class="image_display" style="display:block;">
		<img src="{{$image_link}}">
	</div>
	<div style="margin-top:10px;">
		<a class="insert-media btn btn-success btn-sm" data-type="image" data-for="{{$for}}" data-return="{{$return}}" data-link="{{$link}}">Add Image</a>
   </div>
</div>