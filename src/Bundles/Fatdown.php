<?php

/**
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2019 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Bundles;

abstract class Fatdown extends \s9e\TextFormatter\Bundle
{
	/**
	* @var s9e\TextFormatter\Parser Singleton instance used by parse()
	*/
	protected static $parser;

	/**
	* @var s9e\TextFormatter\Renderer Singleton instance used by render()
	*/
	protected static $renderer;

	/**
	* Return a new instance of s9e\TextFormatter\Parser
	*
	* @return s9e\TextFormatter\Parser
	*/
	public static function getParser()
	{
		return unserialize('O:24:"s9e\\TextFormatter\\Parser":4:{s:16:"' . "\0" . '*' . "\0" . 'pluginsConfig";a:10:{s:9:"Autoemail";a:5:{s:8:"attrName";s:5:"email";s:10:"quickMatch";s:1:"@";s:6:"regexp";s:39:"/\\b[-a-z0-9_+.]+@[-a-z0-9.]*[a-z0-9]/Si";s:7:"tagName";s:5:"EMAIL";s:11:"regexpLimit";i:50000;}s:8:"Autolink";a:5:{s:8:"attrName";s:3:"url";s:6:"regexp";s:132:"#\\b(?:ftp|https?)://\\S(?>[^\\s()\\[\\]\\x{FF01}-\\x{FF0F}\\x{FF1A}-\\x{FF20}\\x{FF3B}-\\x{FF40}\\x{FF5B}-\\x{FF65}]|\\([^\\s()]*\\)|\\[\\w*\\])++#Siu";s:7:"tagName";s:3:"URL";s:10:"quickMatch";s:3:"://";s:11:"regexpLimit";i:50000;}s:7:"Escaper";a:4:{s:10:"quickMatch";s:1:"\\";s:6:"regexp";s:29:"/\\\\[-!#()*+.:<>@[\\\\\\]^_`{|}]/";s:7:"tagName";s:3:"ESC";s:11:"regexpLimit";i:50000;}s:10:"FancyPants";a:2:{s:8:"attrName";s:4:"char";s:7:"tagName";s:2:"FP";}s:12:"HTMLComments";a:5:{s:8:"attrName";s:7:"content";s:10:"quickMatch";s:4:"<!--";s:6:"regexp";s:22:"/<!--(?!\\[if).*?-->/is";s:7:"tagName";s:2:"HC";s:11:"regexpLimit";i:50000;}s:12:"HTMLElements";a:5:{s:10:"quickMatch";s:1:"<";s:6:"prefix";s:4:"html";s:6:"regexp";s:385:"#<(?>/((?:a(?:bbr)?|br?|code|d(?:[dlt]|el|iv)|em|hr|i(?:mg|ns)?|li|ol|pre|r(?:[bp]|tc?|uby)|s(?:pan|trong|u[bp])?|t(?:[dr]|able|body|foot|h(?:ead)?)|ul?))|((?:a(?:bbr)?|br?|code|d(?:[dlt]|el|iv)|em|hr|i(?:mg|ns)?|li|ol|pre|r(?:[bp]|tc?|uby)|s(?:pan|trong|u[bp])?|t(?:[dr]|able|body|foot|h(?:ead)?)|ul?))((?>\\s+[a-z][-a-z0-9]*(?>\\s*=\\s*(?>"[^"]*"|\'[^\']*\'|[^\\s"\'=<>`]+))?)*+)\\s*/?)\\s*>#i";s:7:"aliases";a:6:{s:1:"a";a:2:{s:0:"";s:3:"URL";s:4:"href";s:3:"url";}s:2:"hr";a:1:{s:0:"";s:2:"HR";}s:2:"em";a:1:{s:0:"";s:2:"EM";}s:1:"s";a:1:{s:0:"";s:1:"S";}s:6:"strong";a:1:{s:0:"";s:6:"STRONG";}s:3:"sup";a:1:{s:0:"";s:3:"SUP";}}s:11:"regexpLimit";i:50000;}s:12:"HTMLEntities";a:5:{s:8:"attrName";s:4:"char";s:10:"quickMatch";s:1:"&";s:6:"regexp";s:38:"/&(?>[a-z]+|#(?>[0-9]+|x[0-9a-f]+));/i";s:7:"tagName";s:2:"HE";s:11:"regexpLimit";i:50000;}s:8:"Litedown";a:1:{s:18:"decodeHtmlEntities";b:1;}s:10:"MediaEmbed";a:4:{s:10:"quickMatch";s:3:"://";s:6:"regexp";s:26:"/\\bhttps?:\\/\\/[^["\'\\s]+/Si";s:7:"tagName";s:5:"MEDIA";s:11:"regexpLimit";i:50000;}s:10:"PipeTables";a:3:{s:16:"overwriteEscapes";b:1;s:17:"overwriteMarkdown";b:1;s:10:"quickMatch";s:1:"|";}}s:14:"registeredVars";a:3:{s:9:"urlConfig";a:1:{s:14:"allowedSchemes";s:20:"/^(?:ftp|https?)$/Di";}s:16:"MediaEmbed.hosts";a:13:{s:12:"bandcamp.com";s:8:"bandcamp";s:6:"dai.ly";s:11:"dailymotion";s:15:"dailymotion.com";s:11:"dailymotion";s:12:"facebook.com";s:8:"facebook";s:12:"liveleak.com";s:8:"liveleak";s:14:"soundcloud.com";s:10:"soundcloud";s:16:"open.spotify.com";s:7:"spotify";s:16:"play.spotify.com";s:7:"spotify";s:9:"twitch.tv";s:6:"twitch";s:9:"vimeo.com";s:5:"vimeo";s:7:"vine.co";s:4:"vine";s:11:"youtube.com";s:7:"youtube";s:8:"youtu.be";s:7:"youtube";}s:16:"MediaEmbed.sites";a:10:{s:8:"bandcamp";a:2:{i:0;a:0:{}i:1;a:2:{i:0;a:2:{s:7:"extract";a:1:{i:0;a:2:{i:0;s:25:"!/album=(?\'album_id\'\\d+)!";i:1;a:2:{i:0;s:0:"";i:1;s:8:"album_id";}}}s:5:"match";a:1:{i:0;a:2:{i:0;s:23:"!bandcamp\\.com/album/.!";i:1;a:1:{i:0;s:0:"";}}}}i:1;a:2:{s:7:"extract";a:3:{i:0;a:2:{i:0;s:29:"!"album_id":(?\'album_id\'\\d+)!";i:1;R:90;}i:1;a:2:{i:0;s:31:"!"track_num":(?\'track_num\'\\d+)!";i:1;a:2:{i:0;s:0:"";i:1;s:9:"track_num";}}i:2;a:2:{i:0;s:25:"!/track=(?\'track_id\'\\d+)!";i:1;a:2:{i:0;s:0:"";i:1;s:8:"track_id";}}}s:5:"match";a:1:{i:0;a:2:{i:0;s:23:"!bandcamp\\.com/track/.!";i:1;R:96;}}}}}s:11:"dailymotion";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:27:"!dai\\.ly/(?\'id\'[a-z0-9]+)!i";i:1;a:2:{i:0;s:0:"";i:1;s:2:"id";}}i:1;a:2:{i:0;s:92:"!dailymotion\\.com/(?:live/|swf/|user/[^#]+#video=|(?:related/\\d+/)?video/)(?\'id\'[a-z0-9]+)!i";i:1;R:119;}i:2;a:2:{i:0;s:17:"!start=(?\'t\'\\d+)!";i:1;a:2:{i:0;s:0:"";i:1;s:1:"t";}}}i:1;R:84;}s:8:"facebook";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:135:"@/(?!(?:apps|developers|graph)\\.)[-\\w.]*facebook\\.com/(?:[/\\w]+/permalink|(?!pages/|groups/).*?)(?:/|fbid=|\\?v=)(?\'id\'\\d+)(?=$|[/?&#])@";i:1;R:119;}i:1;a:2:{i:0;s:51:"@facebook\\.com/(?\'user\'\\w+)/(?\'type\'post|video)s?/@";i:1;a:3:{i:0;s:0:"";i:1;s:4:"user";i:2;s:4:"type";}}i:2;a:2:{i:0;s:46:"@facebook\\.com/video/(?\'type\'post|video)\\.php@";i:1;a:2:{i:0;s:0:"";i:1;s:4:"type";}}}i:1;R:84;}s:8:"liveleak";a:2:{i:0;a:1:{i:0;a:2:{i:0;s:41:"!liveleak\\.com/(?:e/|view\\?i=)(?\'id\'\\w+)!";i:1;R:119;}}i:1;a:1:{i:0;a:2:{s:7:"extract";a:1:{i:0;a:2:{i:0;s:28:"!liveleak\\.com/e/(?\'id\'\\w+)!";i:1;R:119;}}s:5:"match";a:1:{i:0;a:2:{i:0;s:24:"!liveleak\\.com/view\\?t=!";i:1;R:96;}}}}}s:10:"soundcloud";a:2:{i:0;a:4:{i:0;a:2:{i:0;s:84:"@https?://(?:api\\.)?soundcloud\\.com/(?!pages/)(?\'id\'[-/\\w]+/[-/\\w]+|^[^/]+/[^/]+$)@i";i:1;R:119;}i:1;a:2:{i:0;s:52:"@api\\.soundcloud\\.com/playlists/(?\'playlist_id\'\\d+)@";i:1;a:2:{i:0;s:0:"";i:1;s:11:"playlist_id";}}i:2;a:2:{i:0;s:89:"@api\\.soundcloud\\.com/tracks/(?\'track_id\'\\d+)(?:\\?secret_token=(?\'secret_token\'[-\\w]+))?@";i:1;a:3:{i:0;s:0:"";i:1;s:8:"track_id";i:2;s:12:"secret_token";}}i:3;a:2:{i:0;s:81:"@soundcloud\\.com/(?!playlists|tracks)[-\\w]+/[-\\w]+/(?=s-)(?\'secret_token\'[-\\w]+)@";i:1;a:2:{i:0;s:0:"";i:1;s:12:"secret_token";}}}i:1;a:2:{i:0;a:3:{s:7:"extract";a:1:{i:0;a:2:{i:0;s:36:"@soundcloud:tracks:(?\'track_id\'\\d+)@";i:1;R:109;}}s:6:"header";s:29:"User-agent: PHP (not Mozilla)";s:5:"match";a:1:{i:0;a:2:{i:0;s:56:"@soundcloud\\.com/(?!playlists/\\d|tracks/\\d)[-\\w]+/[-\\w]@";i:1;R:96;}}}i:1;a:3:{s:7:"extract";a:1:{i:0;a:2:{i:0;s:44:"@soundcloud://playlists:(?\'playlist_id\'\\d+)@";i:1;R:162;}}s:6:"header";s:29:"User-agent: PHP (not Mozilla)";s:5:"match";a:1:{i:0;a:2:{i:0;s:27:"@soundcloud\\.com/\\w+/sets/@";i:1;R:96;}}}}}s:7:"spotify";a:2:{i:0;a:1:{i:0;a:2:{i:0;s:102:"!(?:open|play)\\.spotify\\.com/(?\'id\'(?:user/[-.\\w]+/)?(?:album|artist|playlist|track)(?:[:/][-.\\w]+)+)!";i:1;R:119;}}i:1;R:84;}s:6:"twitch";a:2:{i:0;a:4:{i:0;a:2:{i:0;s:47:"#twitch\\.tv/(?:videos|\\w+/v)/(?\'video_id\'\\d+)?#";i:1;a:2:{i:0;s:0:"";i:1;s:8:"video_id";}}i:1;a:2:{i:0;s:44:"#www\\.twitch\\.tv/(?!videos/)(?\'channel\'\\w+)#";i:1;a:2:{i:0;s:0:"";i:1;s:7:"channel";}}i:2;a:2:{i:0;s:32:"#t=(?\'t\'(?:(?:\\d+h)?\\d+m)?\\d+s)#";i:1;R:126;}i:3;a:2:{i:0;s:56:"#clips\\.twitch\\.tv/(?:(?\'channel\'\\w+)/)?(?\'clip_id\'\\w+)#";i:1;a:3:{i:0;s:0:"";i:1;s:7:"channel";i:2;s:7:"clip_id";}}}i:1;R:84;}s:5:"vimeo";a:2:{i:0;a:2:{i:0;a:2:{i:0;s:50:"!vimeo\\.com/(?:channels/[^/]+/|video/)?(?\'id\'\\d+)!";i:1;R:119;}i:1;a:2:{i:0;s:19:"!#t=(?\'t\'[\\dhms]+)!";i:1;R:126;}}i:1;R:84;}s:4:"vine";a:2:{i:0;a:1:{i:0;a:2:{i:0;s:25:"!vine\\.co/v/(?\'id\'[^/]+)!";i:1;R:119;}}i:1;R:84;}s:7:"youtube";a:2:{i:0;a:4:{i:0;a:2:{i:0;s:69:"!youtube\\.com/(?:watch.*?v=|v/|attribution_link.*?v%3D)(?\'id\'[-\\w]+)!";i:1;R:119;}i:1;a:2:{i:0;s:25:"!youtu\\.be/(?\'id\'[-\\w]+)!";i:1;R:119;}i:2;a:2:{i:0;s:25:"@[#&?]t=(?\'t\'\\d[\\dhms]*)@";i:1;R:126;}i:3;a:2:{i:0;s:26:"![&?]list=(?\'list\'[-\\w]+)!";i:1;a:2:{i:0;s:0:"";i:1;s:4:"list";}}}i:1;a:1:{i:0;a:2:{s:7:"extract";a:1:{i:0;a:2:{i:0;s:19:"!/vi/(?\'id\'[-\\w]+)!";i:1;R:119;}}s:5:"match";a:1:{i:0;a:2:{i:0;s:14:"!/shared\\?ci=!";i:1;R:96;}}}}}}}s:14:"' . "\0" . '*' . "\0" . 'rootContext";a:2:{s:7:"allowed";a:2:{i:0;i:65527;i:1;i:65432;}s:5:"flags";i:8;}s:13:"' . "\0" . '*' . "\0" . 'tagsConfig";a:74:{s:8:"BANDCAMP";a:7:{s:10:"attributes";a:3:{s:8:"album_id";a:2:{s:8:"required";b:0;s:11:"filterChain";R:84;}s:8:"track_id";R:256;s:9:"track_num";R:256;}s:11:"filterChain";a:1:{i:0;a:2:{s:8:"callback";s:59:"s9e\\TextFormatter\\Parser\\FilterProcessing::filterAttributes";s:6:"params";a:4:{s:3:"tag";N;s:9:"tagConfig";N;s:14:"registeredVars";N;s:6:"logger";N;}}}s:12:"nestingLimit";i:10;s:5:"rules";a:1:{s:5:"flags";i:3089;}s:8:"tagLimit";i:5000;s:9:"bitNumber";i:5;s:7:"allowed";a:2:{i:0;i:49360;i:1;i:32768;}}s:1:"C";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:1:{s:5:"flags";i:66;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:0;s:7:"allowed";a:2:{i:0;i:0;i:1;i:0;}}s:4:"CODE";a:7:{s:10:"attributes";a:1:{s:4:"lang";a:2:{s:11:"filterChain";a:1:{i:0;a:2:{s:8:"callback";s:62:"s9e\\TextFormatter\\Parser\\AttributeFilters\\RegexpFilter::filter";s:6:"params";a:2:{s:9:"attrValue";N;i:0;s:23:"/^[- +,.0-9A-Za-z_]+$/D";}}}s:8:"required";b:0;}}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";a:10:{s:1:"C";i:1;s:2:"EM";i:1;s:6:"STRONG";i:1;s:3:"URL";i:1;s:5:"EMAIL";i:1;s:6:"html:b";i:1;s:9:"html:code";i:1;s:6:"html:i";i:1;s:11:"html:strong";i:1;s:6:"html:u";i:1;}s:12:"fosterParent";R:295;s:5:"flags";i:4436;}s:8:"tagLimit";i:5000;s:9:"bitNumber";i:1;s:7:"allowed";R:280;}s:11:"DAILYMOTION";a:7:{s:10:"attributes";a:2:{s:2:"id";R:256;s:1:"t";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:267;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:5;s:7:"allowed";R:271;}s:3:"DEL";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:1:{s:5:"flags";i:512;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:0;s:7:"allowed";R:249;}s:2:"EM";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:1:{s:5:"flags";i:2;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:0;s:7:"allowed";a:2:{i:0;i:65521;i:1;i:65408;}}s:5:"EMAIL";a:7:{s:10:"attributes";a:1:{s:5:"email";a:2:{s:11:"filterChain";a:1:{i:0;a:2:{s:8:"callback";s:61:"s9e\\TextFormatter\\Parser\\AttributeFilters\\EmailFilter::filter";s:6:"params";a:1:{s:9:"attrValue";N;}}}s:8:"required";b:1;}}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:1:{s:5:"flags";i:514;}s:8:"tagLimit";i:5000;s:9:"bitNumber";i:5;s:7:"allowed";a:2:{i:0;i:53191;i:1;i:65432;}}s:3:"ESC";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:1:{s:5:"flags";i:1616;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:6;s:7:"allowed";R:280;}s:8:"FACEBOOK";a:7:{s:10:"attributes";a:3:{s:2:"id";R:256;s:4:"type";R:256;s:4:"user";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:267;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:5;s:7:"allowed";R:271;}s:2:"FP";a:7:{s:10:"attributes";a:1:{s:4:"char";a:2:{s:8:"required";b:1;s:11:"filterChain";R:84;}}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:267;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:7;s:7:"allowed";a:2:{i:0;i:49344;i:1;i:32896;}}s:2:"H1";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";R:295;s:12:"fosterParent";R:295;s:5:"flags";i:260;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:2;s:7:"allowed";R:326;}s:2:"H2";R:367;s:2:"H3";R:367;s:2:"H4";R:367;s:2:"H5";R:367;s:2:"H6";R:367;s:2:"HC";a:7:{s:10:"attributes";a:1:{s:7:"content";R:359;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:1:{s:5:"flags";i:3153;}s:8:"tagLimit";i:5000;s:9:"bitNumber";i:6;s:7:"allowed";R:280;}s:2:"HE";R:357;s:2:"HR";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:2:{s:11:"closeParent";R:295;s:5:"flags";i:3349;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:1;s:7:"allowed";R:364;}s:3:"IMG";a:7:{s:10:"attributes";a:3:{s:3:"alt";R:256;s:3:"src";a:2:{s:11:"filterChain";a:1:{i:0;a:2:{s:8:"callback";s:59:"s9e\\TextFormatter\\Parser\\AttributeFilters\\UrlFilter::filter";s:6:"params";a:3:{s:9:"attrValue";N;s:9:"urlConfig";N;s:6:"logger";N;}}}s:8:"required";b:1;}s:5:"title";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:267;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:0;s:7:"allowed";R:364;}s:2:"LI";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";a:12:{s:1:"C";i:1;s:2:"EM";i:1;s:6:"STRONG";i:1;s:3:"URL";i:1;s:5:"EMAIL";i:1;s:6:"html:b";i:1;s:9:"html:code";i:1;s:6:"html:i";i:1;s:11:"html:strong";i:1;s:6:"html:u";i:1;s:2:"LI";i:1;s:7:"html:li";i:1;}s:12:"fosterParent";R:295;s:5:"flags";i:264;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:3;s:7:"allowed";a:2:{i:0;i:65527;i:1;i:65424;}}s:4:"LIST";a:7:{s:10:"attributes";a:2:{s:5:"start";a:2:{s:11:"filterChain";a:1:{i:0;a:2:{s:8:"callback";s:67:"s9e\\TextFormatter\\Parser\\AttributeFilters\\NumericFilter::filterUint";s:6:"params";R:335;}}s:8:"required";b:0;}s:4:"type";R:285;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";R:295;s:12:"fosterParent";R:295;s:5:"flags";i:3460;}s:8:"tagLimit";i:5000;s:9:"bitNumber";i:1;s:7:"allowed";a:2:{i:0;i:65352;i:1;i:65408;}}s:8:"LIVELEAK";a:7:{s:10:"attributes";a:1:{s:2:"id";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:267;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:5;s:7:"allowed";R:271;}s:5:"MEDIA";a:7:{s:11:"filterChain";a:1:{i:0;a:2:{s:8:"callback";s:54:"s9e\\TextFormatter\\Plugins\\MediaEmbed\\Parser::filterTag";s:6:"params";a:5:{s:3:"tag";N;s:6:"parser";N;s:16:"MediaEmbed.hosts";N;s:16:"MediaEmbed.sites";N;s:8:"cacheDir";N;}}}s:12:"nestingLimit";i:10;s:5:"rules";a:1:{s:5:"flags";i:513;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:15;s:7:"allowed";a:2:{i:0;i:65527;i:1;i:65304;}}s:5:"QUOTE";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";R:295;s:12:"fosterParent";R:295;s:5:"flags";i:268;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:1;s:7:"allowed";R:419;}s:10:"SOUNDCLOUD";a:7:{s:10:"attributes";a:4:{s:2:"id";R:256;s:11:"playlist_id";R:256;s:12:"secret_token";R:256;s:8:"track_id";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:267;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:5;s:7:"allowed";R:271;}s:7:"SPOTIFY";R:437;s:6:"STRONG";R:320;s:3:"SUB";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:1:{s:5:"flags";i:0;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:0;s:7:"allowed";R:326;}s:3:"SUP";R:471;s:5:"TABLE";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:430;s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:1;s:7:"allowed";a:2:{i:0;i:65344;i:1;i:65413;}}s:5:"TBODY";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";a:20:{s:1:"C";i:1;s:2:"EM";i:1;s:6:"STRONG";i:1;s:3:"URL";i:1;s:5:"EMAIL";i:1;s:6:"html:b";i:1;s:9:"html:code";i:1;s:6:"html:i";i:1;s:11:"html:strong";i:1;s:6:"html:u";i:1;s:5:"TBODY";i:1;s:2:"TD";i:1;s:2:"TH";i:1;s:5:"THEAD";i:1;s:2:"TR";i:1;s:10:"html:tbody";i:1;s:7:"html:td";i:1;s:7:"html:th";i:1;s:10:"html:thead";i:1;s:7:"html:tr";i:1;}s:12:"fosterParent";R:295;s:5:"flags";i:3456;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:8;s:7:"allowed";a:2:{i:0;i:65344;i:1;i:65412;}}s:2:"TD";a:7:{s:10:"attributes";a:1:{s:5:"align";a:2:{s:11:"filterChain";a:2:{i:0;a:2:{s:8:"callback";s:10:"strtolower";s:6:"params";R:335;}i:1;a:2:{s:8:"callback";s:62:"s9e\\TextFormatter\\Parser\\AttributeFilters\\RegexpFilter::filter";s:6:"params";a:2:{s:9:"attrValue";N;i:0;s:34:"/^(?:center|justify|left|right)$/D";}}}s:8:"required";b:0;}}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";a:14:{s:1:"C";i:1;s:2:"EM";i:1;s:6:"STRONG";i:1;s:3:"URL";i:1;s:5:"EMAIL";i:1;s:6:"html:b";i:1;s:9:"html:code";i:1;s:6:"html:i";i:1;s:11:"html:strong";i:1;s:6:"html:u";i:1;s:2:"TD";i:1;s:2:"TH";i:1;s:7:"html:td";i:1;s:7:"html:th";i:1;}s:12:"fosterParent";R:295;s:5:"flags";i:256;}s:8:"tagLimit";i:5000;s:9:"bitNumber";i:9;s:7:"allowed";R:419;}s:2:"TH";a:7:{s:10:"attributes";R:515;s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:527;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:9;s:7:"allowed";a:2:{i:0;i:64499;i:1;i:65424;}}s:5:"THEAD";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";R:295;s:12:"fosterParent";R:295;s:5:"flags";i:3456;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:8;s:7:"allowed";R:511;}s:2:"TR";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";a:16:{s:1:"C";i:1;s:2:"EM";i:1;s:6:"STRONG";i:1;s:3:"URL";i:1;s:5:"EMAIL";i:1;s:6:"html:b";i:1;s:9:"html:code";i:1;s:6:"html:i";i:1;s:11:"html:strong";i:1;s:6:"html:u";i:1;s:2:"TD";i:1;s:2:"TH";i:1;s:2:"TR";i:1;s:7:"html:td";i:1;s:7:"html:th";i:1;s:7:"html:tr";i:1;}s:12:"fosterParent";R:295;s:5:"flags";i:3456;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:10;s:7:"allowed";a:2:{i:0;i:65344;i:1;i:65410;}}s:6:"TWITCH";a:7:{s:10:"attributes";a:4:{s:7:"channel";R:256;s:7:"clip_id";R:256;s:1:"t";R:256;s:8:"video_id";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:267;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:5;s:7:"allowed";R:271;}s:3:"URL";a:7:{s:10:"attributes";a:2:{s:5:"title";R:256;s:3:"url";R:388;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:339;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:4;s:7:"allowed";R:343;}s:5:"VIMEO";a:7:{s:10:"attributes";a:2:{s:2:"id";R:256;s:1:"t";a:2:{s:11:"filterChain";a:1:{i:0;a:2:{s:8:"callback";s:65:"s9e\\TextFormatter\\Parser\\AttributeFilters\\TimestampFilter::filter";s:6:"params";R:335;}}s:8:"required";b:0;}}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:267;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:5;s:7:"allowed";R:271;}s:4:"VINE";R:437;s:7:"YOUTUBE";a:7:{s:10:"attributes";a:3:{s:2:"id";a:2:{s:11:"filterChain";a:1:{i:0;a:2:{s:8:"callback";s:62:"s9e\\TextFormatter\\Parser\\AttributeFilters\\RegexpFilter::filter";s:6:"params";a:2:{s:9:"attrValue";N;i:0;s:19:"/^[-0-9A-Za-z_]+$/D";}}}s:8:"required";b:0;}s:4:"list";R:256;s:1:"t";R:597;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:267;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:5;s:7:"allowed";R:271;}s:9:"html:abbr";a:7:{s:10:"attributes";a:1:{s:5:"title";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:473;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:0;s:7:"allowed";R:326;}s:6:"html:b";R:320;s:7:"html:br";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:1:{s:5:"flags";i:3201;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:0;s:7:"allowed";a:2:{i:0;i:65344;i:1;i:65408;}}s:9:"html:code";R:274;s:7:"html:dd";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";a:12:{s:1:"C";i:1;s:2:"EM";i:1;s:6:"STRONG";i:1;s:3:"URL";i:1;s:5:"EMAIL";i:1;s:6:"html:b";i:1;s:9:"html:code";i:1;s:6:"html:i";i:1;s:11:"html:strong";i:1;s:6:"html:u";i:1;s:7:"html:dd";i:1;s:7:"html:dt";i:1;}s:12:"fosterParent";R:295;s:5:"flags";i:256;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:11;s:7:"allowed";R:419;}s:8:"html:del";R:314;s:8:"html:div";a:7:{s:10:"attributes";a:1:{s:5:"class";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:462;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:12;s:7:"allowed";R:249;}s:7:"html:dl";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:430;s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:1;s:7:"allowed";a:2:{i:0;i:65344;i:1;i:65432;}}s:7:"html:dt";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:634;s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:11;s:7:"allowed";R:550;}s:6:"html:i";R:320;s:8:"html:img";a:7:{s:10:"attributes";a:5:{s:3:"alt";R:256;s:6:"height";R:256;s:3:"src";a:2:{s:11:"filterChain";R:389;s:8:"required";b:0;}s:5:"title";R:256;s:5:"width";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:625;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:0;s:7:"allowed";R:629;}s:8:"html:ins";R:314;s:7:"html:li";R:400;s:7:"html:ol";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:430;s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:1;s:7:"allowed";R:434;}s:8:"html:pre";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";R:295;s:12:"fosterParent";R:295;s:5:"flags";i:276;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:1;s:7:"allowed";R:326;}s:7:"html:rb";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";R:295;s:12:"fosterParent";R:295;s:5:"flags";i:256;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:13;s:7:"allowed";R:326;}s:7:"html:rp";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";a:3:{s:11:"closeParent";a:12:{s:1:"C";i:1;s:2:"EM";i:1;s:6:"STRONG";i:1;s:3:"URL";i:1;s:5:"EMAIL";i:1;s:6:"html:b";i:1;s:9:"html:code";i:1;s:6:"html:i";i:1;s:11:"html:strong";i:1;s:6:"html:u";i:1;s:7:"html:rp";i:1;s:7:"html:rt";i:1;}s:12:"fosterParent";R:295;s:5:"flags";i:256;}s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:14;s:7:"allowed";R:326;}s:7:"html:rt";R:690;s:8:"html:rtc";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:686;s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:13;s:7:"allowed";a:2:{i:0;i:65521;i:1;i:65472;}}s:9:"html:ruby";a:7:{s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:473;s:8:"tagLimit";i:5000;s:10:"attributes";R:84;s:9:"bitNumber";i:0;s:7:"allowed";a:2:{i:0;i:65521;i:1;i:65504;}}s:9:"html:span";a:7:{s:10:"attributes";R:652;s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:473;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:0;s:7:"allowed";R:326;}s:11:"html:strong";R:320;s:8:"html:sub";R:471;s:8:"html:sup";R:471;s:10:"html:table";R:477;s:10:"html:tbody";R:484;s:7:"html:td";a:7:{s:10:"attributes";a:2:{s:7:"colspan";R:256;s:7:"rowspan";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:527;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:9;s:7:"allowed";R:419;}s:10:"html:tfoot";R:484;s:7:"html:th";a:7:{s:10:"attributes";a:3:{s:7:"colspan";R:256;s:7:"rowspan";R:256;s:5:"scope";R:256;}s:11:"filterChain";R:258;s:12:"nestingLimit";i:10;s:5:"rules";R:527;s:8:"tagLimit";i:5000;s:9:"bitNumber";i:9;s:7:"allowed";R:550;}s:10:"html:thead";R:553;s:7:"html:tr";R:559;s:6:"html:u";R:320;s:7:"html:ul";R:674;}}');
	}

	/**
	* Return a new instance of s9e\TextFormatter\Renderer
	*
	* @return s9e\TextFormatter\Renderer
	*/
	public static function getRenderer()
	{
		return unserialize('O:42:"s9e\\TextFormatter\\Bundles\\Fatdown\\Renderer":2:{s:19:"enableQuickRenderer";b:1;s:9:"' . "\0" . '*' . "\0" . 'params";a:0:{}}');
	}
}