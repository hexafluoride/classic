<?php

/*
 * Fish format!
 * 
 * Version 0.1
 * 
 */

require "parser.php";
require "embed.php";
require "view.php";
require "edit.php";

function FishFormat($text, $action='markup')
{
    switch($action)
    {
        case "strip":
            $parsed = find_markup($text);
            $markup = filter_markup($parsed['markup']);

            foreach($markup as $filtered)
            {
                $text = replace_once($filtered['source'], $filtered['text'], $text);
            }

            // Remove newlines
            $text = str_replace("\n", "", $text);

            // Filter links
            $text = preg_replace("/(?:\[\[|{{)([^\]\|}]+)(?:\]\]|}})/", "\\1", $text);
            $text = preg_replace("/(?:\[\[|{{)[^\]}]+\|([^\]}]+)(?:\]\]|}})/", "\\1", $text);

            $output = $text;
        break;

        case "edit":
            $output = str_replace("\t", "    ", $text);

            while(preg_match('/^ *:+/m', $output)) {
                $output = preg_replace('/^( *):/m','\1    ', $output); }

            // Filter links
            $output = str_replace(array('[[', ']]', '{{', '}}'), array('&91;&91;', '&93;&93;', '&123;&123;', '&125;&125;'), $output);

            // Rewrite specific tags (images, soundcloud, date)
            $parsed = find_markup($output);
            $output = rewrite_markup($parsed['input'], $parsed['markup']);

            // Un-filter links
            $output = str_replace(array('&91;&91;', '&93;&93;', '&123;&123;', '&125;&125;'), array('[[', ']]', '{{', '}}'), $output);
        break;
        
        case "format":
            $output = preg_replace('/(\w{14})/', "$1&#8203;", $text);
        break;

        default:
            $output = $text;
            $output = str_replace("    ", "&emsp;&emsp;&emsp;", $output);

            // Links with custom text
            $output = preg_replace_callback('/(?:{{|\[\[)([\w -@\/~]+?)\|([\w -@\/~]+?)(?:\]\]|}})/', "custom_link", $output);

            // Basic links
            $output = preg_replace_callback('/(?:{{|\[\[)([\w -@\/~]+?)(?:\]\]|}})(s)?/', "basic_link", $output);

            // Replace semicolon tags with HTML entities (for writing documentation)
            $output = str_replace(array(":{", "}:", ':[', ']:'), array("&#123;", "&#125;", "&#91;", "&#93;"), $output);
            
            $Keywords = array('Ad|Ads',
                            'Pre',
                            'Flash',
                            'Color',
                            'Bold|B',
                            'Box|Title|Infobox|TitleBox',
                            'Underline|U',
                            'Italics?|I',
                            'Strike|S',
                            'Heading|SubHeading',
                            'Big',
                            'Medium|Med',
                            'Small|Sml',
                            'URL',
                            'Image|IMG',
                            'Redirect',
                            'Video|Youtube|Vimeo|Vine|Ted',
                            'SoundCloud',
                            'Load',
                            'Music',
                            'Snow',
                            '(?:Double|Dbl)?Rainbow2?',
                            'Glitch',
                            'Embed',
                            'Center',
                            'Right|Left',
                            'Playlist',
                            'Style',
                            'Total',
                            'Anchor',
                            'Codepen',
                            'FB|FishBux',
                            'NSFW',
                            'Snip|Hide');
                            
            $Keywords = implode('|', $Keywords);

            $markup = find_markup($output);

            if($_SESSION['admin'] && 1 == 2)
            {
                echo "<pre style='background-color: #000;'>";
                print_r($markup);
                echo "</pre>";
            }

            while(preg_match("/\b($Keywords), [^\S\n]* ($Keywords) [^\S\n]* (?:(\S) [\[{] \s* ([^\[{]*) \s* [}\]] \\3 | [\[{] \s* ([^\[{]*?) \s* [\]}])/xis", $output)) {
                $output = preg_replace_callback("/\b($Keywords), [^\S\n]* ($Keywords) [^\S\n]* (?:(\S) [\[{] \s* ([^\[{]*) \s* [\]}] \\3 | [\[{] \s* ([^\[{]*?) \s* [\]}])/xis", "ReplaceKeyPENIS", $output); }

            while(preg_match("/\b($Keywords), [^\S\n]* ($Keywords) [^\S\n]* (?:(\S) [\[{] \s* (.*) \s* [}\]] \\3 | [\[{] \s* (.*?) \s* [\]}])/xis", $output)) {
                $output = preg_replace_callback("/\b($Keywords), [^\S\n]* ($Keywords) [^\S\n]* (?:(\S) [\[{] \s* (.*) \s* [\]}] \\3 | [\[{] \s* (.*?) \s* [\]}])/xis", "ReplaceKeyPENIS", $output); }

            while(preg_match("/\b($Keywords) [^\S\n]* (?:(\S) [\[{] \s* ([^\[{]*) \s* [\]}] \\2 | [\[{] \s* ([^\[{]*?) \s* [\]}])/xis", $output)) {
                $output = preg_replace_callback("/\b($Keywords) [^\S\n]* (?:(.) [\[{] \s* ([^\[{]*) \s* [\]}] \\2 | [\[{] \s* ([^\[{]*?) \s* [\]}])/xis", "ReplaceKeywords", $output); }

            while(preg_match("/\b($Keywords) [^\S\n]* (?:(\S) [\[{] \s* (.*) \s* [\]}] \\2 | [\[{] \s* (.*?) \s* [\]}])/xis", $output)) {
                $output = preg_replace_callback("/\b($Keywords) [^\S\n]* (?:(.) [\[{] \s* (.*) \s* [\]}] \\2 | [\[{] \s* (.*?) \s* [\]}])/xis", "ReplaceKeywords", $output); }


            $Search[':Z'] = "<span class='warning'>:Z</span>";
            $Search[':downy:'] = "<span class='warning medium' style='font-family:helvetica'>.'<u>/</u>)</span>";
            $Search[':dunno'] = "<span class='warning'>¯\(º_o)/¯</span>";
            
            foreach($Search as $Key => $Value)
            {
                $output = str_replace($Key, $Value, $output);
            }
            
            $output = str_replace("&lt;3", "<span class='error'>&lt;3</span>", $output);

            // Ordered and unordered lists
            $output = preg_replace_callback("/(?:(?:^|\n)\s*(\*|\-|\#)\s+[^\n]+(?:\n|$))+/m", 'replace_lists', $output);

            // Allow HTML comments
            $output = str_replace(array('&lt;!--', '--&gt;'), array('<!--', '-->'), $output);

            // Strip newlines around comments
            $output = preg_replace('{\n*(<!--|-->)\n*}', "\\1", $output);            
            
            // Strip newlines between images.
            $output = preg_replace('{div>\n+<div}', "div><div", $output);
            
            // Strip newlines after titles and headings
            $output = preg_replace('{header>\n+}', "header>", $output);
            $output = preg_replace('{<hr /></span>\n+}', '<hr /></span>', $output);
            $output = str_replace("<hr />\n", "<hr />", $output);

            // Replace newlines with line breaks
            $output = str_replace("\n", "<br />", $output);
        break;
    }
    
    return $output;
}

?>
