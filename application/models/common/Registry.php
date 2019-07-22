<?php

/**
 * Registry
 *
 * @author dev. Dmitry Kamyshov <dk@company.com>
 * @package Model
 * @subpackage common
 * @version 2.0.0
 */

namespace Models {

    class Registry extends \ArrayObject {

        /**
         * Название класса объекта хранилища синглтона
         * @var string
         */
        private static $_registryClassName = '\Models\Registry';

        /**
         * Хранилище объекта предоставляет хранилище для "расшаренных" объектов
         * @var Registry
         */
        private static $_registry = null;

        /**
         * Получает образец хранилища по умолчанию
         *
         * @return Registry
         */
        public static function getInstance() {
            if (self::$_registry === null) {
                self::init();
            }

            return self::$_registry;
        }

        /**
         * Инициализация образца хранилища по умолчанию
         *
         * @return void
         */
        protected static function init() {
            self::setInstance(new self::$_registryClassName());
        }

        /**
         * Устанавливаем образец хранилища по умолчанию для указанной образца
         *
         * @param Registry $registry Копия объекта типа Registry
         * @return void
         * @throws Exception если хранилище уже инициализировано
         */
        public static function setInstance(Registry $registry) {
            if (self::$_registry !== null) {
                throw new \Exception('Registry is already initialized');
            }

            self::setClassName(get_class($registry));
            self::$_registry = $registry;
        }

        /**
         * Устанавливает название класса для использования для образца хранилища по умолчанию
         *
         * @param string $registryClassName
         * @return void
         * @throws Exception если хранилице проинициализировано или если название класса не есть строка
         */
        public static function setClassName($registryClassName = 'Registry') {
            if (self::$_registry !== null) {
                throw new \Exception('Registry is already initialized');
            }

            if (!is_string($registryClassName)) {
                throw new \Exception("Argument is not a class name");
            }

            self::$_registryClassName = $registryClassName;
        }

        /**
         * Метод для извлечения объекта из хранилища
         *
         * @param string $index - получить значение, ассоциированное с $index
         * @return mixed
         * @throws Exception если вхождение $index не найдено
         */
        public static function get($index) {
            $instance = self::getInstance();

            if (!$instance->offsetExists($index)) {
                throw new \Exception("No entry is registered for key '$index'");
            }

            return $instance->offsetGet($index);
        }

        /**
         * Метод для помещения объекта в хранилище
         *
         *
         * @param string $index Расположение в  ArrayObject в котором хранить значение
         * @param mixed $value Объект, который собираемся хранить в ArrayObject.
         * @return void
         */
        public static function set($index, $value) {
            $instance = self::getInstance();
            $instance->offsetSet($index, $value);
        }

        /**
         * @param string $index
         * @return mixed
         *
         * Workaround for http://bugs.php.net/bug.php?id=40442 (ZF-960).
         */
        public function offsetExists($index) {
            return array_key_exists($index, $this);
        }

    }

}