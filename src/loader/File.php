<?php
namespace smpl\mydi\loader;

use smpl\mydi\LoaderInterface;

/**
 * Загрузка зависимостей на основе php файлов,
 * в случае если в имени контенейра указано _ то он трансформируется в DIRECTORY_SEPARATOR
 *
 * Class File
 * @package smpl\mydi\loader
 */
class File implements LoaderInterface
{
    private $context;

    private $basePath;

    public function __construct($basePath, array $context = [])
    {
        $this->basePath = realpath($basePath);
        $this->setContext($context);
    }

    /**
     * Проверяет может ли загрузить данный контейнер этот Loader
     * @param string $containerName
     * @throws \InvalidArgumentException если имя не строка
     * @return bool
     */
    public function isLoadable($containerName)
    {
        if (!is_string($containerName)) {
            throw new \InvalidArgumentException('Container name must be string');
        }
        $path = $this->containerNameToPath($containerName);
        $result = false;
        if (substr($path, 0, strlen($this->basePath)) === $this->basePath) {
            $result = is_readable($this->containerNameToPath($containerName));
        } else {
            $result = false;    // Пытаются загрузить что то за пределами указанной папки
        }
        return $result;
    }

    /**
     * Можно переопредилить для того чтобы использовать свою структуру поиска файлов в зависимости от имени контейнера
     * @param string $containerName
     * @return string
     */
    protected function containerNameToPath($containerName)
    {
        $result = str_replace('_', DIRECTORY_SEPARATOR, $containerName);
        return realpath($this->basePath . DIRECTORY_SEPARATOR . $result . '.php');
    }

    /**
     * Загрузка контейнера
     * @param string $containerName
     * @throws \InvalidArgumentException если имя нельзя загрузить
     * @throws \LogicException если у файла что подгружаем будет выводиться какой то текст
     * @return mixed
     */
    public function load($containerName)
    {
        if (!$this->isLoadable($containerName)) {
            throw new \InvalidArgumentException(sprintf('Container:`%s` must be loadable', $containerName));
        }
        ob_start();
        extract($this->context);
        $result = require $this->containerNameToPath($containerName);
        $output = ob_get_clean();
        if (!empty($output)) {
            throw new \LogicException(
                sprintf(
                    'Output in file: `%s` must be empty',
                    $this->containerNameToPath($containerName)
                )
            );
        }
        return $result;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }
}