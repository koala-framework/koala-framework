<?php
/**
 * @group Kwc_Basic_Text
 */
class Kwc_Basic_Text_MailParserTest extends Kwc_TestAbstract
{
    public function testBasic()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);

        $mailOut = $mailParser->parse('hallo');
        $this->assertEquals("hallo", $mailOut);

        $mailOut = $mailParser->parse("hallo du");
        $this->assertEquals("hallo du", $mailOut);

        $mailOut = $mailParser->parse("hallo        \n    du");
        $this->assertEquals("hallo du", $mailOut);

        $mailOut = $mailParser->parse('<p>hallo</p><h1>Foo</h1>');
        $this->assertEquals("hallo\n\nFoo\n\n", $mailOut);

        $mailOut = $mailParser->parse("hallo  du");
        $this->assertEquals("hallo du", $mailOut);

        $mailOut = $mailParser->parse("    hallo        \n    du    ");
        $this->assertEquals("hallo du", $mailOut);

        $mailOut = $mailParser->parse("\nhallo        \n    du\n");
        $this->assertEquals("hallo du", $mailOut);

        $mailOut = $mailParser->parse("hallo\ndu");
        $this->assertEquals('hallo du', $mailOut);
        return;

    }

    public function testNewsletter()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);

//         $mailOut = $mailParser->parse("&nbsp;hallo");
//         //d($mailOut);
//         $this->assertEquals(" hallo", $mailOut);

        $mailOut = $mailParser->parse("<p>\n<strong>Sehr geehrte Frau Mag Gruber,</strong>\n</p>\n<p>\nder beste Weg, um Treibstoff zu sparen, ist der Kauf eines neuen Volkswagen. Ab\nsofort gibt es daher bis zu EUR 2.000,-&nbsp;Spritspar-Prämie* bei Eintausch eines\nmindestens 2 Jahre alten Fahrzeuges und Kauf eines neuen, sparsamen und\numweltfreundlichen Volkswagen (VW Pkw und VW Nutzfahrzeuge). Schnell zugreifen, die\nAktion ist streng limitiert!\n</p>\n<p>\n<strong>Viel Spaß beim Lesen</strong>\n<br /><strong>Ihr Volkswagen Online Team</strong></p>\n");
        //d($mailOut);
        $this->assertEquals("Sehr geehrte Frau Mag Gruber,\n".
                "der beste Weg, um Treibstoff zu sparen, ist der Kauf eines neuen\n".
                "Volkswagen. Ab sofort gibt es daher bis zu EUR 2.000,- Spritspar-Prämie*\n".
                "bei Eintausch eines mindestens 2 Jahre alten Fahrzeuges und Kauf eines\n".
                "neuen, sparsamen und umweltfreundlichen Volkswagen (VW Pkw und VW\n".
                "Nutzfahrzeuge). Schnell zugreifen, die Aktion ist streng limitiert!\n".
                "Viel Spaß beim Lesen\n".
                "Ihr Volkswagen Online Team\n", $mailOut);

    }

    public function testStrong1()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);
        $mailOut = $mailParser->parse("<strong>test</strong>");
        $this->assertEquals("test", $mailOut);
    }

    public function testStrong2()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);
        $mailOut = $mailParser->parse("abc <strong>test</strong>");
        $this->assertEquals("abc test", $mailOut);
    }

    public function testStrong3()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);
        $mailOut = $mailParser->parse("abc<strong> test</strong>");
        $this->assertEquals("abc test", $mailOut);
    }

    public function testBr()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);

        $mailOut = $mailParser->parse("Foo<br />\nBar");
        $this->assertEquals("Foo\nBar", $mailOut);

        $mailOut = $mailParser->parse("Foo<br /> Bar");
        $this->assertEquals("Foo\nBar", $mailOut);

        $mailOut = $mailParser->parse("Foo<br />\n \nBar");
        $this->assertEquals("Foo\nBar", $mailOut);

        $mailOut = $mailParser->parse("Foo <br /> Bar");
        $this->assertEquals("Foo\nBar", $mailOut);

        $mailOut = $mailParser->parse("Foo  <br />  Bar");
        $this->assertEquals("Foo\nBar", $mailOut);

        $mailOut = $mailParser->parse("  Foo  <br />  Bar  ");
        $this->assertEquals("Foo\nBar", $mailOut);

        $mailOut = $mailParser->parse("Foo <br />Bar");
        $this->assertEquals("Foo\nBar", $mailOut);

        $mailOut = $mailParser->parse("<p>Foo<br />Bar</p>");
        $this->assertEquals("Foo\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("<p>Foo<br />\nBar</p>");
        $this->assertEquals("Foo\nBar\n", $mailOut);
        return;

    }
    
