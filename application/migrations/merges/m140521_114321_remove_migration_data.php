<?php

    class m140521_114321_remove_migration_data extends BaseMerge
    {

        /**
         * Run Merge
         *
         * @access public
         * @return void
         */
        public function run()
        {
            $this->delete('{{events}}');
            $this->delete('{{auth_assignments}}');
            $this->delete('{{users}}');
            $this->delete('{{branches}}');
        }

    }
