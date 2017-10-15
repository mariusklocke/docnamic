# Docnamic - The document template engine

Docnamic is a template engine for OpenDocument files (*.odt) based on PHP's DOM and ZIP extensions.
My goal is to provide a simple document generation library whose templates can be easily created using standard WYSIWYG OpenDocument software.
I am interested if this library is useful for anybody out there and i will appreciate any feedback on Github or via email.

Please note that the public API should not be considered stable yet.

I will add tests once i am sure about the internal class interfaces.

## Requirements

* PHP 5.6 or later
* DOM extension
* ZIP extension

## Installation

Install this library via composer.

```bash
$ composer require mklocke/docnamic
```


## Usage

Have a look at the folder `examples` which contains examples of how the library should be used.

```php
$renderer = new Renderer();
$renderer->loadTemplate('template.odt')
         ->setData(['foo' => 'bar'])
         ->render('result.odt');
```

## Convert to PDF

The resulting OpenDocument files can be easily converted to PDF using the `unoconv` CLI tool on most linux distributions.

Installation on Ubuntu: `sudo apt-get install unoconv`

Convert a ODT file to PDF: `unoconv -f pdf -o result.pdf result.odt`


## FAQ

* Can i use barcodes or QR Codes?

   Yes, you can by using barcode fonts in your document template. OpenDocument files can have embedded fonts. If you use LibreOffice for creating your document templates, read their help pages regarding [Embedding Fonts](https://help.libreoffice.org/Common/Embedding_Fonts)

* Can i use nested loops in my document template?

   No, for now nested loops are not supported.
   
* Can i use dynamic images?

   No, dynamic images are not supported yet.
   
* What about encodings?

   As OpenDocument is a UTF-8-based XML file format, your data should be UTF-8 encoded as well ;)
   
* What about i18n or l10n?

   As this is a rather simple template engine, it has no notion of languages, countries or currencies. This has be taken care of the integrating application.
   
   
## Inspired by

* [Secretary](https://github.com/christopher-ramirez/secretary)
* [docxtemplater](https://github.com/open-xml-templating/docxtemplater)