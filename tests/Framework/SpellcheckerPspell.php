<?php

/**
 * Test class to test rcube_spellcheck_pspell class
 *
 * @package Tests
 */
class Framework_SpellcheckerPspell extends PHPUnit\Framework\TestCase
{
    /**
     * Class constructor
     */
    function test_class()
    {
        $object = new rcube_spellchecker_pspell(null, 'en');

        $this->assertInstanceOf('rcube_spellchecker_pspell', $object, "Class constructor");
        $this->assertInstanceOf('rcube_spellchecker_engine', $object, "Class constructor");
    }

    /**
     * Test languages() method
     */
    function test_languages()
    {
        if (!extension_loaded('pspell')) {
            $this->markTestSkipped();
        }

        rcube::get_instance()->config->set('spellcheck_engine', 'pspell');

        $object = new rcube_spellchecker();

        $langs = $object->languages();

        $this->assertSame('English (US)', $langs['en']);
    }

    /**
     * Test check() method
     */
    function test_check()
    {
        if (!extension_loaded('pspell')) {
            $this->markTestSkipped();
        }

        rcube::get_instance()->config->set('spellcheck_engine', 'pspell');

        $object = new rcube_spellchecker();

        $this->assertTrue($object->check('one'));

        // Test other methods that depend on the spellcheck result
        $this->assertSame(0, $object->found());
        $this->assertSame([], $object->get_words());

        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?><spellresult charschecked="3"></spellresult>',
            $object->get_xml()
        );

        $this->assertFalse($object->check('ony'));

        // Test other methods that depend on the spellcheck result
        $this->assertSame(1, $object->found());
        $this->assertSame(['ony'], $object->get_words());

        $this->assertMatchesRegularExpression(
            '|^<\?xml version="1.0" encoding="UTF-8"\?><spellresult charschecked="3"><c o="0" l="3">([a-zA-Z\t]+)</c></spellresult>$|',
            $object->get_xml()
        );
    }

    /**
     * Test get_suggestions() method
     */
    function test_get_suggestions()
    {
        if (!extension_loaded('pspell')) {
            $this->markTestSkipped();
        }

        rcube::get_instance()->config->set('spellcheck_engine', 'pspell');

        $object = new rcube_spellchecker();

        $expected = ['ON','on','Ont','only','onya','NY','onyx','Ono','any','one'];
        $result   = $object->get_suggestions('ony');

        sort($expected);
        sort($result);

        $this->assertSame($expected, $result);
    }

    /**
     * Test get_words() method
     */
    function test_get_words()
    {
        if (!extension_loaded('pspell')) {
            $this->markTestSkipped();
        }

        rcube::get_instance()->config->set('spellcheck_engine', 'pspell');

        $object = new rcube_spellchecker();

        $this->assertSame(['ony'], $object->get_words('ony'));
    }
}
