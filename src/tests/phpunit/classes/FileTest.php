<?php
/**
 * Модульные тесты
 *
 * @version ${product.version}
 *
 * @copyright 2013, Михаил Красильников, <m.krasilnikov@yandex.ru>
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author Михаил Красильников, <m.krasilnikov@yandex.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package Downloads
 * @subpackage Tests
 */


require_once __DIR__ . '/../bootstrap.php';
require_once TESTS_SRC_DIR . '/downloads/classes/File.php';

/**
 * @package Downloads
 * @subpackage Tests
 */
class Downloads_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Downloads_File::__construct
     */
    public function testConstruct()
    {
        $file = new Downloads_File();
        $file->fwrite('foo');
        $file->rewind();
        $this->assertEquals('foo', $file->fgets());
    }

    /**
     * @covers Downloads_File::setContents
     */
    public function testSetContents()
    {
        $file = new Downloads_File();
        $file->fwrite('foo');
        $file->setContents('bar');
        $file->rewind();
        $this->assertEquals('bar', $file->fgets());
    }

    /**
     * @covers Downloads_File::send
     * @covers Downloads_File::setContentType
     * @covers Downloads_File::setCharset
     * @covers Downloads_File::getSize
     */
    public function testSend()
    {
        $file = $this->getMock('Downloads_File', array('sendHeader'));
        $file->expects($this->any())->method('sendHeader')->will($this->returnCallback(
            function ($header, $value)
            {
                echo "$header: $value\n";
            }
        ));
        /** @var Downloads_File $file */
        $file->setContents('foo');
        $file->setContentType('text/plain');
        $file->setCharset('utf-8');
        $this->expectOutputString(
            "Content-Description: File Transfer\n" .
            "Content-Type: text/plain; charset=utf-8\n" .
            "Content-Disposition: attachment; filename=temp\n" .
            "Content-Transfer-Encoding: binary\n" .
            "Expires: 0\n" .
            "Cache-Control: must-revalidate, post-check=0, pre-check=0\n" .
            "Pragma: public\n" .
            "Content-Length: 3\n" .
            "foo");
        $file->send();
    }

    /**
     * @covers Downloads_File::setFilename
     */
    public function testSetFilename()
    {
        $file = $this->getMock('Downloads_File', array('sendHeader'));
        $file->expects($this->any())->method('sendHeader')->will($this->returnCallback(
            function ($header, $value)
            {
                echo "$header: $value\n";
            }
        ));
        /** @var Downloads_File $file */
        $file->setFilename('foo.bar');
        $this->expectOutputString(
            "Content-Description: File Transfer\n" .
            "Content-Type: application/octet-stream\n" .
            "Content-Disposition: attachment; filename=foo.bar\n" .
            "Content-Transfer-Encoding: binary\n" .
            "Expires: 0\n" .
            "Cache-Control: must-revalidate, post-check=0, pre-check=0\n" .
            "Pragma: public\n" .
            "Content-Length: 0\n");
        $file->send();
    }
}

