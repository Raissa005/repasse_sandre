<?php

namespace RR\components;

class ProgressComponent
{    
    function __construct()
    {
        $this->render();
    }

    private function render()
    {
?>
        <div class="progress progress-xs progress-striped active">
            <div class="progress-bar bg-success" style="width: 90%"></div>
        </div>
<?php
    }
}
