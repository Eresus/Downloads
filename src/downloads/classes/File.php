<?php
/**
 * Файл, отправляемый браузеру
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
 */

/**
 * Файл, отправляемый браузеру
 *
 * @package Downloads
 * @api
 */
class Downloads_File extends SplFileObject
{
    /**
     * Имя файла, под которым он будет отдан браузеру
     *
     * @var string|null
     */
    private $filename = null;

    /**
     * Тип контента
     * @var string
     */
    private $contentType = 'application/octet-stream';

    /**
     * Кодировка
     * @var string|null
     */
    private $charset = null;

    /**
     * @param string $filename
     */
    public function __construct($filename = null)
    {
        if (null === $filename)
        {
            $filename = 'php://temp';
        }
        parent::__construct($filename, 'r+');
    }

    /**
     * Задаёт содержимое файла
     *
     * @param string $contents
     */
    public function setContents($contents)
    {
        $this->ftruncate(0);
        $this->rewind();
        $this->fwrite($contents);
    }

    /**
     * Задаёт имя файла, под которым он будет отдан браузеру
     *
     * @param string $name
     */
    public function setFilename($name)
    {
        $this->filename = basename($name);
    }

    /**
     * Задаёт тип контета (без указания кодировки)
     *
     * Для указания кодировки используйте {@link setCharset()}
     *
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Задаёт кодировку файла
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Отправляет файл браузеру
     *
     * @return void
     */
    public function send()
    {
        $this->sendHeader('Content-Description', 'File Transfer');
        $contentType = $this->contentType;
        if (null !== $this->charset)
        {
            $contentType .= '; charset=' . $this->charset;
        }
        $this->sendHeader('Content-Type', $contentType);
        $filename = null === $this->filename ? $this->getFilename() : $this->filename;
        $this->sendHeader('Content-Disposition', 'attachment; filename=' . $filename);
        $this->sendHeader('Content-Transfer-Encoding', 'binary');
        $this->sendHeader('Expires', '0');
        $this->sendHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $this->sendHeader('Pragma', 'public');
        $this->sendHeader('Content-Length', $this->getSize());
        $this->rewind();
        while (!$this->eof())
        {
            echo $this->fgets();
        }
    }

    /**
     * Возвращает размер файла
     *
     * @return int
     */
    public function getSize()
    {
        if ('php://temp' == $this->getPathname())
        {
            $saveOffset = $this->ftell();
            $this->fseek(0, SEEK_END);
            $size = $this->ftell();
            $this->fseek($saveOffset, SEEK_SET);
            return $size;
        }
        else
        {
            return parent::getSize();
        }
    }

    /**
     * Отправляет браузеру заголовок
     *
     * @param string $header  имя заголовка
     * @param string $value   значение
     */
    protected function sendHeader($header, $value)
    {
        header("$header: $value");
    }
}

