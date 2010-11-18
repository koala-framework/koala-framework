<?php
/**
 * @group Helper
 * @group Helper_HighlightTerms
 */
class Vps_View_HighlightTermsTest extends PHPUnit_Framework_TestCase
{
    private $_text = '';

    public function setUp()
    {
        $this->_text = "Für. Dies ist ein Text für das Highlighten des Textes. \n"
            ."Es können auch Umlaute vorkommen, die gut für das Testen von utf8 sind.\n"
            ."Auch Zeilenumbrüche sind drin um zu sehen, ob trotzem alles funktioniert.\n"
            ."Ein längerer Aufsatz hierfür sollte es eigentlich nicht werden, allerdings "
            ."benötigen wir für das korrekte Testen schon etwas mehr als 200 Zeichen. "
            ."Aber ich denke mittlerweile haben wir genug für einen korrekten Test.\n"
            ." Fürwahr, das könnte gut Für mich sein. für";
    }

    public function testHighlighting()
    {
        $searchWord = 'für';
        $h = new Vps_View_Helper_HighlightTerms();
        $res = $h->highlightTerms($searchWord, $this->_text, array('maxReturnLength' => 0));

        $highlights = mb_substr_count($res, '<span ');
        $this->assertEquals(7, $highlights);

        $this->assertRegExp('/<span class="highlightTerms highlightTerm1">für<\/span>/', $res);
        $this->assertRegExp('/<span class="highlightTerms highlightTerm1">Für<\/span>/', $res);
        $this->assertNotRegExp('/hier<span class="highlightTerms highlightTerm1">für<\/span>/', $res);
        $this->assertNotRegExp('/<span class="highlightTerms highlightTerm1">Für<\/span>wahr/', $res);

        $res = $h->highlightTerms(array('Highlighten', ' '), $this->_text, array('maxReturnLength' => 0));
        $highlights = mb_substr_count($res, '<span ');
        $this->assertEquals(1, $highlights);
    }

    public function testShorting()
    {
        $searchWord = 'für';
        $h = new Vps_View_Helper_HighlightTerms();
        $res = $h->highlightTerms($searchWord, $this->_text, array('maxReturnLength' => 200, 'maxReturnBlocks' => 4));

        $highlights = mb_substr_count($res, '<span ');
        $this->assertEquals(4, $highlights);

        $highlights = mb_substr_count($res, ' ... ');
        $this->assertEquals(3, $highlights);

        $this->assertLessThanOrEqual(200, mb_strlen(strip_tags($res)));
    }

