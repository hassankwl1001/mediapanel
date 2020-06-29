<div class="dg-mediapanel">
	<div class="dg-wrap dg-col-2">
		@include("mediapanel::temp.sidebar")
		<div class="dg-m">
			<h3 class="dg-headding">Insert Media</h3>
			<div class="dg-list">
				<div class="fdm">
					<ul>
						@for($n=1; $n<=10; $n++)
						<li>
							<nav>
								<a href="#">File Info</a>
								<a href="#">Edit File</a>
								<a href="#">Insert File</a>
							</nav>
							<img src="{{url('/packages/dgaps/mediapanel/src/resources/assets/no.jpg')}}">
						</li>
						@endfor
					</ul>	
				</div>
				
			</div>
		</div>
	</div>
</div>


<link rel="stylesheet" href="{{url('/packages/dgaps/mediapanel/src/resources/assets/style.css')}}" type="text/css">
<script src="{{url('/packages/dgaps/mediapanel/src/resources/assets/script.js')}}"></script>