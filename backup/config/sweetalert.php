<?php

return [
    // Other configs...
    
    'custom_class' => [
        'popup' => '',
        'header' => '',
        'title' => '',
        'content' => '',
        'actions' => '',
        'confirmButton' => 'bg-purple',
        'cancelButton' => '',
        'footer' => '',
    ],

    'theme' => 'default',
    
    // Add custom styles
    'customStyles' => '
        .swal2-styled.swal2-confirm {
            background-color: #6c5ce7 !important;
        }
        .swal2-popup {
            background-color: white !important;
        }
    ',
];