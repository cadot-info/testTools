# library for simplify tests in Symfony

## Installation

`require cadot-info/testTools`

```php
use CadotInfo\Tools;

class ClientTest extends PantherTestCase
{
   use Tools;
   ...
   $liens = $this->returnAllLinks('/', 1, null, ['mailto',  'javascript'], [''], ['bigpicture']);
        foreach ($liens as $url => $texte) {
            dump("Test url:$url(texte)");
   ...
   $this->E('test of links');

```

## Get All Links of web page or html string

Return a array of url links of page and sub-page:

**_example:_**
`$links=$this->returnAllLinks('https://github.com');`

or by html

`$links = $this->returnAllLinks($html);`

## Utilisation

getLinks(start url, int levels, array of $options, array of $links)

-2points => refuse the links before : ,for example with mailto,javascript,..
-point => refuse the links before . ,for example https://github.
-class => refuse the links with this classes, example: bigpicture button ...
-link => refuse this links for example https:github.com, www.google.com ...
-start => refuse link start for example /profiler, http://google
-pass => if true, if a link is refused, the code seek in this link for recursivity

```php
use function CadotInfo\getLinks;

class ...
{
   use getLinks;
   ...
   $liens = getLinks('wwwh.goole.fr', 1,);
        foreach ($liens as $url => $texte) {
            dump("Test url:$url(texte)");
   ...
   $this->E('test of links');

```

### tests links

(http://google.fr)
(http://thispagedontexiste.exist)
