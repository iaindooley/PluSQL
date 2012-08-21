<?php
    RocketPack\Install::package('https://github.com/iaindooley/PluSQL',array(0,0,0));
    
    RocketPack\Dependencies::register(function()
    {
        RocketPack\Dependency::forPackage('https://github.com/iaindooley/PluSQL')
        ->add('https://github.com/iaindooley/Murphy',array(0,2,2))
        ->verify();
    });
