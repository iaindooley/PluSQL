<?php
    rocketpack\Install::package('PluSQL',array(0,1,0));
    
    rocketpack\Dependencies::register(function()
    {
        rocketpack\Dependency::forPackage('PluSQL')
        ->add('Murphy',array(0,1,0))
        ->verify();
    });
