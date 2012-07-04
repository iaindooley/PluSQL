<?php
    RocketPack\Install::package('https://github.com/iaindooley/PluSQL',array(0,2,1));
    
    RocketPack\Dependencies::register(function()
    {
        rocketpack\Dependency::forPackage('https://github.com/iaindooley/PluSQL')
        ->add('https://github.com/iaindooley/Murphy',array(0,2,0))
        ->verify();
    });