//     public function test2cData() //sometimes the XML parser splits one cData into 2
//     {
//         $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);
//         $mailOut = $mailParser->parse("Ã¼berrascht er Ã–sterreich miasdeiner Gewinnchance, die es so noch nie zuvor gegeben hat:+nbsp;Mit der groÃŸen Social Media-Kampagne asdasdasda+nbsp;suchen wir 300 Testfahrer+nbsp;fÃ¼r ein halbes Jahr fÃ¼r 300 neue up!. Sie haben ab+nbsp;heute zwei spektakulÃ¤re Monate Zeit, sich zu qualifizieren. Die einzigen Bedingungen: FÃ¼hrerscheinbesitz, Wohnsitz in Ã–sterreich+nbsp;und die UnterstÃ¼tzung von Freunden durch+nbsp;deren Klicks.");
//         $this->assertEquals("Ã¼berrascht er Ã–sterreich miasdeiner Gewinnchance, die es so noch nie\n".
//                 "zuvor gegeben hat: Mit der groÃŸen Social Media-Kampagne asdasdasda\n".
//                 "suchen wir 300 Testfahrer fÃ¼r ein halbes Jahr fÃ¼r 300 neue up!. Sie\n".
//                 "haben ab heute zwei spektakulÃ¤re Monate Zeit, sich zu qualifizieren. Die\n".
//                 "einzigen Bedingungen: FÃ¼hrerscheinbesitz, Wohnsitz in Ã–sterreich und\n".
//                 "die UnterstÃ¼tzung von Freunden durch deren Klicks.", $mailOut);
//     }

    public function testP1()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);

        $mailOut = $mailParser->parse("<p>Foo</p>");
        $this->assertEquals("Foo\n", $mailOut);

        $mailOut = $mailParser->parse("<p>Foo</p>\n<p>Bar</p>\n");
        $this->assertEquals("Foo\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("<p>Foo</p><p>Bar</p>");
        $this->assertEquals("Foo\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("<p>Foo</p>\n           <p>Bar</p>");
        $this->assertEquals("Foo\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("<p>Foo          </p>\n<p>Bar</p>");
        $this->assertEquals("Foo\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("<p>Foo\n  Bar\n  Bam</p>\n");
        $this->assertEquals("Foo Bar Bam\n", $mailOut);

        $mailOut = $mailParser->parse("<p>Foo\n  Bar\n  Bam</p>\n<p>Foooo</p>");
        $this->assertEquals("Foo Bar Bam\nFoooo\n", $mailOut);

        $mailOut = $mailParser->parse("<p>Foo\n  <p>Bar\n</p>\n  Bam</p>\n<p>Foooo</p>");
        $this->assertEquals("Foo\nBar\nBam\nFoooo\n", $mailOut);

        $mailOut = $mailParser->parse("<p>Foo\n  <p>Bar\n</p></p>\n<p>Foooo</p>");
        $this->assertEquals("Foo\nBar\nFoooo\n", $mailOut);
        return;
    }

    public function testLineLength()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);

        $mailOut = $mailParser->parse("<p>Das ist ein Testabsatz für die Länge! Das ist ein Testabsatz für die Länge! Das ist ein Testabsatz für die Länge! Das ist ein Testabsatz für die Länge!</p>");
        //break at max 72 chars
        $this->assertEquals("Das ist ein Testabsatz für die Länge! Das ist ein Testabsatz für die\n".
                            "Länge! Das ist ein Testabsatz für die Länge! Das ist ein Testabsatz für\n".
                            "die Länge!\n", $mailOut);
    }

    public function testHs()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);

        $mailOut = $mailParser->parse("<h1>Foo</h1><p>Bar</p>");
        $this->assertEquals("\nFoo\n\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("\n<h1>Foo</h1><p>Bar</p>");
        $this->assertEquals("\nFoo\n\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("<h1>Foo</h1>\n<p>Bar</p>");
        $this->assertEquals("\nFoo\n\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("<h2>Foo</h2>\n<p>Bar</p>");
        $this->assertEquals("\nFoo\n\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("<h3>Foo</h3>\n<p>Bar</p>");
        $this->assertEquals("\nFoo\n\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("<h4>Foo</h4>\n<p>Bar</p>");
        $this->assertEquals("\nFoo\n\nBar\n", $mailOut);

        $mailOut = $mailParser->parse("<h5>Foo</h5>\n<p>Bar</p>");
        $this->assertEquals("\nFoo\n\nBar\n", $mailOut);
    }

    public function testList()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);

        $mailOut = $mailParser->parse("<ul>\n  <li>Foo</li>\n  <li>Bar</li>\n</ul>\n");
        $this->assertEquals("* Foo\n* Bar\n", $mailOut);

        $mailOut = $mailParser->parse("<ul>\n  <li>Foo\n<li>Bar\n</li>\n</li>\n  <li>Bar</li>\n</ul>\n");
        $this->assertEquals("* Foo\n    * Bar\n* Bar\n", $mailOut);

        $mailOut = $mailParser->parse("<ul>\n  <li>Foo\n<li>Foo2\n<li>Foo3\n</li></li>\n</li>\n  <li>Bar</li>\n</ul>\n");
        $this->assertEquals("* Foo\n    * Foo2\n        * Foo3\n* Bar\n", $mailOut);
        return;
    }

    public function testCC() //no linebreak because the email url won't be valide anymore
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);
        $mailOut = $mailParser->parse("<kwc2 mail root-at_newsletter_14-mail-content-2647-text-l2YToxOntzOjQ6InR5cGUiO3M6MzoidHh0Ijt9>");
        $this->assertEquals("<kwc2 mail root-at_newsletter_14-mail-content-2647-text-l2YToxOntzOjQ6InR5cGUiO3M6MzoidHh0Ijt9>", $mailOut);
    }

    public function testLink()
    {
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);
        $mailOut = $mailParser->parse('<a href="<kwc2 mail root-at_newsletter_14-mail-content-2647-text-l1 YToxOntzOjQ6InR5cGUiO3M6MzoidHh0Ijt9>">Hier gehts zur VW Pkw Modellpalette</a>');
        $this->assertEquals("Hier gehts zur VW Pkw Modellpalette:\n<kwc2 mail root-at_newsletter_14-mail-content-2647-text-l1 YToxOntzOjQ6InR5cGUiO3M6MzoidHh0Ijt9>\n", $mailOut);
        $mailParser = new Kwc_Basic_Text_HtmlToTextParser(null);
        $mailOut = $mailParser->parse('  <a href="<kwc2 mail root-at_newsletter_14-mail-content-2647-text-l1 YToxOntzOjQ6InR5cGUiO3M6MzoidHh0Ijt9>">'.
            'Hier gehts zur VW Pkw Modellpalette</a><br />'.
            '<a href="<kwc2 mail root-at_newsletter_14-mail-content-2647-text-l2 YToxOntzOjQ6InR5cGUiO3M6MzoidHh0Ijt9>">Hier gehts zur VW '.
                'Nutzfahrzeuge Modellpalette</a>');
        $this->assertEquals("Hier gehts zur VW Pkw Modellpalette:\n<kwc2 mail root-at_newsletter_14-mail-content-2647-text-l1 YToxOntzOjQ6InR5cGUiO3M6MzoidHh0Ijt9>\n\nHier gehts zur VW Nutzfahrzeuge Modellpalette:\n<kwc2 mail root-at_newsletter_14-mail-content-2647-text-l2 YToxOntzOjQ6InR5cGUiO3M6MzoidHh0Ijt9>\n", $mailOut);
        return;
    }
}
