<?php
/**
* CSS3 plugin for ExpressionEngine 1.6.x
*
* @package	ExpressionEngine
* @category	plugin
* @author	Steve Stedman
* @link		http://github.com/stedman/sd.css3.ee_addon
*/

$plugin_info = array(
	'pi_name' => 'Css3',
	'pi_version' => '2.0.0',
	'pi_author' => 'Steve Stedman',
	'pi_author_url' => 'http://stedmandesign.com/',
	'pi_description' => 'For each advanced CSS3 property, use this plugin in a CSS template to spew out all the supporting browser prefixes. Supports Gecko (Firefox, moz), Konqueror (khtml), Presto (Opera, o), and Webkit (Safari, Chrome, webkit).',
	'pi_usage' => Css3::usage()
);

class Css3 {
	var $vendor_prefix = '';
	
	/**
	* Class constructor
	*/
	function Css3()
	{
		$this->EE =& get_instance();
	}
	
	/**
	* Factory for building typical property with browser prefixes
	*
	* @param string $property The CSS3 property name
	* @param array $vendor_prefix The browser prefixes to apply
	* @return array
	*/
	function create_property($property, $vendor_prefix = array('', '-moz-', '-webkit-'))
	{
		$values = $this->EE->TMPL->fetch_param('value');

		foreach($vendor_prefix as $prefix) {
			$css_array[] = $prefix . $property . ':' . $values . ';';
		}

		return implode("\n", $css_array);
	}


	/**
	* Apply browser prefixes to background-clip property 
	* see http://www.w3.org/TR/css3-background/#background-clip
	* see https://developer.mozilla.org/en/CSS/-moz-background-clip
	*
	* @return string
	*/
	function backgroundClip()
	{
		$vendor_prefix = array('', '-moz-', '-webkit-');
		
		$values = $this->EE->TMPL->fetch_param('value');

		foreach($vendor_prefix as $prefix) {
			$suffix = ($prefix === '-moz-') ? '' : '-box';
			$css_array[] = $prefix . 'background-clip:' . $values . $suffix . ';';				
		}

		return implode("\n", $css_array);
	}

	/**
	* Apply browser prefixes to background-image:gradient property 
	* see http://dev.w3.org/csswg/css3-images/#linear-gradients
	* see https://developer.mozilla.org/en/CSS/-moz-linear-gradient
	* see http://msdn.microsoft.com/en-us/library/ms532997(VS.85).aspx
	*
	* @return string
	*/
	function backgroundLinearGradient()
	{
		$values = $this->EE->TMPL->fetch_param('value');
		$values_array = explode(',', $values);

		$ie = $this->EE->TMPL->fetch_param('ie');

		if ($ie != '') {
			// for Internet Explorer
			$ie_css = "progid:DXImageTransform.Microsoft.gradient(startColorstr='{$values_array[1]}', endColorstr='{$values_array[2]}');";
			
			if ($ie == '8') {
				return '-ms-filter:"' . rtrim($ie_css, ';') . '";';
			}
			else {
				return 'filter:' . $ie_css;				
			}
		}
		else {
			// for normal browsers w/o gradient capabilities
			$css_array[] = 'background:' . $values_array[1] . ';';
			// for browsers w/ gradients
			$css_array[] = 'background:linear-gradient(' . $values . ');';
			// for gecko
			$css_array[] = 'background:-moz-linear-gradient(' . $values . ');';
			// for webkit
			$start_stop = ($values_array[0] == 'left') ? 'left top,right top' : 'left top,left bottom';
			$css_array[] = 'background:-webkit-gradient(linear,' . $start_stop . ',color-stop(0,' . trim($values_array[1]) . '),color-stop(1,' . trim($values_array[2]) . '));';

			return implode("\n", $css_array);
		}
	}

	/**
	* Apply browser prefixes to background-origin property 
	* see http://www.w3.org/TR/css3-background/#background-origin
	* see https://developer.mozilla.org/en/CSS/-moz-background-origin
	*
	* @return string
	*/
	function backgroundOrigin()
	{
		$vendor_prefix = array('', '-moz-', '-webkit-');
		
		$values = $this->EE->TMPL->fetch_param('value');

		foreach($vendor_prefix as $prefix) {
			$suffix = ($prefix === '-moz-') ? '' : '-box';
			$css_array[] = $prefix . 'background-origin:' . $values . $suffix . ';';				
		}

		return implode("\n", $css_array);
	}

	/**
	* Apply browser prefixes to background-size property 
	* see http://www.w3.org/TR/css3-background/#the-background-size
	* see https://developer.mozilla.org/en/CSS/-moz-background-size
	*
	* @return string
	*/
	function backgroundSize()
	{
		return $this->create_property('background-size', array('', '-khtml-', '-moz-', '-o-', '-webkit-'));
	}

