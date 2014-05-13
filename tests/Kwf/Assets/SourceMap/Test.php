<?php
/*
 * Test Contents based on https://github.com/mozilla/source-map test/source-map/util.js
 * Copyright 2011 Mozilla Foundation and contributors
 * Licensed under the New BSD license. See LICENSE or:
 * http://opensource.org/licenses/BSD-3-Clause
*/
class Kwf_Assets_SourceMap_Test extends Kwf_Test_TestCase
{

    // This is a test mapping which maps functions from two different files
    // (one.js and two.js) to a minified generated source.
    //
    // Here is one.js:
    //
    //   ONE.foo = function (bar) {
    //     return baz(bar);
    //   };
    //
    // Here is two.js:
    //
    //   TWO.inc = function (n) {
    //     return n + 1;
    //   };
    //
    // And here is the generated code (min.js):
    //
    //   ONE.foo=function(a){return baz(a);};
    //   TWO.inc=function(a){return a+1;};
    private static $testGeneratedCode = " ONE.foo=function(a){return baz(a);};\n TWO.inc=function(a){return a+1;};";
    private static $testMap = '{
        "version": 3,
        "file": "min.js",
        "names": ["bar", "baz", "n"],
        "sources": ["one.js", "two.js"],
        "sourceRoot": "/the/root",
        "mappings": "CAAC,IAAI,IAAM,SAAUA,GAClB,OAAOC,IAAID;CCDb,IAAI,IAAM,SAAUE,GAClB,OAAOA"
    }';
    private static $testMapWithSourcesContent = '{
        "version": 3,
        "file": "min.js",
        "names": ["bar", "baz", "n"],
        "sources": ["one.js", "two.js"],
        "sourcesContent": [
        " ONE.foo = function (bar) {
   return baz(bar);
 };",
        " TWO.inc = function (n) {
   return n + 1;
 };"
        ],
        "sourceRoot": "/the/root",
        "mappings": "CAAC,IAAI,IAAM,SAAUA,GAClB,OAAOC,IAAID;CCDb,IAAI,IAAM,SAAUE,GAClB,OAAOA"
    }';
    private static $emptyMap = '{
        "version": 3,
        "file": "min.js",
        "names": [],
        "sources": [],
        "mappings": ""
    }';

    public function testBase64Vlq()
    {
        for ($i = -255; $i < 256; $i++) {
            $result = Kwf_Assets_Util_Base64VLQ::decode(Kwf_Assets_Util_Base64VLQ::encode($i));
            $this->assertEquals($result['value'], $i);
            $this->assertEquals($result['rest'], "");
        }
    }

    public function testEmptyMap()
    {
        $map = new Kwf_Assets_Util_SourceMap(self::$emptyMap, '');
        $mappings = $map->getMappings();
        $this->assertEquals(count($mappings), 0);
    }

    public function testGetMappings()
    {
        $map = new Kwf_Assets_Util_SourceMap(self::$testMap, self::$testGeneratedCode);
        $mappings = $map->getMappings();
        $this->assertEquals(count($mappings), 13);
        $this->assertEquals($mappings[0], array(
            'generatedLine' => 1,
            'generatedColumn' => 1,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 1,
            'originalColumn' => 1,
        ));
        $this->assertEquals($mappings[1], array(
            'generatedLine' => 1,
            'generatedColumn' => 5,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 1,
            'originalColumn' => 5,
        ));
        $this->assertEquals($mappings[2], array(
            'generatedLine' => 1,
            'generatedColumn' => 9,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 1,
            'originalColumn' => 11,
        ));
        $this->assertEquals($mappings[3], array(
            'generatedLine' => 1,
            'generatedColumn' => 18,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 1,
            'originalColumn' => 21,
            'name' => 'bar'
        ));
        $this->assertEquals($mappings[4], array(
            'generatedLine' => 1,
            'generatedColumn' => 21,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 2,
            'originalColumn' => 3,
        ));
        $this->assertEquals($mappings[5], array(
            'generatedLine' => 1,
            'generatedColumn' => 28,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 2,
            'originalColumn' => 10,
            'name' => 'baz'
        ));
        $this->assertEquals($mappings[6], array(
            'generatedLine' => 1,
            'generatedColumn' => 32,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 2,
            'originalColumn' => 14,
            'name' => 'bar'
        ));


        $this->assertEquals($mappings[7], array(
            'generatedLine' => 2,
            'generatedColumn' => 1,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 1,
            'originalColumn' => 1,
        ));
        $this->assertEquals($mappings[8], array(
            'generatedLine' => 2,
            'generatedColumn' => 5,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 1,
            'originalColumn' => 5,
        ));
        $this->assertEquals($mappings[9], array(
            'generatedLine' => 2,
            'generatedColumn' => 9,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 1,
            'originalColumn' => 11,
        ));
        $this->assertEquals($mappings[10], array(
            'generatedLine' => 2,
            'generatedColumn' => 18,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 1,
            'originalColumn' => 21,
            'name' => 'n'
        ));
        $this->assertEquals($mappings[11], array(
            'generatedLine' => 2,
            'generatedColumn' => 21,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 2,
            'originalColumn' => 3,
        ));
        $this->assertEquals($mappings[12], array(
            'generatedLine' => 2,
            'generatedColumn' => 28,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 2,
            'originalColumn' => 10,
            'name' => 'n'
        ));
    }

    public function testStringReplace()
    {
        $map = new Kwf_Assets_Util_SourceMap(self::$testMap, self::$testGeneratedCode);
        $map->stringReplace('baz', 'asdfasdf');

             //0        1         2         3         4
             //1234567890123456789012345678901234567890123
        $s = " ONE.foo=function(a){return asdfasdf(a);};\n".
             " TWO.inc=function(a){return a+1;};";
        $this->assertEquals($map->getFileContents(), $s);

        $mappings = $map->getMappings();
        $this->assertEquals($mappings[5], array(
            'generatedLine' => 1,
            'generatedColumn' => 28, //must not change
            'originalSource' => '/the/root/one.js',
            'originalLine' => 2,
            'originalColumn' => 10,
            'name' => 'baz'
        ));
        $this->assertEquals($mappings[6], array(
            'generatedLine' => 1,
            'generatedColumn' => 32+5,  //this neets to be shifted
            'originalSource' => '/the/root/one.js',
            'originalLine' => 2,
            'originalColumn' => 14,
            'name' => 'bar'
        ));

        //first of line 2
        $this->assertEquals($mappings[7], array(
            'generatedLine' => 2,
            'generatedColumn' => 1, //must not change
            'originalSource' => '/the/root/two.js',
            'originalLine' => 1,
            'originalColumn' => 1,
        ));
    }

    public function testStringReplaceSecondLine()
    {
        $map = new Kwf_Assets_Util_SourceMap(self::$testMap, self::$testGeneratedCode);
        $map->stringReplace('inc', 'increment');

             //0        1         2         3         4
             //1234567890123456789012345678901234567890123
        $s = " ONE.foo=function(a){return baz(a);};\n".
             " TWO.increment=function(a){return a+1;};";
        $this->assertEquals($map->getFileContents(), $s);

        $mappings = $map->getMappings();
        //last of line 1
        $this->assertEquals($mappings[6], array(
            'generatedLine' => 1,
            'generatedColumn' => 32,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 2,
            'originalColumn' => 14,
            'name' => 'bar'
        ));


        $this->assertEquals($mappings[7], array(
            'generatedLine' => 2,
            'generatedColumn' => 1, //don't change
            'originalSource' => '/the/root/two.js',
            'originalLine' => 1,
            'originalColumn' => 1,
        ));
        $this->assertEquals($mappings[8], array(
            'generatedLine' => 2,
            'generatedColumn' => 5, //don't change
            'originalSource' => '/the/root/two.js',
            'originalLine' => 1,
            'originalColumn' => 5,
        ));
        $this->assertEquals($mappings[9], array(
            'generatedLine' => 2,
            'generatedColumn' => 9+6,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 1,
            'originalColumn' => 11,
        ));
        $this->assertEquals($mappings[10], array(
            'generatedLine' => 2,
            'generatedColumn' => 18+6,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 1,
            'originalColumn' => 21,
            'name' => 'n'
        ));
        $this->assertEquals($mappings[12], array(
            'generatedLine' => 2,
            'generatedColumn' => 28+6,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 2,
            'originalColumn' => 10,
            'name' => 'n'
        ));
    }

    public function testStringReplaceMultipleInOneLine()
    {
        $map = new Kwf_Assets_Util_SourceMap(self::$testMap, self::$testGeneratedCode);
        $map->stringReplace('a', 'xbbbbxxxxxxxx');

             //0        1         2         3         4
             //1234567890123456789012345678901234567890123
          //   ONE.foo=function(a){return baz(a);};
        $s = " ONE.foo=function(xbbbbxxxxxxxx){return bxbbbbxxxxxxxxz(xbbbbxxxxxxxx);};\n".
          //   TWO.inc=function(a){return a+1;};
             " TWO.inc=function(xbbbbxxxxxxxx){return xbbbbxxxxxxxx+1;};";
        $this->assertEquals($map->getFileContents(), $s);

        $mappings = $map->getMappings();
        $this->assertEquals($mappings[3], array(
            'generatedLine' => 1,
            'generatedColumn' => 18,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 1,
            'originalColumn' => 21,
            'name' => 'bar'
        ));
        $this->assertEquals($mappings[4], array(
            'generatedLine' => 1,
            'generatedColumn' => 21+12,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 2,
            'originalColumn' => 3,
        ));
        $this->assertEquals($mappings[5], array(
            'generatedLine' => 1,
            'generatedColumn' => 28+12,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 2,
            'originalColumn' => 10,
            'name' => 'baz'
        ));
        $this->assertEquals($mappings[6], array(
            'generatedLine' => 1,
            'generatedColumn' => 32+12+12,
            'originalSource' => '/the/root/one.js',
            'originalLine' => 2,
            'originalColumn' => 14,
            'name' => 'bar'
        ));


        $this->assertEquals($mappings[10], array(
            'generatedLine' => 2,
            'generatedColumn' => 18,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 1,
            'originalColumn' => 21,
            'name' => 'n'
        ));
        $this->assertEquals($mappings[11], array(
            'generatedLine' => 2,
            'generatedColumn' => 21+12,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 2,
            'originalColumn' => 3,
        ));
        $this->assertEquals($mappings[12], array(
            'generatedLine' => 2,
            'generatedColumn' => 28+12,
            'originalSource' => '/the/root/two.js',
            'originalLine' => 2,
            'originalColumn' => 10,
            'name' => 'n'
        ));
    }
}
