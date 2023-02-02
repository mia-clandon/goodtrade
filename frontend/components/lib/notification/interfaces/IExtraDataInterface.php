<?

namespace frontend\components\lib\notification\interfaces;

/**
 * Interface IExtraDataInterface
 * Классы реализующие интерфейс, хранят в себе дополнительные данные "extra_data" уведомления.
 * @package frontend\components\lib\notification\interfaces
 * @author Артём Широких kowapssupport@gmail.com
 */
interface IExtraDataInterface {

    /** К какому типу уведомлений относится extra_data объект. */
    public function getRelationNotificationType();

    /**
     * Получения заголовка уведомления.
     * @return string
     */
    public function getTitle();

    /**
     * Получение описания уведомления.
     * @return string
     */
    public function getText();

    /**
     * Установка JSON для пополнения ExtraData объекта.
     * @param string $json
     * @return static
     */
    public function setData($json);

    /**
     * Получение JSON данных extra_data объекта.
     * @return string
     */
    public function getData();

}