    public function testLongTextShorting()
    {
        $text = '
Der schnellste Weg zu einem intensiveren Leben.

Sind Sie bereit für ein Wettrennen mit Ihrem Puls? Ihr Gegner: der neue Golf GTI. Ihre Aufgabe: einsteigen, anlassen, Gas geben. Spätestens dann werden Sie spüren: Hier schlagen zwei Herzen im absoluten Gleichklang. Für ein Leben mit mehr Intensität.
Der schnellste Weg zu mehr Sportlichkeit

Rasant und offensiv. Das ist der neue Golf GTI mit tiefergelegtem Sportfahrwerk und 17-Zoll-Leichtmetallrädern im Design "Denver". Typisches Markenzeichen des GTI ist sein spezielles Kühlerschutzgitter mit schwarz lackierter Wabenstruktur, eingerahmt von zwei roten Leisten. Die Signalfarbe Rot findet sich auch an den Bremssätteln mit 16-Zoll-Scheibenbremsen wieder. Neben der schwarz genarbten Schwellerverbreiterung und den zwei Abgas-Endrohren links und rechts vom schwarzen Diffusor überzeugt das Exterieur mit einem Dachkantenspoiler und exklusiven Stoßfängern in Wagenfarbe von seiner Sportlichkeit.
Der schnellste Weg zu mehr Temperament.

Er ist das Herz des neuen Golf GTI. Der 2,0l Turbomotor schlägt mit rasanten 155 kW (210 PS) und beschleunigt von null auf 100 km in 6,9 Sekunden - bei einer Höchstgeschwindigkeit von bis zu 240 km/h.
(Kraftstoffverbrauch: 7,3 - 7,4 l/100 km; CO2-Emission: 170 - 173 g/km). Schalten wäre da nur hinderlich: Das übernimmt auf Wunsch das 6-Gang-Doppelkupplungsgetriebe DSG für Sie. Wer so sportlich unterwegs ist, sollte sich nicht um seine Sicherheit sorgen müssen. Dank Elektronischem Stabilisierungsprogramm (ESP) inkl. Komfortbremsassistent, Lenkimpuls, ABS, EDS, ASR und XDS werden Sie bei jeder Fahrt optimal unterstützt.
Der schnellste Weg zu mehr Markanz.

Der markante Look des neuen Golf GTI beginnt schon im Cockpit mit dem unten abgeflachten Sport-Lederlenkrad mit GTI-Spange und mit Schriftzug. Immer sportlich, immer premium zeigt sich auch das übrige Interieur: ob Schalthebelgriff oder die Pedale in Alu-Optik, die Dekoreinlagen "Black Stripe" oder die verchromten Einfassungen und Applikationen wie z.B. an den Ausströmern. Während der Dachhimmel und die Säulenverkleidung in Schwarz gehalten sind, wird an Lederlenkrad, Handbremshebelgriff und Schalthebelmanschette die Signalfarbe Rot in Form von Ziernähten wieder aufgenommen.
Der schnellste Weg zu mehr Komfort.

Besonders komfortabel lässt sich die Fahrt im neuen Golf GTI auf den Top-Sportsitzen vorn mit integrierten Kopf- und Lendenwirbelstützen genießen. Sie sind zudem höheneinstell- und beheizbar und verfügen über praktische Sitzlehnentaschen. Aber auch optisch machen sie schwer was her: dank des charakteristischen Karositzbezugs "Jacky". Für noch mehr Komfort sorgen insgesamt vier Leseleuchten und elektrischen Fensterhebr an allen Türen. Den perfekten Überblick bietet die Multifunktionsanzeige "Plus".
Der schnellste Weg zu mehr Freiheit.

Im neuen Golf GTI können Sie sich die Freiheit nehmen, die Sie brauchen - egal, ob es sich um Ihre Wohlfühltemperatur, Ihre Lieblingsmusik oder eine spontane Erfrischung handelt. Das garantieren Ihnen die Klimaanlage "Climatronic" mit 2-Zonen-Temperaturregelung, das Radio "RCD 310" mit 8 Lautsprechern und Multimediabuchse AUX-IN und das Handschuhfach mit Kühlmöglichkeit. Damit Sie sich auch bei jeder Wetterlage frei fühlen können, ist der neue Golf GTI mit chromeingefassten Nebelscheinwerfern ausgerüstet.
';
        $searchWords = array('golf', 'gti');
        $h = new Vps_View_Helper_HighlightTerms();
        $res = $h->highlightTerms($searchWords, $text);
        $resStripped = strip_tags($res);

        $this->assertLessThanOrEqual(350, mb_strlen($resStripped));

        $expectedString = ' ... Wettrennen mit Ihrem Puls? Ihr Gegner: der neue Golf GTI. '
            .'Ihre Aufgabe: einsteigen, anlassen, Gas geben. ... Rasant und offensiv. Das '
            .'ist der neue Golf GTI mit tiefergelegtem Sportfahrwerk und ... "Denver". '
            .'Typisches Markenzeichen des GTI ist sein spezielles Kühlerschutzgitter mit '
            .'schwarz ... ';
        $this->assertEquals($expectedString, $resStripped);
    }

    public function testNoMatch()
    {
        $searchWord = 'blubbel';
        $h = new Vps_View_Helper_HighlightTerms();
        $res = $h->highlightTerms($searchWord, $this->_text, array('maxReturnLength' => 50, 'maxReturnBlocks' => 4));

        $this->assertEquals(50, mb_strlen($res));
        $this->assertEquals('Für. Dies ist ein Text für das Highlighten des Tex', $res);
    }
}
