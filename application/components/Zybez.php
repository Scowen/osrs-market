<?php

    namespace application\components;

    use \Yii;

    class Zybez
    {
        // The Zybez API returns a JSON decodeable format.
        private $zybezApi = "http://forums.zybez.net/runescape-2007-prices/api/";

        public function updateItem($string)
        {
            // Get the item from Zybez and decode.
            $json = file_get_contents($this->zybezApi . $string);
            $data = json_decode($json, true);
            if(!$data || !isset($data[0]))
                return false;
            $data = (object) $data[0];

            // First try and find the item given by the Zybez ID.
            $item = \application\models\db\Items::model()->findByAttributes(array(
                'zybez_id' => $data->id
            ));

            // If the item doesn't exist, create it.
            if(!$item)
                $item = new \application\models\db\Items;

            // Assign its attributes:
            $item->attributes = array(
                'name' => $data->name,
                'zybez_id' => $data->id,
                'zybez_search' => urlencode($string),
                'image' => $data->image,
                'average' => $data->average,
                'high' => $data->recent_high,
                'low' => $data->recent_low,
                'updated' => time(),
            );

            // If the item does not have a created timestamp, give it one.
            if(!$item->created)
                $item->created = time();

            // Finally, save the item.
            $item->save();

            // Next, add the record of item at this time to its history.
            $history = new \application\models\db\ItemHistory;

            // Assign its attributes:
            $history->attributes = array(
                'item' => $item->id,
                'offers' => count($data->offers),
                'quantity' => 1, // Change to the number bought throughout the history later.
                'average' => $item->average,
                'high' => $item->high,
                'low' => $item->low,
                'updated' => time(),
                'created' => time(),
            );

            // Annnnnnddddddd, save.
            $history->save();

            return $item;
        }

        public function updateAllItems()
        {
            // First get all the items currently in the DB.
            $items = \application\models\db\Items::model()->findAll();

            foreach($items as $item){
                $this->updateItem($item->zybez_search);
            }

            // Get all the items again for the return statement.
            $items = \application\models\db\Items::model()->findAll();

            return $items;
        }
    }