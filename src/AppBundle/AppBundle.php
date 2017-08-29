<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{

    // Get parent FOSUserbundle
    public function getParent()
    {
        return 'FOSUserBundle';
    }

}