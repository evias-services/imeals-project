<?php

class AppLib_Utils
{
    /**
     * return url representation of text, replaces special characters
	 * with '-' sign
     *
	 * @todo : replace äüïîûêâéèç by corresponding alpha char
     *
     * @param  string $url
     * @return string
     */
	static public function cleanUrl($url)
	{
		$url = strtolower($url);
		$url = preg_replace(array('/&szlig;/',
                                '/&(..)lig;/',
                                '/&([aeiouAEIOU])uml;/',
                                '/&(.)[^;]*;/'),
                            array('ss',
                                "$1",
                                "$1",
                                "$1"),
                            $url);

		/* strip non alpha characters */
		$url = preg_replace(array('/[^[:alpha:]\d\.]/', '/-+/'), '-', $url);

		// remove eventual leading/trailing hyphens due to leading/trailing non-alpha chars
		return trim($url, '-');
	}

    static public function cleanRegExp($str)
    {
        /* It is important to replace all back-slashes first. */
        $protected = array(
            "\\", "/", "*", "+", "?",
            "|", "{", "[", "(", ")",
            "^", "$", ".", "#");

        $replacement = array_map(function($item) {
            return "\\" . $item;
        }, $protected);

        return str_replace($protected, $replacement, $str);
    }

    static public function suffixNthIn($needle, array $haystack)
    {
        $link_label = $needle;
        if (in_array($link_label, $haystack)) {
            $n = 1;
            while (in_array($link_label . "-$n", $haystack))
                ++$n;

            $link_label .= "-$n";
        }
        return $link_label;
    }

}
