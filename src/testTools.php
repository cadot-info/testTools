<?php
/*
 * Created on Wed Nov 03 2021 by The MIT License (MIT) Copyright (c) 2021 Cadot.info,licence: http://spdx.org/licenses/MIT.html webmestre@cadot.info
 *
 *
 *-------------------------------------------------------------------------- *
 *      getLinksTrait.php *
 * -------------------------------------------------------------------------- *
 *
 * Usage:
 * - composer require cadot.info/getLinks
 * - use function CadotInfo\getLinks;
 *
 * Source on: https://github.com/cadot-info/getLinks
 */

namespace CadotInfo;

use Exception;

use DOMDocument;

/**
 * 
 */
trait testTools
{

    /**
     * E funtion for send message immediatly
     *
     * @param  mixed $texte
     * @return void
     */
    function E($texte): void
    {
        if (!is_string($texte)) {
            $texte = json_encode($texte);
        }
        echo "- " . ucfirst($texte) . "\n";
        ob_flush();
    }

    /**
     * getLinksByClient function for call getLinks with a client, ideal after login
     * 
     * * Options accepted
     * - 2points => refuse the links before : ,for example with mailto,javascript,..
     * - point => refuse the links before . ,for example https://github.
     * - class => refuse the links with this classes, example: bigpicture button ...
     * - link => refuse this links for example https:github.com, www.google.com ...
     * - begin => refuse link start for example /profiler, http://google
     * - pass => if true, if a link is refused, the code seek in this link for recursivity
     *
     * @param  mixed $client
     * @param  string $url
     * @param  int $level
     * @param  array $opts option for getLinks
     * @param  mixed $liens for recurivity
     */
    function getLinksByClient($client, string $url, int $level = 0, $opts = [], $liens = [])
    {
        $client->request('GET', $url);
        $news = $this->getLinks($client->getCrawler()->html(), 0, $opts);
        if ($level > 0) {
            foreach ($news as $nurl => $ntexte) {
                $nnews = $this->getLinksByClient($client, $nurl, $level - 1, $opts);
                $liens = array_merge($liens, $nnews);
                array_unique($liens);
            }
        } else {
            $liens = array_merge($liens, $news);
            array_unique($liens);
        }
        return $liens;
    }

    /**
     * getLinks
     *
     * Options accepted
     * - 2points => refuse the links before : ,for example with mailto,javascript,..
     * - point => refuse the links before . ,for example https://github.
     * - class => refuse the links with this classes, example: bigpicture button ...
     * - link => refuse this links for example https:github.com, www.google.com ...
     * - begin => refuse link start for example /profiler, http://google
     * - pass => if true, if a link is refused, the code seek in this link for recursivity
     *
     * @param  string start url
     * @param  int    level for seek
     * @param  array  options
     * @param  array  links for recurivity or get links
     * @return void
     *

     */
    public function getLinks(string $start, int $descent = 0, array $options = [], array $links = [])
    {
        /* ------------------------------ default value ----------------------------- */
        $opts = [
            '2points' => ['mailto',  'javascript'],
            'point' => [],
            'class' => [],
            'link' => [],
            'begin' => [],
            'pass' => false
        ];
        // verify optioons exis
        if (count($options) > 0) {
            if (count(array_intersect_key($options, $opts)) == 0) {
                return new Exception('An option is not recognized');
            }
        }


        foreach ($opts as $key => $value) {
            if (isset($options[$key]) && $options[$key] != null) {
                $opts[$key] = $options[$key];
            }
        }
        $exlinks = $links;
        $htmlDom = new DOMDocument;
        libxml_use_internal_errors(true);
        if (substr($start, 0, strlen('<!DOCTYPE html>')) == '<!DOCTYPE html>' || substr($start, 0, strlen('<html')) == '<html') {
            $htmlDom->loadHTML($start);
        } else {
            @$htmlDom->loadHTMLFile($start);
        } // pass if error
        foreach ($htmlDom->getElementsByTagName('a') as $link) { // no get link without href
            if ($link->hasAttribute('href')) {
                $url = $link->getAttribute('href');
                $refuse = false;
                if (
                    $url == '' || //href?
                    in_array($url, $opts['link']) || //link forbidden?
                    in_array(explode(':', $url)[0], $opts['2points']) || //before : for mailto for example
                    in_array(explode('.', $url)[0], $opts['point']) ||  // before . for http://noThisDomain for example
                    isset($exlinks[$url])  || //i have this link
                    count(array_intersect($opts['class'], explode(' ', $link->getAttribute('class')))) > 0 // no this class
                ) {
                    $refuse = true;
                }
                if ($refuse == false) {
                    foreach ($opts['begin'] as $begin) {
                        if (substr($url, 0, strlen($begin)) == $begin) { //not start
                            $refuse = true;
                        }
                    }
                }

                if ($descent > 0) { // for test level in recurivity
                    if ($opts['pass'] == true || $refuse == false) {
                        $links = $this->getLinks($url, $descent - 1, $opts, $links);
                    }
                } else {
                    if ($refuse == false) {
                        $links[$url] = trim(preg_replace('/\s+/', ' ', str_replace(array("\n", "\r", ""), '', $link->nodeValue)));
                    }
                }
            }
        }
        libxml_clear_errors();
        return $links;
    }
}