	/**
	* Apply browser prefixes to border-image property 
	* see http://www.w3.org/TR/css3-background/#the-border-image
	* see https://developer.mozilla.org/en/CSS/-moz-border-image
	*
	* @return string
	*/
	function borderImage()
	{
		return $this->create_property('border-image');
	}

	/**
	* Apply browser prefixes to border-radius property 
	* see http://www.w3.org/TR/css3-background/#border-radius
	* see https://developer.mozilla.org/en/CSS/-moz-border-radius
	*
	* @return string
	*/
	function borderRadius()
	{
		global $TMPL, $vendor_prefix;
		
		$values = $this->EE->TMPL->fetch_param('value');

		$vendor_prefix = array('', '-moz-');

		$css_array = array();

		// push non-webkit values thru without change
		foreach($vendor_prefix as $prefix) {
			$css_array[] = $prefix . 'border-radius:' . $values . ';';
		}

		// get ready for webkit
		// check for the horizontal/vertical radii slash
		$slash = '&#47;';
		if (strpos($values, $slash) !== FALSE) {
			// if there aren't a whole lotta spaces involved,
			// this is probably a simple find slash and replace with space
			if (substr_count($values, ' ') <= 2) {
				$value = preg_replace("/\s?{$slash}\s?/", ' ', $values);
				$css_array = array_merge($css_array, build_css($value));
			}	
			else {
				$values_slash = explode($slash, $values);
				// now we should have an array with 2 values, break those up on the spaces
				$values_slash_0 = explode(' ', trim($values_slash[0]));
				$values_slash_1 = explode(' ', trim($values_slash[1]));
				// combine the horizontal and vertical radii
				$values_combined = array();
				foreach ($values_slash_0 as $key => $value) {
					$values_combined[] = $values_slash_0[$key] . ' ' . $values_slash_1[$key];
				}
				$css_array = array_merge($css_array, build_css($values_combined));
			}
		} 
		
		else {
			// bust up the Gecko-style shorthand
			$values_e = explode(' ', $values);
			$values_size = sizeof($values_e);
		
			if ($values_size == 1) {
				$css_array[] = '-webkit-border-radius:' . $values . ';';
			}
			elseif ($values_size == 4) {
				$css_array = array_merge($css_array, build_css($values_e));
			}
		}
		
		if (!function_exists('build_css')) {
			function build_css($val) {
				if (count($val) > 1) {
					$css_array[] = '-webkit-border-top-left-radius:' . $val[0] . ';';
					$css_array[] = '-webkit-border-top-right-radius:' . $val[1] . ';';
					$css_array[] = '-webkit-border-bottom-left-radius:' . $val[2] . ';';
					$css_array[] = '-webkit-border-bottom-right-radius:' . $val[3] . ';';
					return $css_array;				
				} 
				else {
					$css_array[] = '-webkit-border-radius:' . $val . ';';
					return $css_array;				
				}
			}
		}
		
		return implode("\n", $css_array);
	}

	/**
	* Apply browser prefixes to box-shadow property 
	* see https://developer.mozilla.org/en/CSS/-moz-box-shadow
	* see http://msdn.microsoft.com/en-us/library/ms532985(VS.85).aspx
	*
	* @return string
	*/
	function boxShadow()
	{
		$ie = $this->EE->TMPL->fetch_param('ie');

		if ($ie != '') {
			$values = $this->EE->TMPL->fetch_param('value');
			$values_array = explode(' ', $values);

			$ie_css = "progid:DXImageTransform.Microsoft.dropshadow(OffX={$values_array[0]},OffY={$values_array[1]},Color='{$values_array[3]}');";
			
			if ($ie == '8') {
				return '-ms-filter:"' . rtrim($ie_css, ';') . '";';
			}
			else {
				return 'filter:' . $ie_css;				
			}
		}
		else {
			return $this->create_property('box-shadow');
		}
	}

	/**
	* Apply browser prefixes to box-sizing property 
	* see http://www.w3.org/TR/css3-ui/#box-sizing
	* see https://developer.mozilla.org/en/CSS/box-sizing
	*
	* @return string
	*/
	function boxSizing()
	{
		return $this->create_property('box-sizing');
	}

