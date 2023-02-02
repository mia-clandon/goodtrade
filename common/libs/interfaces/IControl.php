<?php

namespace common\libs\interfaces;

/**
 * Интерфейс контрола формы.
 * Interface IComponent
 * @package common\libs\interfaces
 * @author Артём Широких kowapssupport@gmail.com
 */
interface IControl {

    /**
     * Атрибут name контрола формы.
     * @param string $name
     * @return mixed
     */
    public function setName(string $name);

    /**
     * Название контрола (Атрибут name)
     * @return string
     */
    public function getName(): string;

    /**
     * Рендеринг контрола
     * @return string
     */
    public function render(): string;
}