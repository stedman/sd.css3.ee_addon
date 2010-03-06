<?php
$plugin_info = array(
	'pi_name' => 'Css3',
	'pi_version' => '1.0.1',
	'pi_author' => 'Steve Stedman',
	'pi_author_url' => 'http://stedmandesign.com/',
	'pi_description' => 'For each advanced CSS3 property, use this plugin in a CSS template to spew out all the supporting browser prefixes (supports Gecko, Konqueror, Opera, and Webkit).',
	'pi_usage' => Css3::usage()
);

class Css3 {
	var $vendor_prefix;

	/*
		background-clip
		http://www.w3.org/TR/css3-background/#background-clip
		https://developer.mozilla.org/en/CSS/-moz-background-clip
	*/
	function backgroundClip()
	{
		global $TMPL;
		
		$vendor_prefix = array('', '-moz-', '-webkit-');
		
		$values = $TMPL->fetch_param('value');

		foreach($vendor_prefix as $prefix) {
			$suffix = ($prefix === '-moz-') ? '' : '-box';
			$css_array[] = $prefix . 'background-clip:' . $values . $suffix . ';';				
		}

		return implode("\n", $css_array);
	}

	/*
		background-origin
		http://www.w3.org/TR/css3-background/#background-origin
		https://developer.mozilla.org/en/CSS/-moz-background-origin
	*/
	function backgroundOrigin()
	{
		global $TMPL;
		
		$vendor_prefix = array('', '-moz-', '-webkit-');
		
		$values = $TMPL->fetch_param('value');

		foreach($vendor_prefix as $prefix) {
			$suffix = ($prefix === '-moz-') ? '' : '-box';
			$css_array[] = $prefix . 'background-origin:' . $values . $suffix . ';';				
		}

		return implode("\n", $css_array);
	}

	/*
		background-size
		http://www.w3.org/TR/css3-background/#the-background-size
		https://developer.mozilla.org/en/CSS/-moz-background-size
	*/
	function backgroundSize()
	{
		global $TMPL;
		
		$vendor_prefix = array('', '-khtml-', '-moz-', '-o-', '-webkit-');
		
		$values = $TMPL->fetch_param('value');

		foreach($vendor_prefix as $prefix) {
			$css_array[] = $prefix . 'background-size:' . $values . ';';
		}

		return implode("\n", $css_array);
	}

	/*
		border-image
		http://www.w3.org/TR/css3-background/#the-border-image
		https://developer.mozilla.org/en/CSS/-moz-border-image
	*/
	function borderImage()
	{
		global $TMPL;
		
		$vendor_prefix = array('', '-moz-', '-webkit-');
		
		$values = $TMPL->fetch_param('value');

		foreach($vendor_prefix as $prefix) {
			$css_array[] = $prefix . 'border-image:' . $values . ';';
		}

		return implode("\n", $css_array);
	}

	/*
		border-radius
		http://www.w3.org/TR/css3-background/#border-radius
		https://developer.mozilla.org/en/CSS/-moz-border-radius
	*/
	function borderRadius()
	{
		global $TMPL, $vendor_prefix;
		
		$values = $TMPL->fetch_param('value');

		$css_array = array();

		// push Gecko values thru without change
		$css_array[] = '-moz-border-radius:' . $values . ';';

		// prepare for other browsers
		$vendor_prefix = array('', '-webkit-');
		
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
			// bust up the Mozilla shorthand
			$values_e = explode(' ', $values);
			$values_size = sizeof($values_e);
		
			if ($values_size == 1) {
				foreach($vendor_prefix as $prefix) {
					$css_array[] = $prefix . 'border-radius:' . $values . ';';
				}
			}
			elseif ($values_size == 4) {
				$css_array = array_merge($css_array, build_css($values_e));
			}
		}
		
		if (!function_exists('build_css')) {
			function build_css($val) {
				global $vendor_prefix;

				if (count($val) > 1) {
					foreach($vendor_prefix as $prefix) {
						$css_array[] = $prefix . 'border-top-left-radius:' . $val[0] . ';';
						$css_array[] = $prefix . 'border-top-right-radius:' . $val[1] . ';';
						$css_array[] = $prefix . 'border-bottom-left-radius:' . $val[2] . ';';
						$css_array[] = $prefix . 'border-bottom-right-radius:' . $val[3] . ';';
					}				
					return $css_array;				
				} 
				else {
					foreach($vendor_prefix as $prefix) {
						$css_array[] = $prefix . 'border-radius:' . $val . ';';
					}
					return $css_array;				
				}
			}
		}
		
		return implode("\n", $css_array);
	}

	/*
		box-shadow
		https://developer.mozilla.org/en/CSS/-moz-box-shadow
	*/
	function boxShadow()
	{
		global $TMPL;
		
		$vendor_prefix = array('', '-moz-', '-webkit-');
		
		$values = $TMPL->fetch_param('value');

		foreach($vendor_prefix as $prefix) {
			$css_array[] = $prefix . 'box-shadow:' . $values . ';';
		}

		return implode("\n", $css_array);
	}

	/*
		box-sizing
		http://www.w3.org/TR/css3-ui/#box-sizing
		https://developer.mozilla.org/en/CSS/box-sizing
	*/
	function boxSizing()
	{
		global $TMPL;
		
		$vendor_prefix = array('', '-moz-', '-webkit-');
		
		$values = $TMPL->fetch_param('value');
		
		foreach($vendor_prefix as $prefix) {
			$css_array[] = $prefix . 'box-sizing:' . $values . ';';
		}

		return implode("\n", $css_array);
	}

	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------

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

-moz-border-radius:1em 2em 3em 4em;
border-top-left-radius:1em;
border-top-right-radius:2em;
border-bottom-left-radius:3em;
border-bottom-right-radius:4em;
-webkit-border-top-left-radius:1em;
-webkit-border-top-right-radius:2em;
-webkit-border-bottom-left-radius:3em;
-webkit-border-bottom-right-radius:4em;
	
MORE SAMPLES
--------------------

{exp:css3:backgroundClip value="content"}
see https://developer.mozilla.org/en/CSS/-moz-background-clip

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

	<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
	// END
}
?>