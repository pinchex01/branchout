<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary'  => env("WKPDF_BIN",'"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary'  => env("WKIMAGE_BIN",'"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),


);
