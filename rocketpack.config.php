<?php
    rocketpack\Install::package('PluSQL',array(0,0,0));
    
    Dependencies::register(function()
    {
        rocketpack\Dependency::forPackage('PluSQL')
        ->add('Murphy',array(0,0,0))
        ->verify();
    });
