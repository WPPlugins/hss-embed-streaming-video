<?php
/*
Plugin Name: HSS Embed Streaming Video
Plugin URI: https://www.hoststreamsell.com
Description: Provide access to Streaming Video in your WordPress Website
Author: Gavin Byrne
Author URI: https://www.hoststreamsell.com
Contributors:
Version: 2.01

HSS Embed Streaming Video is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

HSS Embed Streaming Video is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with HSS Embed Streaming Video. If not, see <http://www.gnu.org/licenses/>.
*/


// create shortcode with parameters so that the user can define what's queried - default is to list all blog posts



register_activation_hook(__FILE__, 'hss_embed_add_defaults');
register_uninstall_hook(__FILE__, 'hss_embed_delete_plugin_options');
add_action('admin_init', 'hss_embed_init' );

function hss_embed_add_defaults() {
        $tmp = get_option('hss_embed_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
                delete_option('hss_embed_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
                $arr = array(   "api_key" => "", "jwplayer_version" => "7", "jwplayer_logo_file" => "", "jwplayer_logo_link" => "", "jwplayer_logo_hide" => "false"
                );
                update_option('hss_embed_options', $arr);
        }
}

function hss_embed_delete_plugin_options() {
        delete_option('hss_embed_options');
}

function hss_embed_init(){
        register_setting( 'hss_embed_plugin_options', 'hss_embed_options', 'hss_embed_validate_options' );
        $options = get_option('hss_embed_options');
        if(is_array($options)){
                if (array_key_exists('jwplayer_version', $options)==false) {
                        $options['jwplayer_version'] = "6";
                        update_option('hss_embed_options', $options);
                }
                if (array_key_exists('jwplayer_logo_file', $options)==false) {
                        $options['jwplayer_logo_file'] = "";
                        update_option('hss_embed_options', $options);
                }
                if (array_key_exists('jwplayer_logo_link', $options)==false) {
                        $options['jwplayer_logo_link'] = "";
                        update_option('hss_embed_options', $options);
                }
                if (array_key_exists('jwplayer_logo_hide', $options)==false) {
                        $options['jwplayer_logo_hide'] = "false";
                        update_option('hss_embed_options', $options);
                }
	}
}

function hss_embed_validate_options($input) {
         // strip html from textboxes
        $input['api_key'] =  wp_filter_nohtml_kses($input['api_key']); // Sanitize textarea input (strip html tags, and escape characters)
        return $input;
}

// Register style sheet.
add_action( 'wp_enqueue_scripts', 'register_hss_embed_plugin_styles' );

/**
 * Register style sheet.
 */
function register_hss_embed_plugin_styles() {
        wp_register_style( 'hss-embed-streaming-video', plugins_url( 'hss-embed-streaming-video/css/hss-woo.css' ) );
        wp_enqueue_style( 'hss-embed-streaming-video' );
}

function hss_embed_options_page () {
?>
        <div class="wrap">

                <!-- Display Plugin Icon, Header, and Description -->
                <div class="icon32" id="icon-options-general"><br></div>
                <h2>HostStreamSell Video Embed Plugin Settings</h2>
                <p>Please enter the settings below...</p>

                <!-- Beginning of the Plugin Options Form -->
                <form method="post" action="options.php">
                        <?php settings_fields('hss_embed_plugin_options'); ?>
                        <?php $options = get_option('hss_embed_options'); ?>

                        <!-- Table Structure Containing Form Controls -->
                        <!-- Each Plugin Option Defined on a New Table Row -->
                        <table class="form-table">

                                <!-- Textbox Control -->
                                <tr>
                                        <th scope="row">HostStreamSell API Key<BR><i>(available from your account on www.hoststreamsell.com)</i></th>
                                        <td>
                                                <input type="text" size="40" name="hss_embed_options[api_key]" value="<?php echo $options['api_key']; ?>" />
                                        </td>
                                </tr>
                                <tr>
                                        <th scope="row">Video Player Size<BR><i>(leave blank to use defaults)</i></th>
                                        <td>
                                                Width <input type="text" size="10" name="hss_embed_options[player_width_default]" value="<?php echo $options['player_width_default']; ?>" /> Height  <input type="text" size="10" name="hss_embed_options[player_height_default]" value="<?php echo $options['player_height_default']; ?>" />
                                        </td>
                                </tr>
                                <tr>
                                        <th scope="row">Make Player Width and Height Responsive</th>
                                        <td>
                                                <input type="checkbox" name="hss_embed_options[responsive_player]" value="1"<?php checked( 1 == $options['responsive_player']); ?> />
                                        </td>
                                </tr>
                                <tr>
                                        <th scope="row">Reponsive Player Max Width<BR><i>(default is 640 if left blank, only used when Reponsive Player checkbox is checked)</i></th>
                                        <td>
                                                Width <input type="text" size="10" name="hss_embed_options[player_responsive_max_width]" value="<?php echo $options['player_responsive_max_width']; ?>" />
                                        </td>
                                </tr>
                                <tr>
                                        <th scope="row">Mobile Device Video Player Size<BR><i>(leave blank to use defaults)</i></th>
                                        <td>
                                                Width <input type="text" size="10" name="hss_embed_options[player_width_mobile]" value="<?php echo $options['player_width_mobile']; ?>" /> Height  <input type="text" size="10" name="hss_embed_options[player_height_mobile]" value="<?php echo $options['player_height_mobile']; ?>" />
                                        </td>
                                </tr>
                                <tr>
                                        <th scope="row">JW Player Version<BR><i>Note: JW Player 7 requires a license file but they have changed their PRO plan to be free - see www.jwplayer.com for more info</i></th>
                                        <td>
                                                <select name="hss_embed_options[jwplayer_version]">
                                                <?php
                                                if ($options['jwplayer_version']=="6"){
                                                        ?><option value="6" SELECTED>6</option><?php
                                                        ?><option value="7">7</option><?php
                                                }
                                                elseif ($options['jwplayer_version']=="7"){
                                                        ?><option value="7" SELECTED>7</option><?php
                                                        ?><option value="6">6</option><?php
                                                } ?>
                                                </select>
                                        </td>
                                </tr>
                                <tr>
                                        <th scope="row">JW Player License Key<BR><i>(available from www.longtailvideo.com)</i></th>
                                        <td>
                                                <input type="text" size="50" name="hss_embed_options[jwplayer_license]" value="<?php echo $options['jwplayer_license']; ?>" />
                                        </td>
                                </tr>
                                <tr>
                                        <th scope="row">JW Player Watermark File URL<BR><i>(JW Player 7 only - read more <a href='http://support.jwplayer.com/customer/portal/articles/1406865-branding-your-player' target='_blank'>here</a>)</i></th>
                                        <td>
                                                <input type="text" size="50" name="hss_embed_options[jwplayer_logo_file]" value="<?php echo $options['jwplayer_logo_file']; ?>" />
                                        </td>
                                </tr>
                                <tr>
                                        <th scope="row">JW Player Watermark Link<BR><i>(JW Player 7 only)</i></th>
                                        <td>
                                                <input type="text" size="50" name="hss_embed_options[jwplayer_logo_link]" value="<?php echo $options['jwplayer_logo_link']; ?>" />
                                        </td>
                                </tr>
                                <tr>
                                        <th scope="row">JW Player Watermark Hide<BR><i>(JW Player 7 only)</i></th>
                                        <td>
                                                <select name="hss_embed_options[jwplayer_logo_hide]">
                                                <?php
                                                if ($options['jwplayer_logo_hide']=="false" or $options['jwplayer_logo_hide']==""){
                                                        ?><option value="false" SELECTED>false</option><?php
                                                        ?><option value="true">true</option><?php
                                                }
                                                elseif ($options['jwplayer_logo_hide']=="true"){
                                                        ?><option value="true" SELECTED>true</option><?php
                                                        ?><option value="false">false</option><?php
                                                } ?>
                                                </select>
                                        </td>
                                </tr>
                                <tr>
                                        <th scope="row">Website Reference ID<BR><i>(set if you want a user to see the full video if they have purchased using one of the other HSS plugins)</i></th>
                                        <td>
                                                <input type="text" size="40" name="hss_embed_options[database_id]" value="<?php echo $options['database_id']; ?>" />
                                        </td>
                                </tr>
                                <tr>
                        </table>
                        <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                        </p>
                </form>
        </div>
<?php
}

function hss_embed_menu () {
        add_options_page('HostStreamSell Embed Video Admin','HSS Embed Admin','manage_options','hss_embed_admin', 'hss_embed_options_page');
}

add_action('admin_menu','hss_embed_menu');

add_shortcode( 'hss-embed-video', 'hss_embed_video_shortcode' );
function hss_embed_video_shortcode( $atts ) {
global $is_iphone;
        global $user_ID;
    ob_start();
 
    // define attributes and their defaults
    extract( shortcode_atts( array (
      'videoid' => '-1',
      'version' => 'trailer',
    ), $atts ) );


	if($videoid<0){
		echo "ERROR: you need to set the videoid attribute!";	
	      	$myvariable = ob_get_clean();
        	return $myvariable;
	}

	$video = "
                                <SCRIPT type=\"text/javascript\">


                                var agent=navigator.userAgent.toLowerCase();
                                var is_iphone = (agent.indexOf('iphone')!=-1);
                                var is_ipad = (agent.indexOf('ipad')!=-1);
                                var is_playstation = (agent.indexOf('playstation')!=-1);
                                var is_safari = (agent.indexOf('safari')!=-1);
                                var is_iemobile = (agent.indexOf('iemobile')!=-1);
				var is_silk = (agent.indexOf('silk')!=-1);
                                var is_blackberry = (agent.indexOf('BlackBerry')!=-1);
                                var is_android = (agent.indexOf('android')!=-1);
				</SCRIPT>
	";

	
	$videoids = explode(",",$videoid);
	foreach($videoids as $videoidinner)
	{
			$rand = substr(md5(microtime()),rand(0,26),5);
                                $options = get_option('hss_embed_options');
                                $userId = $user_ID;
				if($userId==0){
					$userId = mt_rand(100000,999999);
				}
				#echo $userId;

                                $hss_video_id = $videoidinner;
				if($version=="full")
					$force_allow = "yes";
				else
					$force_allow = "no";
				if (isset($options['database_id'])){
					$database_id = $options['database_id'];
					if($database_id=="")
						$database_id=0;
						
					$response = wp_remote_post( "https://www.hoststreamsell.com/api/1/xml/videos?api_key=".$options['api_key']."&video_id=$hss_video_id&private_user_id=$userId&database_id=".$database_id."&expands=playback_details&force_allow=$force_allow", array(
                	                        'method' => 'GET',
        	                                'timeout' => 15,
	                                        'redirection' => 5,
	                                        'httpversion' => '1.0',
                                        	'blocking' => true,
                                	        'headers' => array(),
                        	                'body' => $params,
                	                        'cookies' => array()
        	                            )
	                                );
				}else{
					$userId = mt_rand(100000,999999);
					$response = wp_remote_post( "https://www.hoststreamsell.com/api/1/xml/videos?api_key=".$options['api_key']."&video_id=$hss_video_id&expands=playback_details&private_user_id=$userId&database_id=0&force_allow=$force_allow", array(
                                                'method' => 'GET',
                                                'timeout' => 15,
                                                'redirection' => 5,
                                                'httpversion' => '1.0',
                                                'blocking' => true,
                                                'headers' => array(),
                                                'body' => $params,
                                                'cookies' => array()
                                            )
                                        );
				}
                                $res = "";
                                if( is_wp_error( $response ) ) {
                                   $return_string .= 'Error occured retieving video information, please try refresh the page';
                                } else {
                                   $res = $response['body'];
                                }

                                $xml = new SimpleXMLElement($res);
                                _log($xml);
				$title = htmlspecialchars($xml->result->title, ENT_QUOTES);
                                $hss_video_title = $title;
                                $user_has_access = $xml->result->user_has_access;
                                //$video = "".$user_has_access;
                                
				#if($user_has_access=="true")
                                #        $video = "<center>You have access to this video</center>";

                                $description = $xml->result->description;
                                $feature_duration = $xml->result->feature_duration;
                                $trailer_duration = $xml->result->trailer_duration;
                                $video_width = $xml->result->width;
                                $video_height = $xml->result->height;
				$aspect_ratio = $xml->result->aspect_ratio;
                                if($video_width>640){
                                        $video_width = "640";
                                        $video_height = "390";
                                }
                                $referrer = site_url();
                                $hss_video_user_token = $xml->result->user_token;

                                $hss_video_mediaserver_ip = $xml->result->wowza_ip;

                                $hss_video_smil_token = "?privatetoken=".$hss_video_user_token;
                                $hss_video_mediaserver_ip = $xml->result->wowza_ip;

                                $hss_video_smil = $xml->result->smil;
                                $hss_video_big_thumb_url = $xml->result->big_thumb_url;
                                $hss_rtsp_url = $xml->result->rtsp_url;
                                $referrer = site_url();

                                $content_width = $video_width;
                                $content_height = $video_height;

                                if($is_iphone){
                                        if($content_width<320){
                                                $content_width=320;
                                        }
                                }

                                if($video_width>$content_width){
                                        $mod = $content_width%40;
                                        $video_width = $content_width-$mod;
                                        $multiple = $video_width/40;
                                        $video_height = $multiple*30;
                                }

                                if($is_iphone){
                                        if($options['player_width_mobile']!="")
                                                $video_width=$options['player_width_mobile'];
                                        if($options['player_height_mobile']!="")
                                                $video_height=$options['player_height_mobile'];
                                }else{
                                        if($options['player_width_default']!="")
                                                $video_width=$options['player_width_default'];
                                        if($options['player_height_default']!="")
                                                $video_height=$options['player_height_default'];
                                }
                                $httpString = "http";
                                if (is_ssl()) {
                                        $httpString = "https";
                                }

                                $subtitle_count = $xml->result->subtitle_count;
                                $subtitle_index = 1;
                                $subtitle_text = "";
                                $captions = "";
                                if($subtitle_count>0){
                                        $subtitle_text = ",
                                                tracks: [{";
                                        while($subtitle_index <= $subtitle_count)
                                        {
                                                $subtitle_label = (string)$xml->result[0]->subtitles->{'subtitle_label_'.$subtitle_index}[0];
                                                $subtitle_file = (string)$xml->result[0]->subtitles->{'subtitle_file_'.$subtitle_index}[0];
                                                $subtitle_text .= "
                                                    file: \"http://www.hoststreamsell.com/mod/secure_videos/subtitles/$subtitle_file?rand=".randomString()."\",
                                                    label: \"$subtitle_label\",
                                                    kind: \"captions\",
                                                    \"default\": true";
                                                $subtitle_index += 1;
                                                if($subtitle_index <= $subtitle_count){
                                                        $subtitle_text .= "
                                                },{";
                                                }
                                        }
                                        $subtitle_text .= "
                                                }]";

                                        $fontSize = "";
                                        if($options["subtitle_font_size"]!=""){
                                                $fontSize = "
                                                        fontSize: ".$options["subtitle_font_size"].",";
                                        }
                                        $captions = "
                                                captions: {
                                                        color: '#FFFFFF',".$fontSize."
                                                        backgroundOpacity: 0
                                                },";
                                }


                                $video = $video."
                                <script type=\"text/javascript\" src=\"";
                                if ($options['jwplayer_version']=="6"){
                                        $video.= $httpString."://www.hoststreamsell.com/mod/secure_videos/jwplayer-6.12/jwplayer/jwplayer.js";
                                }elseif($options['jwplayer_version']=="7"){
                                        $video.= $httpString."://www.hoststreamsell.com/mod/secure_videos/jwplayer7/jwplayer-7.0.2/jwplayer.js";
                                }else{
                                        $video.= $httpString."://www.hoststreamsell.com/mod/secure_videos/jwplayer-6.12/jwplayer/jwplayer.js";
                                }
                                $video.="\"></script>
                                <script type=\"text/javascript\">jwplayer.key=\"".$options['jwplayer_license']."\";</script>";
                                if($options["responsive_player"]==1){
                                        $responsive_width="640";
                                        if($options["player_responsive_max_width"]!="")
                                                $responsive_width=$options["player_responsive_max_width"];
                                        $video.="<div class='hss_video_player' style='max-width:".$responsive_width."px;'>";
                                }else{
                                        $video.="<div class='hss_video_player'>";
                                }
                                $video.="<div id='videoframe$videoidinner$rand'>An error occurred setting up the video player</div>
                                <SCRIPT type=\"text/javascript\">



                                if (is_iphone) { html5Player$videoidinner$rand();}
                                else if (is_ipad) { html5Player$videoidinner$rand(); }
				else if (is_silk) { newJWPlayer$videoidinner$rand(); }
                                else if (is_android) { rtspPlayer$videoidinner$rand(); }
                                else if (is_blackberry) { rtspPlayer$videoidinner$rand(); }
                                else if (is_playstation) { newJWPlayer$videoidinner$rand(); }
                                else { newJWPlayer$videoidinner$rand(); }

                                function newJWPlayer$videoidinner$rand()
                                {
                                        jwplayer('videoframe$videoidinner$rand').setup({
                                            playlist: [{
                                                image: '$hss_video_big_thumb_url',
                                                sources: [{
                                                    file: '$httpString://www.hoststreamsell.com/mod/secure_videos/private_media_playlist_v2.php?params=".$hss_video_id."!".urlencode($referrer)."!".$hss_video_user_token."!',
                                                    type: 'rtmp'
                                                },{
                                                    file: 'http://".$hss_video_mediaserver_ip.":1935/hss/smil:".$hss_video_smil."/playlist.m3u8".$hss_video_smil_token."&referer=".urlencode($referrer)."'
						}]$subtitle_text
                                            }],$captions
 					    rtmp: {
                                                bufferlength: 3,
                                                proxytype: 'best'
                                            },
                ";
                if($options['jwplayer_version']=="7" and $options['jwplayer_logo_file']!=""){
        $video.="                       logo: {
                                file: '".$options['jwplayer_logo_file']."',";
                        if($options['jwplayer_logo_link']!=""){
        $video.="
                                link: '".$options['jwplayer_logo_link']."',";
                        }
                        if($options['jwplayer_logo_hide']=="true"){
        $video.="
                                hide: '".$options['jwplayer_logo_hide']."',";
                        }
        $video.="
        },
                ";
                }
        $video.="
                                            primary: 'flash',   ";
                                if($options["responsive_player"]==1){
                                        $video.="                  width: '100%',
                                            aspectratio: '".$aspect_ratio."'";
                                }else{
                                        $video.="                 height: $video_height,
                                          width: $video_width";
                                }
        $video.="                       });
                                }

                                function rtspPlayer$videoidinner$rand()
                                {
                                        var player=document.getElementById(\"videoframe$videoidinner$rand\");
                                        player.innerHTML='<A HREF=\"rtsp://".$hss_video_mediaserver_ip."/hss/mp4:".$hss_rtsp_url."".$hss_video_smil_token."&referer=".urlencode($referrer)."\">'+
                                        '<IMG SRC=\"".$hss_video_big_thumb_url."\" '+
                                        'ALT=\"Start Mobile Video\" '+
                                        'BORDER=\"0\" '+
                                        'HEIGHT=\"$video_height\"'+
                                        'WIDTH=\"$video_width\">'+
                                        '</A>';
                                }

                                function html5Player$videoidinner$rand()
                                {
                                        var player=document.getElementById(\"videoframe$videoidinner$rand\");
                                        player.innerHTML='<video controls '+
                                        'src=\"http://".$hss_video_mediaserver_ip.":1935/hss/smil:".$hss_video_smil."/playlist.m3u8".$hss_video_smil_token."&referer=".urlencode($referrer)."\" '+
                                        'HEIGHT=\"".$video_height."\" '+
                                        'WIDTH=\"".$video_width."\" '+
                                        'poster=\"".$hss_video_big_thumb_url."\" '+
                                        'title=\"".$hss_video_title."\">'+
                                        '</video>';
                                }

                                </script>
                                </div>
                                <BR>";
		}
		echo $video;

      $myvariable = ob_get_clean();
        return $myvariable;

}

if(!function_exists('_log')){
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}

?>