	/**
	* Apply browser prefixes to transform:rotate() property 
	* see http://www.w3.org/TR/2009/WD-css3-2d-transforms-20091201/#transform-property
	* see https://developer.mozilla.org/en/CSS/-moz-transform
	* see http://msdn.microsoft.com/en-us/library/ms533014(VS.85).aspx
	*
	* @return string
	*/
	function transformRotate()
	{
		$values = $this->EE->TMPL->fetch_param('value');
		
		$ie = $this->EE->TMPL->fetch_param('ie');

		if ($ie != '') {
			// get the rotation integer
			$value_int = rtrim($values, 'deg');
			// prep for IE
			$value_ie = round(($value_int / 90), 3);
			$ie_css = "progid:DXImageTransform.Microsoft.BasicImage(rotation={$value_ie});";
			
			if ($ie == '8') {
				return '-ms-filter:"' . rtrim($ie_css, ';') . '";';
			}
			else {
				return 'filter:' . $ie_css;				
			}
		}
		else {
			$vendor_prefix = array('', '-moz-', '-o-', '-webkit-');
		
			foreach($vendor_prefix as $prefix) {
				$css_array[] = $prefix . 'transform:rotate(' . $values . ');';
			}

			return implode("\n", $css_array);
		}
	}

	/**
	* How to use this plugin
	*
	* @return string
	*/
	function usage()
	{
		ob_start();
	?>
USAGE
--------------------

For each advanced CSS3 property, use this plugin in a CSS template to spew out all the supporting browser prefixes (supports Gecko, Konqueror, Opera, and Webkit).

Refer to the Mozilla site for implementation ideas: https://developer.mozilla.org/en/CSS_Reference/Mozilla_Extensions

For example:

#rounded_box {
  {exp:css3:borderRadius value="1em 2em 3em 4em"}
}

...outputs:

#rounded_box {
  border-radius:1em 2em 3em 4em;
  -moz-border-radius:1em 2em 3em 4em;
  -webkit-border-top-left-radius:1em;
  -webkit-border-top-right-radius:2em;
  -webkit-border-bottom-left-radius:3em;
  -webkit-border-bottom-right-radius:4em;
}

And for IE, try:
<!--[if IE 8]>
.shadow {
  {exp:css3:boxShadow ie="8" value="1px 2px 3px #666666"}	
}
<![endif]-->

...which outputs:
<!--[if IE 8]>
.shadow {
  -ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=1px,OffY=2px,Color='#666666')";
}
<![endif]-->


MORE SAMPLES
--------------------

{exp:css3:backgroundClip value="content"}
see https://developer.mozilla.org/en/CSS/-moz-background-clip

{exp:css3:backgroundLinearGradient value="top, #fff, #000"}
{exp:css3:backgroundLinearGradient value="left, rgba(0,100,200,.5), rgba(200,200,200,.5)"}
see https://developer.mozilla.org/en/CSS/-moz-linear-gradient
Note: this tag follows a very basic syntax: "top||left,color,color". For more complex gradients, including radial, you're on your own for now.

{exp:css3:backgroundOrigin value="content"}
see https://developer.mozilla.org/en/CSS/-moz-background-origin

{exp:css3:backgroundSize value="100% 100%"}
see https://developer.mozilla.org/en/CSS/-moz-background-size

{exp:css3:borderImage value="url(image.png) 1px 2px 3px 4px"}
see https://developer.mozilla.org/en/CSS/-moz-border-image
Note: Make sure you use 1 or 4 space-delimited values. Since cases where 2 or 3 values would make sense seemed so rare, I didn’t bother supporting those. Also, for horizontal/vertical radii, use the Mozilla slash style.

{exp:css3:borderRadius value="1em"}
{exp:css3:borderRadius value="1em 2em 3em 4em"}
{exp:css3:borderRadius value="1em/1px"}
{exp:css3:borderRadius value="1em 2em 3em 4em / 1px 2px 3px 4px"}
see https://developer.mozilla.org/en/CSS/-moz-border-radius

{exp:css3:boxShadow value="1px 2px 3px rgba(0,0,0,.5)"}
see https://developer.mozilla.org/en/CSS/-moz-box-shadow

{exp:css3:boxSizing value="border-box"}
see https://developer.mozilla.org/en/CSS/box-sizing

{exp:css3:transformRotate value="10deg"}
see https://developer.mozilla.org/en/CSS/-moz-transform

INTERNET EXPLORER SUPPORT
--------------------
add the 'ie' parameter with the values 6 or 8

{exp:css3:backgroundLinearGradient ie="8" value="top, #fff, #000"}

{exp:css3:boxShadow ie="6" value="1px 2px 3px #666666"}

{exp:css3:transformRotate ie="6" value="10deg"}


CHANGELOG
--------------------
2.0.0 – 2010-03-17 – Added IE support for background gradient, box-shadow, and rotation. Created EE2.0 version.
1.1.2 – 2010-03-11 – Applied PHPDoc style. Cleaned up docs. Deleted display_error.
1.1.1 – 2010-03-10 – Documentation cleanup.
1.1.0 – 2010-03-10 – Added transform:rotate();. Corrected border-radius syntax for W3C CSS3 candidate.
1.0.1 – 2010-03-05 – Documentation cleanup.
1.0.0 – 2010-03-05 – Initial release.


	<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
}
?>