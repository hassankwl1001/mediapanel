<?php
//dgaps\mediapanel\src\MediaPanelServiceProvider.php
namespace hassankwl1001\mediapanel;
use Illuminate\Support\ServiceProvider;
class MediaPanelServiceProvider extends ServiceProvider {
	public function boot(){
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
	}
	public function register(){
		
		$this->app->make('hassankwl1001\mediapanel\Http\Controllers\MediaPanelController');
		$this->loadViewsFrom(__DIR__.'/resources/views', 'mediapanel');
		$this->loadMigrationsFrom(__DIR__.'/Database/migrations');
	}
}
?>