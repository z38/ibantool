IbanTool
========

[![Build Status](https://travis-ci.org/z38/ibantool.svg?branch=master)](https://travis-ci.org/z38/ibantool)

**IbanTool** is a small wrapper around the [IBAN-Tool][1] by SIX Interbank Clearing. It can be used to calculate the IBANs of Swiss and Liechtenstein bank account numbers for master data conversions.

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Z38\IbanTool\IbanTool;

$tool = new IbanTool([
    'ibantool_jar' => __DIR__.'/ibantool_java.jar' // download JAR from the official website
]);

$tool->convert('80-151-4', '3525-8.888766.2'); // returns 

$tool->convertPostal('80-470-3'); // returns 
```

Further Resources
-----------------

 * [Official website for IBAN-Tool][1]

Disclaimer
----------

This project is not affiliated with, endorsed, or sponsored by SIX Interbank Clearing AG.

License
-------

BSD-3-Clause

[1]: http://www.six-interbank-clearing.com/en/home/standardization/iban/iban-tool.html
