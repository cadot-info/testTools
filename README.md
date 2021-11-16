# library for simplify tests in Symfony

## Installation

`composer require cadot.info/testtools`

and use by traits

```php
use CadotInfo\testTools;

class ClientTest extends PantherTestCase
{
   use testTools;
   ...
   $liens = $this->returnAllLinks('/'); //return links of pages
   ...
   $this->E('test of links');  // echo immediatly
   ...
   $this->getLinksByClient($client, '/compte', 1,$opts)  //function for inject client for page with login

```

## Get All Links of web page or html string

Return a array of url links of page and sub-page:

**_example:_**
`$links=$this->returnAllLinks('https://github.com');`

or by html

`$links = $this->returnAllLinks($html);`

## Utilisation

getLinks(start url, int levels, array of $options, array of $links)

     * - 2points => refuse the links before : ,for example with mailto,javascript,..
     * - point => refuse the links before . ,for example https://github.
     * - class => refuse the links with this classes, example: bigpicture button ...
     * - link => refuse this links for example https:github.com, www.google.com ...
     * - begin => refuse link start for example /profiler, /profiler/123
     * - finish => refuse link finish for example /deleted, http://google.fr/deleted
     * - pass => if true, if a link is refused, the code seek in this link for recursivity

### example complete

```php

<?php
namespace App\Tests;
use CadotInfo\testTools;
use Zenstruck\Browser\Test\HasBrowser;
use Symfony\Component\Panther\PantherTestCase;

class ClientTest extends PantherTestCase
{
    use HasBrowser;
    use testTools;
    //tests of links
    public function testLoginAndLinks()
    {
        $client = static::createPantherClient();
        $client->request('GET', '/login');
        $client->submitForm('Submit', [
            'email' => 'a@aa.aa',
            'password' => 'password'
        ]);
        $this->assertPageTitleSame('Administration');
        $this->E('Connexion ok');

        /* ------------------------- recup of links ------------------------- */
        $links = $this->getLinksByClient($client, '/', 1, ['begin' => ['https://symfony.com', '#trace-box', 'https://www.facebook.com/'], 'class' => ['bigpicture']]);
        $this->E(count($links) . "\n" . print_r($links)); //echo numbers links and list of links

        /* ---------------------------- assert of links ---------------------------- */
        foreach ($links as $url => $texte) {
            $this->E("Test of $url ($texte)");
            $client->followRedirects();
            $client->request('GET', $url);
            $this->assertSelectorExists('#logo'); // element present on all pages, it's possible he is simple <span id="logo">
        }
    }
}
```
