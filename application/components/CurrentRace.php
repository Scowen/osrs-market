<?php

    namespace application\components;

    class CurrentRace
    {
        public static function get(){
            $currentRace = null;
            foreach(\application\models\db\Races::model()->findAll() as $race){
                if(!$currentRace){
                    if(!$race->winner){
                        return $race;
                    } elseif(time() > $race->start && time() < $race->end){
                        return $race;
                    }
                }
            }

            return null;
        }
    }