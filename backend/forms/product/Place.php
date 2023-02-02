<?php

namespace backend\forms\product;

use common\models\goods\Place as PlaceModel;

use backend\components\form\Form;
use backend\components\form\controls\Select;
use backend\components\form\controls\Selectize;
use common\libs\form\components\Option;
use common\models\Location;

/**
 * Class Place
 * @package backend\forms\product
 * @author yerganat
 */
class Place extends Form {

    /** @var PlaceModel. */
    private $places_data = [];

    protected function initControls(): void {
        parent::initControls();

        $this->setControlsTemplateEnv(static::MODE_USE_BACKEND_CONTROLS_TEMPLATE)
            ->needRenderFormTags(false);

        $place_id = isset($this->places_data[0])?$this->places_data[0]->id:0;
        $country_id = isset($this->places_data[0])?$this->places_data[0]->country_id:0;
        $region_id = isset($this->places_data[0])?$this->places_data[0]->region_id:0;
        $city_id = isset($this->places_data[0])?$this->places_data[0]->city_id:0;

        $country_component = (new Selectize())
            ->setName('place['.$place_id.'][country_id]')
            ->setValue($country_id)
        ;
        $this->populateCountries($country_component);
        $this->registerControl($country_component);

        $region_component = (new Selectize())
            ->addClass('place_region_select')
            ->setName('place['.$place_id.'][region_id]')
            ->setValue($region_id)
        ;
        $this->populateRegions($region_component);
        $this->registerControl($region_component);

        $city_component = (new Selectize())
            ->addClass('place_city_select')
            ->setName('place['.$place_id.'][city_id]')
            ->setValue($city_id)
        ;
        $this->populateCities($city_component, $region_id);
        $this->registerControl($city_component);
    }



    /**
     * @param Select $select
     * @return $this
     */
    private function populateCountries(Select $select) {

        $country_map = (new Location())->getPossibleCountries();
        $country_map = array_flip($country_map);

        foreach ($country_map as $country_id => $name) {
            $select->addOption(new Option($country_id, $name));
        }
        return $this;
    }

    /**
     * @param Select $select
     * @return Select
     */
    private function populateRegions(Select $select) {

        $region_map = (new Location())->getPossibleRegions();
        $region_map = array_flip($region_map);

        foreach ($region_map as $region_id => $name) {
            $select->addOption(new Option($region_id, $name));
        }
        return $select;
    }

    /**
     * @param Select $select
     * @param int $region_id
     * @return Select
     */
    private function populateCities(Select $select, $region_id) {
        $locations = Location::find();
        if(!empty($region_id)) {
            $locations = $locations->where(['region' => $region_id]);
        }
        $locations = $locations->all();

        $select->addOption(new Option(0, 'Не выбран'));
        foreach ($locations as $location) {
            $select->addOption(new Option($location->id, $location->title));
        }
        return $select;
    }

    /**
     * Устанавливает данные мест реализации.
     * @param array $data
     * @return $this
     */
    public function setPlacesData(array $data) {
        $this->places_data = $data;
        return $this;
    }
}